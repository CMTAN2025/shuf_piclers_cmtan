<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PairingService
{
    /**
     * Generate doubles pairings from a collection of players.
     * Returns array of [teamA => [p1,p2], teamB => [p1,p2]] match pairs.
     *
     * Modes:
     *   casual      — random shuffle, then pair sequentially
     *   competitive — sort by rating, pair closest ratings; balanced teams
     *   ladder      — top players rotate together (top half vs bottom half)
     */
    public function generate(Collection $players, string $mode = 'casual'): array
    {
        if ($players->count() < 4) {
            return [];
        }

        $players = match ($mode) {
            'competitive' => $players->sortBy('rating')->values(),
            'ladder'      => $players->sortByDesc('rating')->values(),
            default       => $players->shuffle()->values(),
        };

        return match ($mode) {
            'competitive' => $this->competitivePairs($players),
            'ladder'      => $this->ladderPairs($players),
            default       => $this->casualPairs($players),
        };
    }

    /** Random pairs: chunk into groups of 4, split each group into two teams */
    private function casualPairs(Collection $players): array
    {
        $groups = $players->chunk(4);
        $matches = [];

        foreach ($groups as $group) {
            $group = $group->values();
            if ($group->count() < 4) continue;
            $matches[] = [
                'team_a' => [$group[0], $group[1]],
                'team_b' => [$group[2], $group[3]],
            ];
        }

        return $matches;
    }

    /**
     * Competitive: sort by rating, pair 1st+4th vs 2nd+3rd within each group of 4
     * so team ratings are as balanced as possible.
     */
    private function competitivePairs(Collection $players): array
    {
        $groups = $players->chunk(4);
        $matches = [];

        foreach ($groups as $group) {
            $group = $group->values();
            if ($group->count() < 4) continue;
            // [0]=highest, [3]=lowest → team A: 0+3, team B: 1+2 (balanced avg)
            $matches[] = [
                'team_a' => [$group[0], $group[3]],
                'team_b' => [$group[1], $group[2]],
            ];
        }

        return $matches;
    }

    /**
     * Ladder: top half vs bottom half.
     * Top 2 players of each group of 4 form one team, bottom 2 the other.
     */
    private function ladderPairs(Collection $players): array
    {
        $groups = $players->chunk(4);
        $matches = [];

        foreach ($groups as $group) {
            $group = $group->values();
            if ($group->count() < 4) continue;
            $matches[] = [
                'team_a' => [$group[0], $group[1]], // top 2
                'team_b' => [$group[2], $group[3]], // bottom 2
            ];
        }

        return $matches;
    }
}
