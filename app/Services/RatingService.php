<?php

namespace App\Services;

class RatingService
{
    const MIN_RATING = 2.00;
    const MAX_RATING = 8.00;
    const DEFAULT_RATING = 3.50;

    /**
     * DUPR-style divisor: tighter than chess Elo (400) so a 1-point
     * rating gap already represents a very strong favourite.
     */
    const SCALE = 175;

    /**
     * Base K per mode. Kept small so ratings move gradually like real DUPR.
     * casual: slightly more volatile (fun games, less data weight)
     * competitive / ladder: more stable (results carry more meaning)
     */
    const K_FACTORS = [
        'casual'      => 0.08,
        'competitive' => 0.06,
        'ladder'      => 0.07,
    ];

    /**
     * Expected win probability for teamA given average ratings.
     * Standard logistic curve, DUPR-scale divisor.
     */
    public function expectedScore(float $ratingA, float $ratingB): float
    {
        return 1 / (1 + pow(10, ($ratingB - $ratingA) / self::SCALE));
    }

    /**
     * Opponent strength multiplier.
     * Beating a much stronger team gives a bigger boost;
     * losing to a much weaker team gives a bigger penalty.
     *
     * Range: 0.5 (huge upset loss / easy win) → 2.0 (huge upset win)
     */
    public function opponentMultiplier(float $winnerRating, float $loserRating): float
    {
        $diff = $loserRating - $winnerRating; // positive = upset win
        return round(min(2.0, max(0.5, 1.0 + ($diff / self::SCALE))), 4);
    }

    /**
     * Score margin multiplier — closer games move rating less.
     * 11-0 blowout ≈ 1.3×, 11-9 close game ≈ 0.85×
     */
    public function marginMultiplier(int $winnerScore, int $loserScore): float
    {
        $total = max(1, $winnerScore + $loserScore);
        $diff  = max(0, $winnerScore - $loserScore);
        return round(min(1.3, max(0.7, 0.7 + ($diff / $total))), 4);
    }

    /**
     * Calculate DUPR-style rating deltas for a doubles match.
     *
     * Formula:
     *   delta = K × (actual − expected) × opponentMultiplier × marginMultiplier
     *
     * Returns ['deltaA' => float, 'deltaB' => float]
     */
    public function calculateDeltas(
        float $ratingA,
        float $ratingB,
        string $winner,
        int $scoreA,
        int $scoreB,
        string $mode = 'casual'
    ): array {
        $k = self::K_FACTORS[$mode] ?? self::K_FACTORS['casual'];

        $expectedA = $this->expectedScore($ratingA, $ratingB);
        $actualA   = $winner === 'A' ? 1.0 : 0.0;
        $actualB   = 1.0 - $actualA;

        [$winnerRating, $loserRating] = $winner === 'A'
            ? [$ratingA, $ratingB]
            : [$ratingB, $ratingA];

        [$winScore, $loseScore] = $winner === 'A'
            ? [$scoreA, $scoreB]
            : [$scoreB, $scoreA];

        $oppMult    = $this->opponentMultiplier($winnerRating, $loserRating);
        $marginMult = $this->marginMultiplier($winScore, $loseScore);

        $deltaA = round($k * ($actualA - $expectedA) * $oppMult * $marginMult, 2);
        $deltaB = round($k * ($actualB - (1 - $expectedA)) * $oppMult * $marginMult, 2);

        return ['deltaA' => $deltaA, 'deltaB' => $deltaB];
    }

    public function clamp(float $rating): float
    {
        return round(max(self::MIN_RATING, min(self::MAX_RATING, $rating)), 2);
    }

    public function teamRating(float $r1, float $r2): float
    {
        return ($r1 + $r2) / 2;
    }

    /** Human-readable tier label for a given rating */
    public static function tier(float $rating): string
    {
        return match (true) {
            $rating >= 7.0 => 'Pro',
            $rating >= 5.0 => 'Elite',
            $rating >= 4.0 => 'Advanced',
            $rating >= 3.0 => 'Intermediate',
            default        => 'Beginner',
        };
    }
}
