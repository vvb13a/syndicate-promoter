<?php

namespace Syndicate\Promoter\Services\Traits;

use DOMDocument;

trait HandlesXmlGeneration
{
    /**
     * Create base sitemap structure
     */
    private function createSitemap(
        string $sitemapType,
        bool   $image = false,
        bool   $news = false,
        bool   $href = false
    ): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $urlset = $dom->createElement($sitemapType);
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        if ($image) {
            $urlset->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
        }

        if ($news) {
            $urlset->setAttribute('xmlns:news', 'http://www.google.com/schemas/sitemap-news/0.9');
        }

        if ($href) {
            $urlset->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        }

        $dom->appendChild($urlset);
        return $dom;
    }

    /**
     * Add sitemap entry to sitemap index
     */
    private function addSitemap(DOMDocument $xml, string $loc, string $lastmod): void
    {
        $sitemapElement = $xml->createElement('sitemap');

        $locElement = $xml->createElement('loc', htmlspecialchars($loc, ENT_XML1, 'UTF-8'));
        $sitemapElement->appendChild($locElement);

        $lastmodElement = $xml->createElement('lastmod', $lastmod);
        $sitemapElement->appendChild($lastmodElement);

        $xml->documentElement->appendChild($sitemapElement);
    }

    /**
     * Add required namespaces to sitemap based on actual usage
     */
    private function addRequiredNamespaces(DOMDocument $xml, array $usedNamespaces): void
    {
        $rootElement = $xml->documentElement;

        if ($usedNamespaces['image']) {
            $rootElement->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');
        }

        if ($usedNamespaces['news']) {
            $rootElement->setAttribute('xmlns:news', 'http://www.google.com/schemas/sitemap-news/0.9');
        }

        if ($usedNamespaces['xhtml']) {
            $rootElement->setAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');
        }
    }
}
