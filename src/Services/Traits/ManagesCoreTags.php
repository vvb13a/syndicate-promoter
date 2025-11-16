<?php

namespace Syndicate\Promoter\Services\Traits;

use Closure;
use Syndicate\Promoter\Enums\RobotsDirective;

trait ManagesCoreTags
{
    public string|Closure|null $title = null;

    public string|Closure|null $description = null;

    public string|Closure|null $keywords = null;

    public string|Closure|null $canonicalUrl = null;

    public string|Closure|null|RobotsDirective $robots = null;

    public function title(string|Closure|null $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function description(string|Closure|null $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function keywords(string|Closure|null $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function canonicalUrl(string|Closure|null $canonicalUrl): self
    {
        $this->canonicalUrl = $canonicalUrl;

        return $this;
    }

    public function robots(string|Closure|null|RobotsDirective $robots): self
    {
        $this->robots = $robots;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->evaluate($this->keywords);
    }

    public function getRobots(): ?string
    {
        $robots = $this->evaluate($this->robots);

        if ($robots instanceof RobotsDirective) {
            return $robots->value;
        }

        return $robots;
    }

    public function getTitle(): ?string
    {
        return $this->evaluate($this->title);
    }

    public function getDescription(): ?string
    {
        return $this->evaluate($this->description);
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->evaluate($this->canonicalUrl);
    }
}
