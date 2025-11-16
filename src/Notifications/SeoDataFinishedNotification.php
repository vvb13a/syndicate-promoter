<?php

namespace Syndicate\Promoter\Notifications;

use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class SeoDataFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $generatedKeyword,
        public ?string $viewUrl = null,
    ) {
    }

    public function via($notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notification = \Filament\Notifications\Notification::make()
            ->title('SEO data generated')
            ->body("Generated Keyword: '{$this->generatedKeyword}'.")
            ->persistent()
            ->success();

        if ($this->viewUrl) {
            $notification->actions([
                Action::make('view')
                    ->label('View')
                    ->button()
                    ->url($this->viewUrl)
            ]);
        }

        return $notification->getBroadcastMessage();
    }
}
