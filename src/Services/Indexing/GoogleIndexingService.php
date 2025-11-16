<?php

namespace Syndicate\Promoter\Services\Indexing;

use Google\Client;
use Google\Service\Indexing;
use Google\Service\Indexing\UrlNotification;
use Google\Service\SearchConsole;
use Syndicate\Promoter\Contracts\IndexingService;
use Syndicate\Promoter\DTOs\IndexingDto;
use Syndicate\Promoter\Enums\IndexingService as IndexingServiceEnum;
use Syndicate\Promoter\Enums\IndexingStatus;
use Syndicate\Promoter\Services\Indexing\Helpers\GoogleIndexStatusMapper;
use Throwable;

class GoogleIndexingService implements IndexingService
{
    public function getIndexingService(): IndexingServiceEnum
    {
        return IndexingServiceEnum::Google;
    }

    public function submitUrl(string $url): IndexingDto
    {
        try {
            return new IndexingDto(
                status: IndexingStatus::Submitted,
                payload: $this->publishNotification($url, 'URL_UPDATED'),
            );
        } catch (Throwable $e) {
            return new IndexingDto(
                status: IndexingStatus::Error,
                payload: ['error' => $e->getMessage()],
            );
        }
    }

    private function publishNotification(string $url, string $type): array
    {
        $client = new Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Indexing::INDEXING);

        $indexingService = new Indexing($client);

        $notification = new UrlNotification();
        $notification->setUrl($url);
        $notification->setType($type);

        $response = $indexingService->urlNotifications->publish($notification);
        $result = $response;

        return collect($result->toSimpleObject())->toArray();
    }

    public function deleteUrl(string $url): IndexingDto
    {
        try {
            return new IndexingDto(
                status: IndexingStatus::Removal_Requested,
                payload: $this->publishNotification($url, 'URL_DELETED'),
            );
        } catch (Throwable $e) {
            return new IndexingDto(
                status: IndexingStatus::Error,
                payload: ['error' => $e->getMessage()],
            );
        }
    }

    public function inspectUrl(string $url): IndexingDto
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(SearchConsole::WEBMASTERS_READONLY);

            $searchConsoleService = new SearchConsole($client);

            $request = new SearchConsole\InspectUrlIndexRequest();
            $request->setInspectionUrl($url);
            $request->setSiteUrl(config('syndicate.promoter.indexing.google.site_url'));

            $response = $searchConsoleService->urlInspection_index->inspect($request);
            $indexingResult = $response->getInspectionResult()->getIndexStatusResult();

            return new IndexingDto(
                status: GoogleIndexStatusMapper::fromGoogleResponse($indexingResult->verdict,
                    $indexingResult->coverageState),
                payload: collect($response->getInspectionResult()->toSimpleObject())->toArray(),
            );
        } catch (Throwable $e) {
            return new IndexingDto(
                status: IndexingStatus::Error,
                payload: ['error' => $e->getMessage()],
            );
        }
    }
}
