<?php

namespace App\Events;

use App\Models\Note;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConsultationNoteCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public $note;
    public $content;
    public $recipients;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Note $note, $recipients)
    {
        $this->content = str_replace('<br>', "\n", strip_tags($note->content, '<br>'));
        $this->recipients = $recipients;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
