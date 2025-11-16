<?php

namespace Syndicate\Promoter\Services\Traits;

use Closure;
use Syndicate\Promoter\Services\Types\Hreflang;

trait ManagesHreflang
{
    public Hreflang|Closure|null $hreflang = null;

    public function hreflang(Hreflang|Closure|null $hreflang): self
    {
        $this->hreflang = $hreflang;

        return $this;
    }

    public function getHreflang(): ?Hreflang
    {
        return $this->evaluate($this->hreflang);
    }
}
