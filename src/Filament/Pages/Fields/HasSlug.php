<?php

namespace Syndicate\Promoter\Filament\Pages\Fields;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Syndicate\Linguist\Contracts\HasLanguage;

trait HasSlug
{
    /**
     * Configuration: The database field name for the slug.
     */
    protected string $slugFieldName = 'slug';

    /**
     * Configuration: Minimum recommended length for the slug.
     */
    protected int $slugMinLength = 10;

    /**
     * Configuration: Maximum recommended length for the slug.
     */
    protected int $slugMaxLength = 60;

    /**
     * Configuration: Is the slug field required?
     */
    protected bool $isSlugRequired = true;

    /**
     * Configuration: Is the slug field length enforced?
     */
    protected bool $isSlugLengthEnforced = false;

    /**
     * Returns the Section component containing the Slug field.
     *
     * Override this method to customize the section structure or return null to disable.
     */
    protected function getSlugSection(): ?Component
    {
        return Section::make('URL Slug')
            ->aside()
            ->description('This defines the unique web address segment for this record. It should be descriptive and SEO-friendly.')
            ->schema([
                $this->getSlugField(),
            ]);
    }

    /**
     * Returns the TextInput component for the Slug.
     * Override this method to customize the field itself.
     */
    protected function getSlugField(): Component
    {
        return TextInput::make($this->slugFieldName)
            ->label('URL Slug')
            ->helperText('The unique identifying part of the URL. Use lowercase letters, numbers, and hyphens.')
            ->lazy()
            ->afterStateUpdated(function ($state, Set $set, Model $record) {
                if ($record instanceof HasLanguage) {
                    $set('slug', str($state)->slug(language: $record->getLanguage()->value));
                } else {
                    $set('slug', str($state)->slug());
                }
            })
            ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, $record) {
                if ($record instanceof HasLanguage) {
                    $rule->where($record->getLanguageColumn(), $record->getLanguage()->value);
                }
                return $rule;
            })
            ->minLength($this->isSlugLengthEnforced ? $this->slugMinLength : null)
            ->maxLength($this->isSlugLengthEnforced ? $this->slugMaxLength : null)
            ->hint(
                view('syndicate::assistant.fields.length-indicator')
                    ->with([
                        'minLength' => $this->slugMinLength,
                        'maxLength' => $this->slugMaxLength,
                        'attr' => 'data.slug',
                    ])
            )
            ->required($this->isSlugRequired)
            ->prefix(
                str($this->getRecord()->seo->getCanonicalUrl())
                    ->replaceEnd('/', '')
                    ->beforeLast('/')
                    ->append('/')
            );
    }
}
