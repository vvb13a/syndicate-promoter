<?php

namespace Syndicate\Promoter\Filament\Resources\IndexingResource\Pages;

use Filament\Facades\Filament;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Syndicate\Assistant\Enums\FilamentPageType;
use Syndicate\Assistant\Filament\Tables\Columns\CreatedAtColumn;
use Syndicate\Carpenter\Filament\Tables\Columns\MorphModelColumn;
use Syndicate\Promoter\Enums\IndexingService;
use Syndicate\Promoter\Enums\IndexingStatus;
use Syndicate\Promoter\Filament\Filters\SubjectFilter;
use Syndicate\Promoter\Filament\Resources\IndexingResource;
use Syndicate\Promoter\Filament\SeoPlugin;
use Syndicate\Promoter\Filament\Widgets\IndexingStatusStats;

class ListIndexing extends ListRecords
{
    use ExposesTableToWidgets;

    public static function getResource(): string
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('syndicate-promoter-seo')) {
            /** @var SeoPlugin $plugin */
            $plugin = $currentPanel->getPlugin('syndicate-promoter-seo');
        }

        return $plugin?->getIndexingResource() ?? IndexingResource::class;
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereIn('id', function ($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('syndicate_indexing_entries')
                        ->groupBy('subject_type', 'subject_id');
                });
            })
            ->columns([
                MorphModelColumn::make('subject.id')
                    ->link(FilamentPageType::EditSeo),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('service')
                    ->badge(),
                TextColumn::make('trigger')
                    ->badge(),
                CreatedAtColumn::make(),
            ])
            ->filters([
                SubjectFilter::make(),
                SelectFilter::make('status')
                    ->options(IndexingStatus::class),
                SelectFilter::make('service')
                    ->options(IndexingService::class)
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

    protected function getHeaderWidgets(): array
    {
        return [
            IndexingStatusStats::class
        ];
    }
}
