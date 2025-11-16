<?php

namespace Syndicate\Promoter\Services;

use Astrotomic\OpenGraph\TwitterType;
use Astrotomic\OpenGraph\Type;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Spatie\SchemaOrg\Graph;
use Syndicate\Promoter\Contracts\SeoConfig;
use Syndicate\Promoter\Enums\RobotsDirective;
use Syndicate\Promoter\Support\Hreflang;

class SeoService implements Htmlable
{
    protected SeoConfig $seoConfig;

    public function __construct(protected Model $record)
    {
    }

    public static function make(Model $record): self
    {
        return app(static::class, ['record' => $record]);
    }

    public function toHtml(): string
    {
        return $this->render()->render();
    }

    public function render(): View
    {
        return view('promoter::seo', [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'canonicalUrl' => $this->getCanonicalUrl(),
            'robots' => $this->getRobots(),
            'twitter' => $this->getTwitter(),
            'openGraph' => $this->getOpenGraph(),
            'schema' => $this->getSchema(),
            'hreflang' => $this->getHreflang(),
        ]);
    }

    public function getTitle(): ?string
    {
        return $this->seoConfig->title($this->record, $this);
    }

    public function getDescription(): ?string
    {
        return $this->seoConfig->description($this->record, $this);
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->seoConfig->canonicalUrl($this->record, $this);
    }

    public function getRobots(): ?RobotsDirective
    {
        return $this->seoConfig->robots($this->record, $this);
    }

    public function getTwitter(): ?TwitterType
    {
        return $this->seoConfig->twitter($this->record, $this);
    }

    public function getOpenGraph(): ?Type
    {
        return $this->seoConfig->openGraph($this->record, $this);
    }

    public function getSchema(): Graph|null
    {
        return $this->seoConfig->schema($this->record, $this);
    }

    public function getHreflang(): ?Hreflang
    {
        return $this->seoConfig->hreflang($this->record, $this);
    }
}
