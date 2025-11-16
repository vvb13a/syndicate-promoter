<?php

namespace Syndicate\Promoter\Filament\Resources;

use Filament\Resources\Resource;
use Syndicate\Promoter\Filament\Resources\SeoDataResource\Pages\ListSeoData;
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
}
