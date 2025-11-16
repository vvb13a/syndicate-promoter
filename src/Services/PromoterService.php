<?php

namespace Syndicate\Promoter\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\SlugOptions;
use Syndicate\Linguist\Contracts\HasLanguage;

class PromoterService
{
    public function prepareSlugOptions(Model $record): SlugOptions
    {
        $options = SlugOptions::create()
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();

        if ($record instanceof HasLanguage) {
            $options
                ->usingLanguage($record->getLanguage()->value)
                ->extraScope(function (Builder $builder) use ($record): Builder {
                    return $builder->where($record->getLanguageColumn(),
                        $record->getLanguage()->value);
                });
        }

        return $options;
    }
}
