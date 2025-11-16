<?php

namespace Syndicate\Promoter\Services;

use Syndicate\Promoter\Contracts\HasSeo;
use Syndicate\Promoter\Services\Traits\HandlesSitemapContent;
use Syndicate\Promoter\Services\Traits\HandlesXmlGeneration;
use Syndicate\Promoter\Sitemaps\IndexSitemap;
use Syndicate\Promoter\Sitemaps\ModelSitemap;
use Syndicate\Warden\Contracts\HasRevisedAt;

class SitemapService
{
    use HandlesXmlGeneration;
    use HandlesSitemapContent;

    public function generateSitemapContent(ModelSitemap $sitemap): string
    {
        $xml = $this->createSitemap('urlset');

        $usedNamespaces = [
            'image' => false,
            'news' => false,
            'xhtml' => false
        ];

        $sitemap->getBaseQuery()
            ->chunk(20, function ($records) use (&$xml, $sitemap, &$usedNamespaces) {
                foreach ($records as $record) {
                    $this->addSitemapItem($xml, $record, $sitemap, $usedNamespaces);
                }
            });

        $this->addRequiredNamespaces($xml, $usedNamespaces);

        return $xml->saveXML();
    }

    public function generateIndexSitemap(IndexSitemap $sitemap): string
    {
        $models = $sitemap->getAllModels();
        $xml = $this->createSitemap('sitemapindex');
        $newsLastMod = now();

        foreach ($models as $modelClass) {
            $lastMod = $modelClass::sitemap()->getSitemapLastModified();
            $link = $modelClass::sitemap()->link();

            if ($sitemap->isNewsModel($modelClass)) {
                $newsLastMod = $lastMod;
            }

            $this->addSitemap(
                $xml,
                $link,
                $lastMod->toAtomString()
            );
        }

        if (!empty($sitemap->newsModels)) {
            $link = $sitemap->newsLink();
            $this->addSitemap(
                $xml,
                $link,
                $newsLastMod->toAtomString()
            );
        }

        return $xml->saveXML();
    }

    public function generateNewsSitemap(IndexSitemap $sitemapIndex): string
    {
        $newsModels = $sitemapIndex->newsModels;
        $xml = $this->createSitemap('urlset');

        $usedNamespaces = [
            'image' => false,
            'news' => false,
            'xhtml' => false
        ];

        foreach ($newsModels as $modelClass) {
            if (!class_exists($modelClass) || !is_subclass_of($modelClass, HasSeo::class)) {
                continue;
            }

            $sitemap = $modelClass::sitemap();
            $baseQuery = $sitemap->getBaseQuery();

            if (new $modelClass() instanceof HasRevisedAt) {
                $baseQuery->whereDate('revised_at', '>', now()->subDays(2));
            } else {
                $baseQuery->whereDate('published_at', '>', now()->subDays(2));
            }

            $baseQuery
                ->chunk(20, function ($records) use (&$xml, $sitemap, &$usedNamespaces) {
                    foreach ($records as $record) {
                        $this->addSitemapItem($xml, $record, $sitemap, $usedNamespaces, true);
                    }
                });
        }

        $this->addRequiredNamespaces($xml, $usedNamespaces);

        return $xml->saveXML();
    }
}
