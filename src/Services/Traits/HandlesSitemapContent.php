<?php

namespace Syndicate\Promoter\Services\Traits;

use DOMDocument;
use DOMElement;
use Illuminate\Database\Eloquent\Model;
use Syndicate\Promoter\DTOs\NewsMetadataDto;
use Syndicate\Promoter\Sitemaps\ModelSitemap;

trait HandlesSitemapContent
{
    /**
     * Add sitemap item from HasSeo model using per-model Sitemap
     */
    private function addSitemapItem(
        DOMDocument $xml,
        Model $record,
        ModelSitemap $sitemap,
        array &$usedNamespaces = [],
        bool $isNewsSitemap = false
    ): void {
        // Check if the record should be in sitemap using sitemap
        if (!$sitemap->getShouldBeInSitemap($record)) {
            return;
        }

        $urlElement = $xml->createElement('url');

        // Add location using sitemap
        $url = $sitemap->getUrl($record);
        if ($url) {
            $locElement = $xml->createElement('loc', htmlspecialchars($url, ENT_XML1, 'UTF-8'));
            $urlElement->appendChild($locElement);
        }

        // Add last modified using sitemap
        $lastMod = $sitemap->getLastModified($record);
        if ($lastMod) {
            $lastmodElement = $xml->createElement('lastmod', $lastMod->toAtomString());
            $urlElement->appendChild($lastmodElement);
        }

        // Add priority using sitemap
        $priority = $sitemap->getPriority($record);
        if ($priority !== null) {
            $priorityElement = $xml->createElement('priority', (string) $priority);
            $urlElement->appendChild($priorityElement);
        }

        // Add change frequency using sitemap
        $changeFreq = $sitemap->getChangeFrequency($record);
        if ($changeFreq) {
            $changeFreqElement = $xml->createElement('changefreq', $changeFreq);
            $urlElement->appendChild($changeFreqElement);
        }

        // Add translations if enabled
        if ($sitemap->hasTranslations()) {
            $this->addTranslations($xml, $urlElement, $sitemap, $record, $usedNamespaces);
        }

        // Add images if enabled
        if ($sitemap->hasImages()) {
            $this->addImages($xml, $urlElement, $sitemap, $record, $usedNamespaces);
        }

        // Add news metadata if enabled and this is a news sitemap
        if ($isNewsSitemap && $sitemap->isNewsItem()) {
            $this->addNewsMetadata($xml, $urlElement, $sitemap, $record, $usedNamespaces);
        }

        $xml->documentElement->appendChild($urlElement);
    }

    private function addTranslations(
        DOMDocument $xml,
        DOMElement $urlElement,
        ModelSitemap $sitemap,
        $record,
        array &$usedNamespaces = []
    ): void {
        $translations = $sitemap->getTranslations($record);

        if ($translations->isNotEmpty()) {
            // Mark xhtml namespace as used
            $usedNamespaces['xhtml'] = true;

            foreach ($translations as $translation) {
                $hrefElement = $xml->createElement('xhtml:link');
                $hrefElement->setAttribute('rel', 'alternate');
                $hrefElement->setAttribute('hreflang', $translation->language->getSlug());
                $hrefElement->setAttribute('href', $translation->link());
                $urlElement->appendChild($hrefElement);
            }
        }
    }

    private function addImages(
        DOMDocument $xml,
        DOMElement $urlElement,
        ModelSitemap $sitemap,
        $record,
        array &$usedNamespaces = []
    ): void {
        $images = $sitemap->getImages($record);

        if ($images->isNotEmpty()) {
            // Mark the image namespace as used
            $usedNamespaces['image'] = true;

            foreach ($images as $imageUrl) {
                $imageElement = $xml->createElement('image:image');
                $imageLocElement = $xml->createElement('image:loc', htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8'));

                $imageElement->appendChild($imageLocElement);
                $urlElement->appendChild($imageElement);
            }
        }
    }

    /**
     * Add news metadata from per-model Sitemap
     */
    private function addNewsMetadata(
        DOMDocument $xml,
        DOMElement $urlElement,
        ModelSitemap $sitemap,
        $record,
        array &$usedNamespaces = []
    ): void {
        $newsMetadata = $sitemap->getNewsMetadata($record);

        // Mark news namespace as used
        $usedNamespaces['news'] = true;

        $newsElement = $xml->createElement('news:news');

        // Publication info
        $publicationElement = $xml->createElement('news:publication');

        // Handle both DTO and array formats
        if ($newsMetadata instanceof NewsMetadataDto) {
            $nameElement = $xml->createElement('news:name', $newsMetadata->publicationName);
            $publicationElement->appendChild($nameElement);

            $languageElement = $xml->createElement('news:language', $newsMetadata->publicationLanguage);
            $publicationElement->appendChild($languageElement);

            $newsElement->appendChild($publicationElement);

            // Publication date
            $publicationDate = $xml->createElement('news:publication_date',
                htmlspecialchars($newsMetadata->getPublicationDateString(), ENT_XML1, 'UTF-8'));
            $newsElement->appendChild($publicationDate);

            // Title
            $title = $xml->createElement('news:title',
                htmlspecialchars($newsMetadata->title, ENT_XML1, 'UTF-8'));
            $newsElement->appendChild($title);
        } else {
            // Backward compatibility for array format
            $nameElement = $xml->createElement('news:name', $newsMetadata['publication_name'] ?? config('app.name'));
            $publicationElement->appendChild($nameElement);

            $languageElement = $xml->createElement('news:language',
                $newsMetadata['publication_language'] ?? app()->getLocale());
            $publicationElement->appendChild($languageElement);

            $newsElement->appendChild($publicationElement);

            // Publication date
            $publicationDate = $xml->createElement('news:publication_date',
                htmlspecialchars($newsMetadata['publication_date'], ENT_XML1, 'UTF-8'));
            $newsElement->appendChild($publicationDate);

            // Title
            $title = $xml->createElement('news:title',
                htmlspecialchars($newsMetadata['title'], ENT_XML1, 'UTF-8'));
            $newsElement->appendChild($title);
        }

        $urlElement->appendChild($newsElement);
    }
}
