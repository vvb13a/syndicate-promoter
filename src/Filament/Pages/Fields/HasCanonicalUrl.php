<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

trait HasCanonicalUrl
{
    /**
     * Configuration: Is the Canonical URL field required?
     * Typically false, as it often defaults to the page's own URL if empty.
     */
    protected bool $isSeoCanonicalUrlRequired = false;

    /**
     * Returns the Section component containing the Canonical URL field.
     * Override this method to customize the section structure or return null to disable.
     */
    protected function getSeoCanonicalUrlSection(): ?Component
    {
        return Section::make('Canonical URL')
            ->aside()
            ->description('Specify the preferred ("canonical") URL for this content, especially if it exists on multiple URLs or has variations (e.g., with/without www, HTTP/HTTPS), to prevent duplicate content issues.')
            ->schema([
                $this->getSeoCanonicalUrlField(),
            ]);
    }

    /**
     * Returns the TextInput component for the Canonical URL.
     * Override this method to customize the field itself.
     */
    protected function getSeoCanonicalUrlField(): Component
    {
        return TextInput::make('canonical_url')
            ->label('Canonical URL')
            ->helperText('Leave empty to use the page\'s default URL. Ensures search engines know the primary source.')
            ->url()
            ->required($this->isSeoCanonicalUrlRequired)
            ->placeholder($this->getRecord()->seo->getCanonicalUrl());
    }
}
