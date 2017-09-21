<?php

namespace App;

use App\Libraries\IFTTT;
use App\Libraries\Valenbisi;

class Station
{
    /**
     * The ID of the station.
     *
     * @var string
     */
    protected $id;

    /**
     * Compose the notification to send
     *
     * @var SimpleXMLElement
     */
    protected $status;

    /**
     * Construct the Station object
     *
     * @return void
     */
    public function __construct($stationId) {
        $this->id = $stationId;
        $this->fetchStatus();
    }

    /**
     * Get the ID of the station
     *
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * Get the status of the station
     *
     * @return mixed
     */
    public function status($key = null)
    {
        if($key) {
            return $this->status->$key;
        }

        return $this->status;
    }

    /**
     * Compose the notification to send
     *
     * @return string
     */
    public function notificationMessage($intent = 'rent', $single = true)
    {
        $key = ($intent == 'rent') ? 'available' : 'free';
        $number = $this->status($key);

        if($this->status('open') == 0) {
            $message = "Station #$this->id is out of order.";
            if($single) $message .= " Go to an alternative station.";
        } elseif($number == 0) {
            $word = $this->word($intent, $number);
            $message = "Station #$this->id has no $key $word.";
            if($single) $message .= " Go to an alternative station.";
        } elseif($number <= 3) {
            $word = $this->word($intent, $number);
            $message = "Only $number $word left at Station #$this->id.";
            if($single) $message .= "Hurry!";
        } else {
            $word = $this->word($intent, $number);
            $message = "Station #$this->id has $number $key $word.";
        }

        return $message;
    }

    /**
     * Submit the request to notify to IFTTT
     *
     * @return void
     */
    public function notify($intent)
    {
        $message = $this->notificationMessage($intent);
        $ifttt = new IFTTT;
        $ifttt->submit('notify_station_status', $message);
    }

    /**
     * Retreive the status of the station
     *
     * @return SimpleXMLElement
     */
    private function fetchStatus()
    {
        $valenbisi = new Valenbisi;
        $this->status = $valenbisi->getStation($this->id);

        return $this;
    }

    private function word($intent = 'rent', $number)
    {
        $words = [
            'rent' => ['singular' => 'bike', 'plural' => 'bikes'],
            'park' => ['singular' => 'dock', 'plural' => 'docks']
        ];
        $key = ($number == 1) ? 'singular' : 'plural';

        return $words[$intent][$key];
    }
}
