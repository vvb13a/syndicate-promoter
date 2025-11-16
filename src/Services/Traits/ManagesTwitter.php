<?php

namespace Syndicate\Promoter\Services\Traits;

use Astrotomic\OpenGraph\TwitterType;
use Closure;

trait ManagesTwitter
{
    public TwitterType|Closure|null $twitter = null;

    public function twitter($twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getTwitter(): ?TwitterType
    {
        return $this->evaluate($this->twitter);
    }
}
