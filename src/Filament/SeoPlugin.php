<?php

namespace Syndicate\Promoter\Filament;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Resources\Resource;
use Syndicate\Promoter\Filament\Resources\IndexingResource;
use Syndicate\Promoter\Filament\Resources\SeoDataResource;
use Syndicate\Promoter\Filament\Widgets\IndexingStatusStats;

class SeoPlugin implements Plugin
{
    /**
     * @var array<int, class-string>
     */
    protected array $seoableTypes = [];
    protected string $seoDataResource = SeoDataResource::class;
    protected string $indexingResource = IndexingResource::class;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'syndicate-promoter-seo';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->widgets([
                IndexingStatusStats::class,
            ])
            ->resources([
                $this->getSeoDataResource(),
                $this->getIndexingResource(),
            ]);
    }

    public function getSeoDataResource(): string
    {
        return $this->seoDataResource;
    }

    public function getIndexingResource(): string
    {
        return $this->indexingResource;
    }

    public function getSeoableTypes(): array
    {
        return $this->seoableTypes;
    }

    public function seoableTypes(array $seoableTypes): static
    {
        $this->seoableTypes = $seoableTypes;
        return $this;
    }

    /**
     * @param  class-string<Resource>  $resourceClass
     * @return $this
     */
    public function indexingResource(string $resourceClass): static
    {
        $this->indexingResource = $resourceClass;
        return $this;
    }

    public function boot(Panel $panel): void
    {
        // no-op
    }

    /**
     * @param  class-string<Resource>  $resourceClass
     * @return $this
     */
    public function seoDataResource(string $resourceClass): static
    {
        $this->seoDataResource = $resourceClass;
        return $this;
    }
}
