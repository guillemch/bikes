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
    public function notificationMessage($intent = 'rent')
    {
        $word = ($intent == 'rent') ? 'bikes' : 'docks';
        $key = ($intent == 'rent') ? 'available' : 'free';
        $number = $this->status($key);

        if($number == 0) {
            $message = "Station #$this->id has no available $word.";
        } elseif($number <= 3) {
            $message = "Only $number $word left at Station #$this->id. Hurry!";
        } else {
            $message = "Station #$this->id has $number available $word to $intent.";
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
}
