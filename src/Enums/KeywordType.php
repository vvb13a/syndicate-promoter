<?php

namespace Syndicate\Promoter\Enums;

/**
 * Represents the SEO template tail length type.
 */
enum KeywordType: string
{
    case Long = 'long';
    case Mid = 'mid';
    case Short = 'short';

    public static function default(): self
    {
        return self::Mid;
    }
}
