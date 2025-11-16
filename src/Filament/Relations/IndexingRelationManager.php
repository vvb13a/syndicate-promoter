<?php

namespace Syndicate\Promoter\Filament\Relations;

use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Syndicate\Assistant\Filament\Tables\Columns\CreatedAtColumn;
use Syndicate\Carpenter\Filament\Infolists\JsonEntry;

class IndexingRelationManager extends RelationManager
{
    protected static string $relationship = 'indexingTimeline';
    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->inverseRelationship('subject')
            ->columns([
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('service')
                    ->badge(),
                TextColumn::make('trigger')
                    ->badge(),
                CreatedAtColumn::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction(ViewAction::class)
            ->paginationPageOptions(['10'])
            ->actions([
                ViewAction::make()
                    ->infolist([
                        TextEntry::make('status')
                            ->badge(),
                        JsonEntry::make('details')
                    ]),
                DeleteAction::make()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
