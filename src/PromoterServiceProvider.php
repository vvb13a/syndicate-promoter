<?php

namespace Syndicate\Promoter;

use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Syndicate\Promoter\Listeners\FlushSitemapOnActivation;
use Syndicate\Promoter\Services\IndexingService;
use Syndicate\Promoter\Services\PromoterService;
use Syndicate\Promoter\Services\SitemapService;

class PromoterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('syndicate-promoter')
            ->hasViews()
            ->hasMigration('create_seo_data_table');
    }

    public function registeringPackage()
    {
        $this->app->singleton(PromoterService::class);
        $this->app->singleton(SitemapService::class);
    }
}
