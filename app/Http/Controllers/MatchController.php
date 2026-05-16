<?php

namespace App\Http\Controllers;

use App\Models\PickleMatch;
use App\Models\Player;
use App\Models\RatingHistory;
use App\Services\PairingService;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MatchController extends Controller
{
    public function __construct(
        private PairingService $pairing,
        private RatingService  $rating,
    ) {}

    public function matchesView()
    {
        $players = Player::orderBy('name')->get();
        return view('matches', compact('players'));
    }

    public function leaderboardView()
    {
        return view('leaderboard');
    }

    /** POST /pairings — generate pairings and persist as pending matches */
    public function generatePairings(Request $request)
    {
        $request->validate([
            'players' => 'required|array|min:4',
            'players.*' => 'exists:players,id',
            'mode' => 'in:casual,competitive,ladder',
        ]);

        $mode    = $request->input('mode', 'casual');
        $players = Player::whereIn('id', $request->players)->get();
        $pairs   = $this->pairing->generate($players, $mode);

        $created = [];
        foreach ($pairs as $pair) {
            $match = PickleMatch::create([
                'mode'      => $mode,
                'team_a_p1' => $pair['team_a'][0]->id,
                'team_a_p2' => $pair['team_a'][1]->id,
                'team_b_p1' => $pair['team_b'][0]->id,
                'team_b_p2' => $pair['team_b'][1]->id,
            ]);
            $created[] = $this->formatMatch($match->load(['playerA1','playerA2','playerB1','playerB2']));
        }

        return response()->json($created);
    }

    /** POST /matches/{match}/result — submit score and apply ratings */
    public function submitResult(Request $request, PickleMatch $match)
    {
        $request->validate([
            'score_a' => 'required|integer|min:0|max:30',
            'score_b' => 'required|integer|min:0|max:30',
        ]);

        if ($match->ratings_applied) {
            return response()->json(['error' => 'Ratings already applied for this match.'], 422);
        }

        $scoreA = (int) $request->score_a;
        $scoreB = (int) $request->score_b;

        if ($scoreA === $scoreB) {
            return response()->json(['error' => 'Scores cannot be tied.'], 422);
        }

        $winner = $scoreA > $scoreB ? 'A' : 'B';

        DB::transaction(function () use ($match, $scoreA, $scoreB, $winner) {
            $match->update(['score_a' => $scoreA, 'score_b' => $scoreB, 'winner' => $winner]);

            $p1 = Player::find($match->team_a_p1);
            $p2 = Player::find($match->team_a_p2);
            $p3 = Player::find($match->team_b_p1);
            $p4 = Player::find($match->team_b_p2);

            $teamARating = $this->rating->teamRating($p1->rating, $p2->rating);
            $teamBRating = $this->rating->teamRating($p3->rating, $p4->rating);

            $deltas = $this->rating->calculateDeltas(
                $teamARating, $teamBRating, $winner,
                $scoreA, $scoreB, $match->mode
            );

            $this->applyDelta($p1, $match, $deltas['deltaA'], $winner === 'A');
            $this->applyDelta($p2, $match, $deltas['deltaA'], $winner === 'A');
            $this->applyDelta($p3, $match, $deltas['deltaB'], $winner === 'B');
            $this->applyDelta($p4, $match, $deltas['deltaB'], $winner === 'B');

            $match->update(['ratings_applied' => true]);
        });

        return response()->json($this->formatMatch($match->fresh()->load(['playerA1','playerA2','playerB1','playerB2'])));
    }

    /** GET /leaderboard */
    public function leaderboard()
    {
        $players = Player::orderByDesc('rating')
            ->orderByDesc('wins')
            ->get()
            ->map(fn($p, $i) => [
                'rank'           => $i + 1,
                'id'             => $p->id,
                'name'           => $p->name,
                'rating'         => (float) $p->rating,
                'tier'           => RatingService::tier((float) $p->rating),
                'wins'           => $p->wins,
                'losses'         => $p->losses,
                'matches_played' => $p->matches_played,
                'win_rate'       => $p->matches_played > 0
                    ? round($p->wins / $p->matches_played * 100, 1)
                    : 0,
            ]);

        return response()->json($players);
    }

    /** GET /players/{player}/history */
    public function ratingHistory(Player $player)
    {
        $history = $player->ratingHistory()
            ->with('match:id,mode,score_a,score_b,winner,created_at')
            ->limit(20)
            ->get()
            ->map(fn($h) => [
                'match_id'     => $h->match_id,
                'mode'         => $h->match->mode ?? null,
                'score'        => $h->match ? "{$h->match->score_a}–{$h->match->score_b}" : null,
                'won'          => $h->match ? $this->playerWon($player, $h->match) : null,
                'rating_before'=> (float) $h->rating_before,
                'rating_after' => (float) $h->rating_after,
                'delta'        => (float) $h->delta,
                'date'         => $h->created_at->toDateTimeString(),
            ]);

        return response()->json([
            'player'  => ['id' => $player->id, 'name' => $player->name, 'rating' => (float) $player->rating],
            'history' => $history,
        ]);
    }

    /** GET /api/matches — list matches as JSON */
    public function index()
    {
        $matches = PickleMatch::with(['playerA1','playerA2','playerB1','playerB2'])
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->map(fn($m) => $this->formatMatch($m));

        return response()->json($matches);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function applyDelta(Player $player, PickleMatch $match, float $delta, bool $won): void
    {
        $before = (float) $player->rating;
        $after  = $this->rating->clamp($before + $delta);

        RatingHistory::create([
            'player_id'     => $player->id,
            'match_id'      => $match->id,
            'rating_before' => $before,
            'rating_after'  => $after,
            'delta'         => round($after - $before, 2),
        ]);

        $player->update([
            'rating'         => $after,
            'wins'           => $player->wins + ($won ? 1 : 0),
            'losses'         => $player->losses + ($won ? 0 : 1),
            'matches_played' => $player->matches_played + 1,
        ]);
    }

    private function playerWon(Player $player, PickleMatch $match): bool
    {
        $onTeamA = in_array($player->id, [$match->team_a_p1, $match->team_a_p2]);
        return ($onTeamA && $match->winner === 'A') || (!$onTeamA && $match->winner === 'B');
    }

    private function formatMatch(PickleMatch $m): array
    {
        return [
            'id'              => $m->id,
            'mode'            => $m->mode,
            'team_a'          => [
                ['id' => $m->playerA1->id, 'name' => $m->playerA1->name, 'rating' => (float)$m->playerA1->rating],
                ['id' => $m->playerA2->id, 'name' => $m->playerA2->name, 'rating' => (float)$m->playerA2->rating],
            ],
            'team_b'          => [
                ['id' => $m->playerB1->id, 'name' => $m->playerB1->name, 'rating' => (float)$m->playerB1->rating],
                ['id' => $m->playerB2->id, 'name' => $m->playerB2->name, 'rating' => (float)$m->playerB2->rating],
            ],
            'score_a'         => $m->score_a,
            'score_b'         => $m->score_b,
            'winner'          => $m->winner,
            'ratings_applied' => (bool) $m->ratings_applied,
            'created_at'      => $m->created_at?->toDateTimeString(),
        ];
    }
}
