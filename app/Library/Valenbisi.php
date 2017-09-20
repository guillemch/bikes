<?php

namespace App\Library;

class Valenbisi {
    /**
     * The API url
     *
     * @var string
     */
    protected $url = 'http://www.valenbisi.es/service/stationdetails/valence/';

    /**
     * Request a station and parse XML output.
     *
     * @return object
     */
    public function getStation($station)
    {
        $output = $this->call($station);
        $status = simplexml_load_string($output);

        return $status;
    }

    /**
     * Call the API using cURL.
     *
     * @return string
     */
    private function call($station)
    {
        $url = $this->url . $station;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        if(curl_errno($ch)){
            throw new \Exception('Failed attempting to retreive station info.');
        }
        curl_close ($ch);

        return $output;
    }
}
