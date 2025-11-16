<?php

namespace Syndicate\Promoter\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Syndicate\Promoter\Filament\Resources\SeoDataResource\Pages\ListSeoData;
use Syndicate\Promoter\Filament\SeoPlugin;
use Syndicate\Promoter\Models\SeoData;

class SeoDataResource extends Resource
{
    protected static ?string $model = SeoData::class;

    protected static ?string $navigationLabel = 'SEO Data';
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationGroup = 'SEO';

    public static function getPages(): array
    {
        return [
            'index' => ListSeoData::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('syndicate-promoter-seo')) {
            $plugin = $currentPanel->getPlugin('syndicate-promoter-seo');
        }

        /** @var ?SeoPlugin $plugin */
        $seoableTypes = $plugin?->getSeoableTypes() ?? [];

        if (empty($seoableTypes)) {
            return parent::getEloquentQuery();
        }

        $aliases = collect($seoableTypes)->map(function ($value) {
            return Relation::getMorphAlias($value);
        })->toArray();

        return parent::getEloquentQuery()->whereIn('seoable_type', $aliases);
    }
}
