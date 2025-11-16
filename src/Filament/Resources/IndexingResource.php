<?php

namespace Syndicate\Promoter\Filament\Resources;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Syndicate\Promoter\Filament\Relations\IndexingRelationManager;
use Syndicate\Promoter\Filament\Resources\IndexingResource\Pages\ListIndexing;
use Syndicate\Promoter\Filament\SeoPlugin;
use Syndicate\Promoter\Models\Indexing;

class IndexingResource extends Resource
{
    protected static ?string $model = Indexing::class;

    protected static ?string $navigationLabel = 'Indexing';
    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = 'SEO';

    public static function getRelations(): array
    {
        return [
            IndexingRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIndexing::route('/'),
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

        return parent::getEloquentQuery()->whereIn('subject_type', $aliases);
    }
}
