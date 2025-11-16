<?php

namespace Syndicate\Promoter\Jobs;

use Illuminate\Support\Facades\Notification;
use Syndicate\Assistant\Enums\FilamentPageType;
use Syndicate\Engineer\Contracts\Answer;
use Syndicate\Engineer\Jobs\BaseChatAiJob;
use Syndicate\Lawyer\Contracts\HasFilamentResource;
use Syndicate\Promoter\Contracts\HasSeo;
use Syndicate\Promoter\Notifications\SeoDataFinishedNotification;
use Syndicate\Promoter\Prompts\SeoDataAnswer;
use Syndicate\Warden\Contracts\HasVisibility;
use Syndicate\Warden\Enums\Visibility;

class SeoDataViaAiJob extends BaseChatAiJob
{
    /**
     * Processes the AI-generated answer to update SEO data and notify the user.
     * @param  SeoDataAnswer  $answer
     */
    public function processAnswer(Answer $answer): void
    {
        $this->updateSeoData($answer);
        $this->updateSubjectSlugIfNeeded($answer);
        $this->notifyCauserOfCompletion($answer);
    }

    /**
     * Create or update the SEO data for the subject model.
     */
    private function updateSeoData(SeoDataAnswer $answer): void
    {
        if (!$this->subject instanceof HasSeo) {
            $this->fail('Subject must implement: '.HasSeo::class);
            return;
        }

        $this->subject->seoData()->updateOrCreate([], [
            'title' => $answer->title,
            'description' => $answer->description,
            'image_alt' => $answer->image_alt,
            'generated_keyword' => $answer->generated_keyword,
            'keyword_score' => $answer->keyword_score,
        ]);
    }

    /**
     * Update the subject's slug if it is not yet live.
     */
    private function updateSubjectSlugIfNeeded(SeoDataAnswer $answer): void
    {
        if ($this->subject instanceof HasVisibility && $this->subject->getVisibility() !== Visibility::Live) {
            if ($this->subject->hasAttribute('slug')) {
                $this->subject->setAttribute('slug', $answer->slug);
                $this->subject->save();
            }
        }
    }

    /**
     * Send a notification to the user who initiated the job.
     */
    private function notifyCauserOfCompletion(SeoDataAnswer $answer): void
    {
        if (!$this->causer) {
            return;
        }

        Notification::send($this->causer, new SeoDataFinishedNotification(
            generatedKeyword: $answer->generated_keyword,
            viewUrl: $this->subject instanceof HasFilamentResource
                ? $this->subject->getFilamentPageUrl(FilamentPageType::EditSeo)
                : null,
        ));
    }
}
