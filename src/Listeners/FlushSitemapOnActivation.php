<?php

namespace Syndicate\Promoter\Listeners;

use Syndicate\Promoter\Contracts\HasSeo;
use Syndicate\Racer\Contracts\HasResponseCache;
use Syndicate\Warden\Events\VisibilityChanged;

class FlushSitemapOnActivation
{
    public function handle(VisibilityChanged $event): void
    {
        if (!$event->affectsLive()) {
            return;
        }

        if ($event->model instanceof HasSeo) {
            $sitemap = $event->model::sitemap();

            if ($sitemap instanceof HasResponseCache) {
                $sitemap->flushResponseCache();
            }
        }
    }
}
