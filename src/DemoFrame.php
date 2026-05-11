<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use VitalyArt\DemoParser\Enums\MacroTypeEnum;

readonly class DemoFrame
{
    public function __construct(
        private MacroTypeEnum $type,
        private float $time,
        private int $frame,
        private int $payloadLength,
    )
    {
    }

    public function getType(): MacroTypeEnum
    {
        return $this->type;
    }

    public function getTime(): float
    {
        return $this->time;
    }

    public function getFrame(): int
    {
        return $this->frame;
    }

    public function getPayloadLength(): int
    {
        return $this->payloadLength;
    }
}
