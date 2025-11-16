<?php

namespace Syndicate\Promoter\Filament\Resources\SeoDataResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Syndicate\Promoter\Filament\PromoterPlugin;

class ListSeoData extends ListRecords
{
    public static function getResource(): string
    {
        return PromoterPlugin::get()->getSeoDataResource();
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('keyword')
                    ->label('Keyword')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('generated_keyword')
                    ->label('Generated Keyword'),
                Tables\Columns\TextColumn::make('keyword_score')
                    ->label('Score')
                    ->numeric(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }
}
