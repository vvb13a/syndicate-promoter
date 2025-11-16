<?php

namespace Syndicate\Promoter\Notifications;

use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Syndicate\Assistant\Enums\FilamentPageType;
use Syndicate\Lawyer\Contracts\HasFilamentResource;
use Syndicate\Promoter\Models\Indexing;

class IndexingCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Indexing $indexing
    ) {
    }

    public function via($notifiable): array
    {
        return ['broadcast'];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        $notification = \Filament\Notifications\Notification::make()
            ->title('Indexing Inspection Finished')
            ->body("Indexing Status: '{$this->indexing->status->getLabel()}'.")
            ->persistent()
            ->icon($this->indexing->status->getIcon())
            ->iconColor($this->indexing->status->getColor())
            ->color($this->indexing->status->getColor())
            ->success();

        if (!$this->indexing->subject instanceof HasFilamentResource) {
            return $notification->getBroadcastMessage();
        }

        $notification->actions([
            Action::make('view')
                ->label('View')
                ->button()
                ->url($this->indexing->subject->getFilamentResourceUrl(FilamentPageType::EditSeo))
        ]);

        return $notification->getBroadcastMessage();
    }
}
