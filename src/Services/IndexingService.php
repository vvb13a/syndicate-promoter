<?php

namespace Syndicate\Promoter\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;
use Syndicate\Lawyer\Contracts\HasUrl;
use Syndicate\Promoter\Contracts\HasSeo;
use Syndicate\Promoter\Enums\IndexingService as IndexingServiceEnum;
use Syndicate\Promoter\Enums\IndexingTrigger;
use Syndicate\Promoter\Jobs\IndexingJob;
use Syndicate\Promoter\Models\Indexing;
use Syndicate\Promoter\Notifications\IndexingCreatedNotification;

class IndexingService
{
    public function handle(
        IndexingServiceEnum $service,
        IndexingTrigger $trigger,
        Model&HasSeo&HasUrl $subject
    ): Indexing {
        return match ($trigger) {
            IndexingTrigger::Submission => $this->submit($service, $subject),
            IndexingTrigger::Deletion => $this->delete($service, $subject),
            IndexingTrigger::Inspection => $this->inspect($service, $subject),
        };
    }

    public function submit(IndexingServiceEnum $service, Model&HasSeo&HasUrl $subject): Indexing
    {
        $url = $subject->getUrl();
        $serviceInstance = $service->getService();
        $result = $serviceInstance->submitUrl($url);

        /** @var Indexing $indexing */
        $indexing = $subject->indexingTimeline()->create([
            'url' => $url,
            'service' => $service,
            'status' => $result->status,
            'trigger' => IndexingTrigger::Submission,
            'details' => $result->payload,
        ]);

        IndexingJob::dispatch(
            service: $service,
            trigger: IndexingTrigger::Inspection,
            subject: $subject,
        )->delay(now()->addHour());

        return $indexing;
    }

    public function delete(IndexingServiceEnum $service, Model&HasSeo&HasUrl $subject): Indexing
    {
        $url = $subject->getUrl();
        $serviceInstance = $service->getService();
        $result = $serviceInstance->deleteUrl($url);

        /** @var Indexing $indexing */
        $indexing = $subject->indexingTimeline()->create([
            'url' => $url,
            'service' => $service,
            'status' => $result->status,
            'trigger' => IndexingTrigger::Deletion,
            'details' => $result->payload,
        ]);

        IndexingJob::dispatch(
            service: $service,
            trigger: IndexingTrigger::Inspection,
            subject: $subject,
        )->delay(now()->addHour());

        return $indexing;
    }

    public function inspect(IndexingServiceEnum $service, Model&HasSeo&HasUrl $subject): Indexing
    {
        $url = $subject->getUrl();
        $serviceInstance = $service->getService();
        $result = $serviceInstance->inspectUrl($url);

        /** @var Indexing $indexing */
        $indexing = $subject->indexingTimeline()->create([
            'url' => $url,
            'service' => $service,
            'status' => $result->status,
            'trigger' => IndexingTrigger::Inspection,
            'details' => $result->payload,
        ]);

        return $indexing;
    }

    public function notifyCauser(Indexing $indexing, Model $causer): void
    {
        Notification::send($causer, new IndexingCreatedNotification($indexing));
    }
}
