<?php

namespace Syndicate\Promoter\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SeoData extends Model
{
    protected $table = 'seo_data';

    protected $fillable = [
        'title',
        'description',
        'image_alt',
        'keywords',
        'keyword',
        'generated_keyword',
        'keyword_score',
        'canonical_url',
        'robots',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    protected function title(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function sanitizeStringAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmedValue = trim($value);

        return $trimmedValue === '' ? null : $trimmedValue;
    }

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function imageAlt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function keywords(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function keyword(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function generatedKeyword(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function canonicalUrl(): Attribute
    {
        return Attribute::make(
            fn($value) => $this->sanitizeStringAttribute($value),
        );
    }

    protected function robots(): Attribute
    {
        return Attribute::make(
            fn($value) => $this->sanitizeStringAttribute($value),
        );
    }
}
