<?php

namespace Syndicate\Promoter\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Syndicate\Promoter\Enums\IndexingStatus;
use Syndicate\Promoter\Filament\Resources\IndexingResource\Pages\ListIndexing;

class IndexingStatusStats extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected int|string|array $columnSpan = 'full';

    protected function getTablePage(): string
    {
        return ListIndexing::class;
    }

    protected function getStats(): array
    {
        $statusCounts = $this->getPageTableQuery()
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');
        $total = $statusCounts->sum();
        $stats = [
            Stat::make('Total', $total)
                ->description('Total')
                ->color('gray')
        ];

        $buckets = [
            [
                'color' => 'success',
                'cases' => [IndexingStatus::Indexed],
                'label' => 'Indexed',
                'description' => 'Indexed and discoverable via google search'
            ],
            [
                'color' => 'danger',
                'cases' => [
                    IndexingStatus::Discovered,
                    IndexingStatus::Excluded,
                    IndexingStatus::Removed,
                    IndexingStatus::Error,
                ],
                'label' => 'Not Indexed',
                'description' => 'Not indexed or discoverable via google search'
            ],
            [
                'color' => 'info',
                'cases' => [
                    IndexingStatus::Unknown,
                    IndexingStatus::Submitted,
                    IndexingStatus::Removal_Requested
                ],
                'label' => 'Pending',
                'description' => 'Pending indexing or removal'
            ]
        ];

        foreach ($buckets as $bucket) {
            $figure = 0;

            foreach ($bucket['cases'] as $indexStatus) {
                $figure += $statusCounts->get($indexStatus->value, 0);
            }

            $percentage = $total === 0 ? $total : round(($figure / $total) * 100);

            $stats[] = Stat::make($percentage.'%', $figure)
                ->extraAttributes([
                    'x-tooltip.raw' => $bucket['description'],
                ])
                ->description($bucket['label'])
                ->color($bucket['color']);
        }

        return $stats;
    }
}
