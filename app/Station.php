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
     * The station's nickname, if any.
     *
     * @var string
     */
    protected $nickname;

    /**
     * Construct the Station object
     *
     * @return void
     */
    public function __construct($station) {
        $this->setId($station)
             ->setNickname($station)
             ->fetchStatus();
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
     * Get the ID of the station
     *
     * @return string
     */
    public function nickname()
    {
        return $this->nickname;
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
        $stationName = ($this->nickname) ? $this->nickname : "Station #$this->id";
        $key = ($intent == 'rent') ? 'available' : 'free';
        $number = $this->status($key);

        if($this->status('open') == 0 || $this->status('connected') == 0) {
            $message = "$stationName is out of order.";
            if($single) $message .= " Go to an alternative station.";
        } elseif($number == 0) {
            $word = $this->word($intent, $number);
            $message = "$stationName has no $key $word.";
            if($single) $message .= " Go to an alternative station.";
        } elseif($number <= 3) {
            $word = $this->word($intent, $number);
            $message = "Only $number $word left at $stationName.";
            if($single) $message .= "Hurry!";
        } else {
            $word = $this->word($intent, $number);
            $message = "$stationName has $number $key $word.";
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

    private function setId($station)
    {
        $station = explode(":", $station);
        $this->id = $station[0];

        return $this;
    }

    private function setNickname($station)
    {
        $station = explode(":", $station);
        if(isset($station[1])) {
            $this->nickname = $station[1];
        }

        return $this;
    }
}
