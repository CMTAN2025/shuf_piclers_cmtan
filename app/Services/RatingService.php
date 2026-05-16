<?php

namespace App\Services;

class RatingService
{
    // K-factor: casual = 32, competitive = 20, ladder = 24
    const K_FACTORS = [
        'casual'      => 32,
        'competitive' => 20,
        'ladder'      => 24,
    ];

    const MIN_RATING = 1.00;
    const MAX_RATING = 7.00;

    public function expectedScore(float $playerRating, float $opponentRating): float
    {
        return 1 / (1 + pow(10, ($opponentRating - $playerRating) / 400));
    }

    /**
     * Score differential multiplier — bigger margin = bigger change.
     * Log curve so a blowout isn't infinitely punishing.
     */
    public function marginMultiplier(int $winnerScore, int $loserScore): float
    {
        $diff = max(1, $winnerScore - $loserScore);
        return min(1.5, log($diff + 1) / log(12));
    }

    /**
     * Calculate Elo deltas for a doubles match.
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

        [$winScore, $loseScore] = $winner === 'A'
            ? [$scoreA, $scoreB]
            : [$scoreB, $scoreA];

        $margin = $this->marginMultiplier($winScore, $loseScore);

        return [
            'deltaA' => round($k * ($actualA - $expectedA) * $margin, 2),
            'deltaB' => round($k * ($actualB - (1 - $expectedA)) * $margin, 2),
        ];
    }

    public function clamp(float $rating): float
    {
        return round(max(self::MIN_RATING, min(self::MAX_RATING, $rating)), 2);
    }

    public function teamRating(float $r1, float $r2): float
    {
        return ($r1 + $r2) / 2;
    }
}
