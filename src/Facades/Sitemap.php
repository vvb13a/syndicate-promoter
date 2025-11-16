<?php

namespace Syndicate\Promoter\Facades;

use Illuminate\Support\Facades\Facade;
use Syndicate\Promoter\Services\SitemapService;

/**
 * @mixin SitemapService
 */
class Sitemap extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SitemapService::class;
    }
}
