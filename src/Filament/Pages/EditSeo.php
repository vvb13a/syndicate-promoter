<?php

namespace Syndicate\Promoter\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Model;
use Syndicate\Promoter\Contracts\HasSeo;
use Syndicate\Promoter\Filament\Relations\IndexingRelationManager;

class EditSeo extends EditRecord
{
    use Fields\HasTitle;
    use Fields\HasSlug;
    use Fields\HasDescription;
    use Fields\HasKeywords;
    use Fields\HasRobots;
    use Fields\HasCanonicalUrl;
    use Fields\HasImageAlt;

    protected static bool $isLazy = false;
    protected static ?string $navigationIcon = 'heroicon-o-signal';
    protected static ?string $navigationLabel = 'SEO';
    protected static ?string $breadcrumb = 'SEO';
    protected static ?string $title = 'Edit SEO';

    public function form(Form $form): Form
    {
        return $form->schema($this->getSeoFormSchema());
    }

    /**
     * Returns the complete schema array for the form.
     * Determines the overall layout (e.g., main grid vs. sidebar).
     */
    protected function getSeoFormSchema(): array
    {
        $schema = [];

        // 1. Add the Slug Section (Bound to the main model)
        $schema[] = $this->getSlugSection();

        // 2. Add SEO Sections Grid (Bound to 'seoData' relationship)
        $schema[] = Grid::make()
            ->relationship('seoData')
            ->schema($this->getSeoSectionsSchema())
            ->columns(1);

        return $schema;
    }

    /**
     * Returns the schema array for the SEO sections grid.
     * This is where individual SEO sections/fields are assembled.
     */
    protected function getSeoSectionsSchema(): array
    {
        return [
            Tabs::make('Required Information')
                ->contained(false)
                ->tabs([
                    Tabs\Tab::make('Required Information')
                        ->schema([
                            $this->getSeoKeywordInsightsSection(),
                            $this->getSeoTitleSection(),
                            $this->getSeoDescriptionSection(),
                            $this->getSeoImageAltSection(),
                        ]),
                    Tabs\Tab::make('Advanced Configuration')
                        ->schema([
                            $this->getSeoCanonicalUrlSection(),
                            $this->getSeoRobotsSection(),
                            // Optional legacy meta-keywords field
                            //  $this->getSeoKeywordsSection(),
                        ])
                ]),
        ];
    }

    /**
     * Keyword & Insights section reflecting the latest SEO model changes.
     * - Keyword: user-provided target keyword to optimize metadata
     * - generated_keyword: AI-derived from title/content (read-only)
     * - keyword_score: how well content aligns with the basis keyword (read-only)
     */
    protected function getSeoKeywordInsightsSection(): ?Component
    {
        return Section::make('Keyword & Insights')
            ->aside()
            ->columns(2)
            ->description('Set a target keyword used for optimization. The AI also derives a generated keyword from content and provides a relevance score.')
            ->schema([
                TextInput::make('keyword')
                    ->label('Target Keyword')
                    ->helperText('User-provided keyword that determines the optimization of title, description, image alt, and slug when set.')
                    ->maxLength(255),
                TextInput::make('generated_keyword')
                    ->label('Generated Keyword')
                    ->helperText('AI-generated from content; always present but only used for optimization when no target keyword is provided.')
                    ->disabled()
                    ->dehydrated(false),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('keyword_score')
                ->visible(function (Model&HasSeo $record): bool {
                    return $record->seoData?->keyword_score !== null;
                })
                ->disabled()
                ->outlined()
                ->label(function (Model&HasSeo $record): string {
                    return 'Keyword Score: '.$record->seoData->keyword_score;
                }),
            Action::make('submit')
                ->action(function ($livewire): void {
                    $livewire->save();
                })
                ->tooltip('Save current Record')
                ->submit(null)
                ->label('Save')
                ->color('success')
                ->icon('heroicon-o-bookmark-square')
                ->iconPosition(IconPosition::After)
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
