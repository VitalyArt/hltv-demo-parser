<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser\Enums;

enum MacroTypeEnum: int
{
    case GAME_DATA_START  = 0;
    case GAME_DATA_NORMAL = 1;
    case UNUSED           = 2;
    case CLIENT_COMMAND   = 3;
    case STRING           = 4;
    case LAST_IN_SEGMENT  = 5;
    case UNKNOWN_6        = 6;
    case UNKNOWN_7        = 7;
    case PLAY_SOUND       = 8;
    case DELTA_DATA       = 9;
}
