<?php

namespace App;

use App\Libraries\IFTTT;
use App\Station;

class Zone
{
    /**
     * The stations in the zone
     *
     * @var array
     */
    protected $stations;

    /**
     * The statuses of the stations
     *
     * @return array
     */
    protected $statuses;

    /**
     * Construct a Zone object and fetch the statuses
     *
     * @return void
     */
    public function __construct($stations) {
        if(!is_array($stations)) {
            $stations = explode(",", $stations);
        }

        $this->stations = $stations;
        $this->fetchStatuses();
    }

    /**
     * Get the statuses of the stations in the zone
     *
     * @return array
     */
    public function statuses()
    {
        $statuses = [];

        foreach($this->statuses as $station) {
            $statuses[$station->id()] = $station->status();
        }

        return $statuses;
    }

    /**
     * Compose the notification to send
     *
     * @return string
     */
    public function notificationMessage($intent = 'rent')
    {
        $message = [];
        $key = ($intent == 'rent') ? 'available' : 'free';
        $i = 0;

        foreach($this->statuses as $station) {
            $i++;

            // Preceed last sentence with 'But' if it has enough bikes/docks.
            $but = ($station->status($key) > 3 && $i != 1) ? 'But ' : '';

            // Push message
            $message[] = $but . $station->notificationMessage($intent);

            // If we've reached a station with enough bikes/docks, stop composing.
            if($station->status($key) > 3) break;
        }

        return implode(' ', $message);
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
     * Iterate through all the stations and retreive their statuses.
     *
     * @return string
     */
    private function fetchStatuses()
    {
        foreach($this->stations as $station) {
            $this->statuses[] = new Station($station);
        }

        return $this;
    }
}
