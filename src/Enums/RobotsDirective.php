<?php

namespace Syndicate\Promoter\Enums;

/**
 * Represents common combined values for the Robots Meta Tag directive.
 * Allows for type-safe setting and checking of standard directives.
 */
enum RobotsDirective: string
{
    case INDEX_FOLLOW = 'index,follow';
    case NOINDEX_FOLLOW = 'noindex,follow';
    case INDEX_NOFOLLOW = 'index,nofollow';
    case NOINDEX_NOFOLLOW = 'noindex,nofollow';

    public static function default(): self
    {
        return self::INDEX_FOLLOW;
    }

    public function allowsIndex(): bool
    {
        return match ($this) {
            self::INDEX_FOLLOW, self::INDEX_NOFOLLOW => true,
            self::NOINDEX_FOLLOW, self::NOINDEX_NOFOLLOW => false,
        };
    }

    public function allowsFollow(): bool
    {
        return match ($this) {
            self::INDEX_FOLLOW, self::NOINDEX_FOLLOW => true,
            self::INDEX_NOFOLLOW, self::NOINDEX_NOFOLLOW => false,
        };
    }
}
