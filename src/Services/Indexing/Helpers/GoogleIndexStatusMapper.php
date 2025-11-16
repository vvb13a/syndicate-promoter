<?php

namespace Syndicate\Promoter\Services\Indexing\Helpers;

use InvalidArgumentException;
use Syndicate\Promoter\Enums\IndexingStatus;

/**
 * Maps Google Search Console API responses to the internal IndexStatus enum.
 */
final class GoogleIndexStatusMapper
{
    /**
     * Determines the IndexStatus from a Google URL Inspection API response.
     *
     * @param  string  $verdict  The high-level verdict (e.g., 'PASS', 'FAIL', 'NEUTRAL').
     * @param  string|null  $coverageState  The detailed reason for the verdict.
     * @return IndexingStatus The mapped status.
     *
     * @throws InvalidArgumentException If the verdict is unknown or cannot be mapped.
     */
    public static function fromGoogleResponse(string $verdict, ?string $coverageState): IndexingStatus
    {
        // First, check for the most definitive success case.
        if ($verdict === 'PASS') {
            return IndexingStatus::Indexed;
        }

        // Handle specific "removed" states first, as they can have a 'FAIL' verdict.
        if ($coverageState && str_contains(strtolower($coverageState), 'removed')) {
            return IndexingStatus::Removed;
        }

        return match ($verdict) {
            'NEUTRAL' => self::mapFromNeutralVerdict($coverageState),
            'FAIL' => self::mapFromFailVerdict($coverageState),

            // If the verdict is unspecified or a new value we don't recognize,
            // we should fail loudly rather than guess a status.
            default => throw new InvalidArgumentException("Unknown or unmappable Google API verdict: '{$verdict}'"),
        };
    }

    /**
     * Handles the nuanced logic for a 'NEUTRAL' verdict.
     */
    private static function mapFromNeutralVerdict(?string $coverageState): IndexingStatus
    {
        // 'Discovered' is a key intermediate state that we want to represent specifically.
        if ($coverageState === 'Discovered - currently not indexed') {
            return IndexingStatus::Discovered;
        }

        // Most other 'NEUTRAL' states indicate an issue preventing indexing.
        // 'EXCLUDED' is the most appropriate status for these cases.
        return IndexingStatus::Excluded;
    }

    /**
     * Handles the logic for a 'FAIL' verdict.
     */
    private static function mapFromFailVerdict(?string $coverageState): IndexingStatus
    {
        // A 'FAIL' verdict unambiguously means the page is not indexed for a specific reason.
        // 'EXCLUDED' is the correct status for all these scenarios.
        // Examples of $coverageState for FAIL:
        // - "Crawled - currently not indexed"
        // - "Page with redirect"
        // - "Duplicate without user-selected canonical"
        // - "Blocked by robots.txt"
        // - "Not found (404)"
        // - "Soft 404"
        return IndexingStatus::Excluded;
    }
}
