<?php

namespace App\Scoring;

use App\Entity\Ascent;
use App\Entity\Boulder;
use App\Struct\BoulderStruct;

interface ScoringInterface
{
    public const ASCENT_TYPES = [
        Ascent::ASCENT_FLASH,
        Ascent::ASCENT_TOP,
        Ascent::ASCENT_RESIGNED
    ];

    public const SCORED_ASCENT_TYPES = [
        Ascent::ASCENT_FLASH,
        Ascent::ASCENT_TOP,
    ];

    /**
     * @param BoulderStruct[] $boulders
     * @return array
     */
    public function calculate(array $boulders): array;

    public function calculateScore(Boulder $boulder): void;

    public function getIdentifier(): string;
}
