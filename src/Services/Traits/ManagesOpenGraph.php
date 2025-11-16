<?php

namespace Syndicate\Promoter\Services\Traits;

use Astrotomic\OpenGraph\Type;
use Closure;

trait ManagesOpenGraph
{
    public Type|Closure|null $openGraph = null;

    public function openGraph(Type|Closure $openGraph): self
    {
        $this->openGraph = $openGraph;

        return $this;
    }

    public function getOpenGraph(): ?Type
    {
        return $this->evaluate($this->openGraph);
    }
}
