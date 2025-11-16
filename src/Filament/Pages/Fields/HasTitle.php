<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;

trait HasTitle
{
    /**
     * Configuration: Minimum recommended length for SEO Title.
     */
    protected int $seoTitleMinLength = 20;

    /**
     * Configuration: Maximum recommended length for SEO Title.
     */
    protected int $seoTitleMaxLength = 60;

    /**
     * Configuration: Is the SEO Title field required?
     */
    protected bool $isSeoTitleRequired = true;

    /**
     * Configuration: Is the SEO Title field required?
     */
    protected bool $isSeoTitleLengthEnforced = false;

    /**
     * Returns the Section component containing the SEO Title field.
     * Override this method to customize the entire title section structure.
     * Return null to disable the section entirely.
     */
    protected function getSeoTitleSection(): ?Component
    {
        return Section::make('Title')
            ->aside()
            ->description('Essential for search engine visibility. Appears as the main clickable link in search results.')
            ->schema([
                $this->getSeoTitleField(),
            ]);
    }

    /**
     * Returns the TextInput component for the SEO Title.
     * Override this method to customize the title field itself.
     */
    protected function getSeoTitleField(): Component
    {
        return TextInput::make('title')
            ->label('Title')
            ->helperText('Keep it concise and relevant. Aim for 10-60 characters.')
            ->required($this->isSeoTitleRequired)
            ->minLength($this->isSeoTitleLengthEnforced ? $this->seoTitleMinLength : null)
            ->maxLength($this->isSeoTitleLengthEnforced ? $this->seoTitleMaxLength : 255)
            ->hint(
                view('syndicate::assistant.fields.length-indicator')
                    ->with([
                        'minLength' => $this->seoTitleMinLength,
                        'maxLength' => $this->seoTitleMaxLength,
                        'attr' => 'data.seoData.title',
                    ])
            )
            ->placeholder($this->getRecord()->seo->getTitle());
    }
}
