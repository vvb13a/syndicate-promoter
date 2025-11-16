<?php

namespace Syndicate\Promoter\Filament\Resources\SeoDataResource\Pages;

use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Syndicate\Assistant\Enums\FilamentPageType;
use Syndicate\Assistant\Filament\Tables\Columns\UpdatedAtColumn;
use Syndicate\Carpenter\Filament\Tables\Columns\MorphModelColumn;
use Syndicate\Carpenter\Filament\Tables\Filters\NullFilter;
use Syndicate\Promoter\Filament\Resources\SeoDataResource;
use Syndicate\Promoter\Filament\SeoPlugin;

class ListSeoData extends ListRecords
{
    public static function getResource(): string
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('syndicate-promoter-seo')) {
            /** @var SeoPlugin $plugin */
            $plugin = $currentPanel->getPlugin('syndicate-promoter-seo');
        }

        return $plugin?->getSeoDataResource() ?? SeoDataResource::class;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                MorphModelColumn::make('seoable.id')
                    ->link(FilamentPageType::EditSeo),
                Tables\Columns\TextColumn::make('keyword')
                    ->label('Keyword')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('generated_keyword')
                    ->label('Generated Keyword'),
                Tables\Columns\TextColumn::make('keyword_score')
                    ->label('Score')
                    ->numeric(),
                UpdatedAtColumn::make(),
            ])
            ->filters([
                NullFilter::make('keyword')
                    ->default()
            ])
            ->actions([
                // Simple list-only view; no actions needed for now.
            ])
            ->bulkActions([
                // No bulk actions
            ])
            ->defaultSort('created_at', 'desc')
            ->paginationPageOptions([25, 50, 100]);
    }

    protected function getDispatchModelArr(): array
    {
        $seoableTypes = $this->getSeoableTypes();

        if (empty($seoableTypes)) {
            return [];
        }

        return collect($seoableTypes)->mapWithKeys(function ($value, $key) {
            return [$value => class_basename($value)];
        })->toArray();
    }

    protected function getSeoableTypes(): array
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('syndicate-promoter-seo')) {
            $plugin = $currentPanel->getPlugin('syndicate-promoter-seo');
        }

        /** @var ?SeoPlugin $plugin */
        return $plugin?->getSeoableTypes() ?? [];
    }
}
