<?php

namespace App\Events;

class NotifyStatus extends Event
{
    /**
     * The message to notify
     *
     * @var string
     */
    public $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
}
