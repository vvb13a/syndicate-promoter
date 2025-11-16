<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;

trait HasDescription
{
    /**
     * Configuration: Minimum recommended length for SEO Description.
     */
    protected int $seoDescriptionMinLength = 50;

    /**
     * Configuration: Maximum recommended length for SEO Description.
     */
    protected int $seoDescriptionMaxLength = 160;

    /**
     * Configuration: Is the SEO Description field required?
     */
    protected bool $isSeoDescriptionRequired = true;

    /**
     * Configuration: Enforce min/max length validation rules based on SEO recommendations?
     */
    protected bool $isSeoDescriptionLengthEnforced = false;

    /**
     * Returns the Section component containing the SEO Description field.
     * Override this method to customize the entire description section structure.
     * Return null to disable the section entirely.
     */
    protected function getSeoDescriptionSection(): ?Component
    {
        return Section::make('Description')
            ->aside()
            ->description('Appears below the title in search results. A compelling description can improve click-through rate.')
            ->schema([
                $this->getSeoDescriptionField(),
            ]);
    }

    /**
     * Returns the Textarea component for the SEO Description.
     * Override this method to customize the description field itself.
     */
    protected function getSeoDescriptionField(): Component
    {
        return Textarea::make('description')
            ->label('Description')
            ->helperText('Summarizes the page content. Aim for 50-160 characters.')
            ->required($this->isSeoDescriptionRequired)
            ->minLength($this->isSeoDescriptionLengthEnforced ? $this->seoDescriptionMinLength : null)
            ->maxLength($this->isSeoDescriptionLengthEnforced ? $this->seoDescriptionMaxLength : null)
            ->autosize()
            ->hint(view('syndicate::assistant.fields.length-indicator')
                ->with([
                    'minLength' => $this->seoDescriptionMinLength,
                    'maxLength' => $this->seoDescriptionMaxLength,
                    'attr' => 'data.seoData.description',
                ]))
            ->placeholder($this->getRecord()->seo->getDescription());
    }
}
