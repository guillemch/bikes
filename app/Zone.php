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
    public function notificationMessage($intent = 'rent', $silent = false)
    {
        $message = [];
        $key = ($intent == 'rent') ? 'available' : 'free';
        $i = 0;
        $total = count($this->statuses);

        foreach($this->statuses as $station) {
            $i++;

            // Preceed last sentence with 'But' if it has enough bikes/docks.
            $but = ($station->status($key) > 3 && $i != 1) ? 'But ' : '';

            // Push message
            $message[] = $but . $station->notificationMessage($intent, false, $silent);

            // If we've reached a station with enough bikes/docks, stop composing.
            if($station->status($key) > 3) {
                break;
            }

            // If no station had bikes/docks, add final message
            if($station->status($key) == 0 && $i == $total) {
                $message[] = 'Search for alternative stations.';
            }
        }

        return implode(' ', $message);
    }

    /**
     * Submit the request to notify to IFTTT
     *
     * @return void
     */
    public function notify($intent, $silent = false)
    {
        $message = $this->notificationMessage($intent, $silent);

        if(!empty($message)) {
            $ifttt = new IFTTT;
            $ifttt->submit('notify_station_status', $message);
        }
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
