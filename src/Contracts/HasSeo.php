<?php

namespace Syndicate\Promoter\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Syndicate\Promoter\Services\SeoService;
use Syndicate\Promoter\Sitemaps\ModelSitemap;

interface HasSeo
{
    public static function sitemap(): ModelSitemap;

    public function seoData(): MorphOne;

    public function getSeoAttribute(): SeoService;

    public function scopeWithSeoDescription($query): void;
}
