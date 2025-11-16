<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Syndicate\Promoter\Enums\RobotsDirective;

trait HasRobots
{
    /**
     * Configuration: Is the Robots directive field required?
     * Usually true, as a default should always be set.
     */
    protected bool $isSeoRobotsRequired = true;

    /**
     * Returns the Section component containing the Robots directive field.
     * Override this method to customize the section structure or return null to disable.
     */
    protected function getSeoRobotsSection(): ?Component
    {
        return Section::make('Search Engine Indexing')
            ->aside()
            ->description('Control how search engine crawlers index this page and follow links from it. Choose the appropriate directive based on your content strategy.')
            ->schema([
                $this->getSeoRobotsField(),
            ]);
    }

    /**
     * Returns the Select component for the Robots directive.
     * Override this method to customize the field itself.
     */
    protected function getSeoRobotsField(): Component
    {
        return Select::make('robots')
            ->label('Robots Directive')
            ->options(RobotsDirective::class)
            ->required($this->isSeoRobotsRequired)
            ->helperText('Select the appropriate rule for search engine bots.')
            ->selectablePlaceholder(false);
    }
}
