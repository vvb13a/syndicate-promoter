<?php

namespace Syndicate\Promoter\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Syndicate\Lawyer\Contracts\HasUrl;
use Syndicate\Promoter\Contracts\HasSeo;
use Syndicate\Promoter\Enums\IndexingService as IndexingServiceEnum;
use Syndicate\Promoter\Enums\IndexingTrigger;
use Syndicate\Promoter\Services\IndexingService;

class IndexingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly IndexingServiceEnum $service,
        private readonly IndexingTrigger $trigger,
        private readonly Model&HasUrl&HasSeo $subject,
        public ?Model $causer = null,
    ) {
    }

    public function handle(): void
    {
        $indexingService = resolve(IndexingService::class);

        $indexing = $indexingService
            ->handle(
                service: $this->service,
                trigger: $this->trigger,
                subject: $this->subject
            );

        if ($this->causer) {
            $indexingService->notifyCauser($indexing, $this->causer);
        }
    }
}
