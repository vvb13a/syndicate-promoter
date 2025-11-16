<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

trait HasKeywords
{
    /**
     * Configuration: Is the Keywords field required?
     */
    protected bool $isSeoKeywordsRequired = false;

    /**
     * Returns the Section component containing the Keywords field.
     * Override this method to customize the section structure or return null to disable.
     */
    protected function getSeoKeywordsSection(): ?Component
    {
        return Section::make('Keywords')
            ->aside()
            ->description('Define keywords relevant to the content. Note: Meta keywords are largely ignored by major search engines like Google but might be used by others.')
            ->schema([
                $this->getSeoKeywordsField(),
            ]);
    }

    /**
     * Returns the TextInput component for the Keywords.
     * Override this method to customize the field itself.
     */
    protected function getSeoKeywordsField(): Component
    {
        // Always return TextInput now
        return TextInput::make('keywords')
            ->label('Meta Keywords')
            ->helperText('This is a largely outdated seo attribute but populates the keywords meta tag.')
            ->required($this->isSeoKeywordsRequired)
            ->placeholder($this->getRecord()->seo->getKeywords());
    }
}
