<?php

namespace Syndicate\Promoter\Filament\Filters;

use Filament\Facades\Filament;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Relations\Relation;
use Syndicate\Promoter\Filament\SeoPlugin;

class SubjectFilter extends SelectFilter
{
    public static function make(?string $name = 'subject_type'): static
    {
        return parent::make($name);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->options($this->getTypes())
            ->label('Subject');
    }

    protected function getTypes(): array
    {
        $currentPanel = Filament::getCurrentPanel();
        $plugin = null;

        if ($currentPanel && $currentPanel->hasPlugin('syndicate-promoter-seo')) {
            $plugin = $currentPanel->getPlugin('syndicate-promoter-seo');
        }

        /** @var ?SeoPlugin $plugin */
        $types = $plugin?->getSeoableTypes() ?? [];

        if (empty($types)) {
            return [];
        }

        return collect($types)->mapWithKeys(function ($value, $key) {
            return [Relation::getMorphAlias($value) => class_basename($value)];
        })->toArray();
    }
}
