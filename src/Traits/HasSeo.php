<?php

namespace Syndicate\Promoter\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Syndicate\Promoter\Enums\IndexingStatus;
use Syndicate\Promoter\Models\Indexing;
use Syndicate\Promoter\Models\SeoData;
use Syndicate\Promoter\Services\SeoService;
use Syndicate\Promoter\Sitemaps\ModelSitemap;

/**
 * @template TModel of Model
 * @mixin Model
 */
trait HasSeo
{
    protected ?SeoService $seoInstance = null;

    public static function bootHasSeo(): void
    {
        static::created(function (Model $model) {
            /** @var HasSeo $model */
            if (!$model->seoData()->exists()) {
                $model->seoData()->create();
            }
        });

        static::deleted(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                return;
            }
            /** @var HasSeo $model */
            $model->seoData()->delete();
        });
    }

    public function seoData(): MorphOne
    {
        return $this->morphOne(SeoData::class, 'seoable');
    }

    public static function sitemap(): ModelSitemap
    {
        return ModelSitemap::make(static::class);
    }

    public function getSeoAttribute(): SeoService
    {
        if ($this->seoInstance === null) {
            /** @var Model $this */
            $seo = SeoService::make($this);

            static::configureSeo($seo);

            $this->seoInstance = $seo;
        }

        return $this->seoInstance;
    }

    abstract public static function configureSeo(SeoService $seo): void;

    public function scopeWithSeoDescription($query): void
    {
        $query->addSelect([
            'seo_data__description' => SeoData::query()
                ->select('description')
                ->whereColumn('syndicate_seo_data.seoable_id', $this->getTable().'.'.$this->getKeyName())
                ->where('syndicate_seo_data.seoable_type', $this->getMorphClass())
                ->take(1)
        ]);
    }

    public function indexingStatus(): IndexingStatus
    {
        return $this->indexing?->status ?? IndexingStatus::Unknown;
    }

    public function indexingTimeline(): MorphMany
    {
        return $this->morphMany(Indexing::class, 'subject');
    }

    public function indexing(): MorphOne
    {
        return $this->morphOne(Indexing::class, 'subject')->latest();
    }
}
