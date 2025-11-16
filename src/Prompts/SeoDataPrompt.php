<?php

namespace Syndicate\Promoter\Prompts;

use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Syndicate\Engineer\Contracts\AllowsRuntimeInstructions;
use Syndicate\Engineer\Facades\Engineer;
use Syndicate\Engineer\Prompts\BasePrompt;
use Syndicate\Engineer\Traits\Prompts;
use Syndicate\Lawyer\Contracts\HasContent;
use Syndicate\Lawyer\Contracts\HasTitle;
use Syndicate\Linguist\Contracts\HasLanguage;
use Syndicate\Promoter\Contracts\ToSeoDataPrompt;
use Syndicate\Promoter\Enums\KeywordType;

class SeoDataPrompt extends BasePrompt implements AllowsRuntimeInstructions
{
    use Prompts\HasRuntimeInstructions;
    use Prompts\HasTitle;
    use Prompts\HasContent;

    private ?string $keyword = null;
    private ?KeywordType $keywordType = null;

    public static function createFrom(Model $model): self
    {
        if ($model instanceof ToSeoDataPrompt) {
            return $model->toSeoDataPrompt();
        }

        if (!$model instanceof HasTitle || !$model instanceof HasContent) {
            throw new InvalidArgumentException(sprintf(
                'The source object of class "%s" does not implement the required HasTitle and HasContent contracts to create a SeoDataPrompt.',
                get_class($model)
            ));
        }

        return self::make()
            ->title($model->getTitle())
            ->content(Engineer::cleanHtmlForAi($model->getHtmlContent()))
            ->language($model instanceof HasLanguage ? $model->getLanguage() : null)
            ->keyword($model->seoData->keyword ?? null);
    }

    public function keyword(?string $keyword): self
    {
        $this->keyword = $keyword;
        return $this;
    }

    public static function make(): self
    {
        return app(self::class);
    }

    public static function canCreateFrom(Model $model): bool
    {
        if ($model instanceof ToSeoDataPrompt) {
            return true;
        }

        if ($model instanceof HasTitle && $model instanceof HasContent) {
            return true;
        }

        return false;
    }

    public function getExpectedAnswer(): string
    {
        return SeoDataAnswer::class;
    }

    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    public function getKeywordType(): ?KeywordType
    {
        return $this->keywordType;
    }

    public function keywordType(KeywordType $keywordType): self
    {
        $this->keywordType = $keywordType;
        return $this;
    }

    protected function getDefaultUserView(): string
    {
        return 'syndicate::promoter.prompts.seo-data-user';
    }

    protected function getDefaultSystemView(): string
    {
        return 'syndicate::promoter.prompts.seo-data-system';
    }
}
