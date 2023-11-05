<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser\Enums;

enum EntryTypeEnum: string
{
    case LOADING  = 'loading';
    case PLAYBACK = 'playback';
}
