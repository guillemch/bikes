<?php

namespace App\Libraries;

class IFTTT
{
    /**
     * The API url
     *
     * @var string
     */
    protected $url = 'https://maker.ifttt.com/trigger/*/with/key/';

    /**
     * Call the API using cURL.
     *
     * @return void
     */
    public function submit($event, $message)
    {
        $this->call($event, $message);
    }

    /**
     * Call the API using cURL.
     *
     * @return void
     */
    private function call($event = 'notify_station_status', $message)
    {
        $key = env('IFTTT_KEY');
        $fields = json_encode(['value1' => $message]);
        $url = str_replace('*', $event, $this->url) . $key;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $output = curl_exec($ch);
        if(curl_errno($ch)){
            throw new \Exception('Failed attempting to notify station info.');
        }
        curl_close ($ch);
    }
}
