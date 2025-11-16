<?php

namespace Syndicate\Promoter\Filament;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Resources\Resource;
use Syndicate\Promoter\Filament\Resources\SeoDataResource;

class PromoterPlugin implements Plugin
{
    private static string $pluginId = 'syndicate-promoter';
    protected string $seoDataResource = SeoDataResource::class;

    public static function get(): self
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin(self::$pluginId)) {
            /** @var PromoterPlugin $plugin */
            $plugin = $currentPanel->getPlugin(self::$pluginId);
        }

        return $plugin ?? static::make();
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return self::$pluginId;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources([
                $this->getSeoDataResource(),
            ]);
    }

    public function getSeoDataResource(): string
    {
        return $this->seoDataResource;
    }

    public function boot(Panel $panel): void
    {
        // no-op
    }

    /**
     * @param class-string<Resource> $resourceClass
     * @return $this
     */
    public function seoDataResource(string $resourceClass): static
    {
        $this->seoDataResource = $resourceClass;
        return $this;
    }
}
