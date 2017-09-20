<?php

namespace App\Listeners;

use App\Events\NotifyStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyStatusListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ExampleEvent  $event
     * @return void
     */
    public function handle(NotifyStatus $event)
    {
        $this->notify($event->message);
    }

    private function notify($message)
    {
        $key = env('IFTTT_KEY');
        $event = 'notify_station_status';
        $fields = json_encode(['value1' => $message]);
        $url = 'https://maker.ifttt.com/trigger/' . $event . '/with/key/' . $key;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $output = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close ($ch);
    }

}
