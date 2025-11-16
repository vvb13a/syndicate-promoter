<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;

trait HasImageAlt
{
    /**
     * Configuration: Minimum recommended length for Image Alt.
     */
    protected int $seoImageAltMinLength = 50;

    /**
     * Configuration: Maximum recommended length for Image Alt.
     */
    protected int $seoImageAltMaxLength = 100;

    /**
     * Configuration: Is the Image Alt field required?
     */
    protected bool $isSeoImageAltRequired = false;

    /**
     * Configuration: Enforce min/max length validation rules based on SEO recommendations?
     */
    protected bool $isSeoImageAltLengthEnforced = false;

    /**
     * Returns the Section component containing the SEO Image alt field.
     * Override this method to customize the entire image alt section structure.
     * Return null to disable the section entirely.
     */
    protected function getSeoImageAltSection(): ?Component
    {
        return Section::make('Img Alt Text')
            ->aside()
            ->description('Define alternative text for the main image. This improves accessibility and SEO.')
            ->schema([
                $this->getSeoImageAltField(),
            ]);
    }

    /**
     * Returns the Textarea component for the SEO Image Alt.
     * Override this method to customize the image alt field itself.
     */
    protected function getSeoImageAltField(): Component
    {
        return Textarea::make('image_alt')
            ->label('Image Alt Text')
            ->helperText('Describe the content of the main image in a short, meaningful sentence.')
            ->required($this->isSeoImageAltRequired)
            ->minLength($this->isSeoImageAltLengthEnforced ? $this->seoImageAltMinLength : null)
            ->maxLength($this->isSeoImageAltLengthEnforced ? $this->seoImageAltMaxLength : null)
            ->autosize()
            ->hint(function () {
                return view('syndicate::assistant.fields.length-indicator')
                    ->with([
                        'minLength' => $this->seoImageAltMinLength,
                        'maxLength' => $this->seoImageAltMaxLength,
                        'attr' => 'data.seoData.image_alt',
                    ]);
            })
            ->placeholder($this->getRecord()->alt);
    }
}
