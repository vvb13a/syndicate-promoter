<?php

namespace Syndicate\Promoter\DTOs;

use Carbon\Carbon;
use JsonSerializable;

/**
 * DTO for News Sitemap Metadata
 */
class NewsMetadataDto implements JsonSerializable
{
    public function __construct(
        public readonly string $publicationName,
        public readonly string $publicationLanguage,
        public readonly Carbon $publicationDate,
        public readonly string $title
    ) {}

    /**
     * Create NewsMetadataDto from array (for backward compatibility)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            publicationName: $data['publication_name'] ?? config('app.name'),
            publicationLanguage: $data['publication_language'] ?? $data['language'] ?? app()->getLocale(),
            publicationDate: $data['publication_date'] instanceof Carbon
                ? $data['publication_date']
                : Carbon::parse($data['publication_date']),
            title: $data['title']
        );
    }

    /**
     * Convert to array for XML generation (backward compatibility)
     */
    public function toArray(): array
    {
        return [
            'publication_name' => $this->publicationName,
            'publication_language' => $this->publicationLanguage,
            'publication_date' => $this->publicationDate->toAtomString(),
            'title' => $this->title,
        ];
    }

    /**
     * Get publication date as atom string for XML
     */
    public function getPublicationDateString(): string
    {
        return $this->publicationDate->toAtomString();
    }

    /**
     * Validate that all required fields are present and valid
     */
    public function isValid(): bool
    {
        return !empty($this->publicationName) &&
               !empty($this->publicationLanguage) &&
               !empty($this->title) &&
               $this->publicationDate instanceof Carbon;
    }

    /**
     * JsonSerializable implementation
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
