<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class StationCheck extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'station:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns available bikes and parking spots at the specified station';

    /**
     * The station ID
     *
     * @var string
     */
    protected $station;

    /**
     * The API endpoint
     *
     * @var string
     */
    protected $url = 'http://www.valenbisi.es/service/stationdetails/valence/';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $station = $this->argument('station');
        $this->station = $station;

        $xml = $this->getStationInfo();
        $status = simplexml_load_string($xml);
        $status = (array)$status;

        if(!isset($status['available'])) {
            $this->error('The station was not found.');
            return;
        }


        if($status['available'] == 0) {
            $this->warning('The station is empty. Go to an alternate station.');
        } elseif($status['available'] <= 3) {
            $this->warning('The station might be empty soon. Proceed at your own risk.');
        } else {
            $this->info('The station has bikes to spare.');
        }

        $headers = ['Available', 'Free'];
        $rows = [
            [
                'available' => $status['available'],
                'free' => $status['free']
            ]
        ];

        $this->table($headers, $rows);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['station', InputArgument::REQUIRED, 'The ID of the station.'],
        ];
    }

    protected function getStationInfo()
    {
        $url = $this->url . $this->station;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec ($ch);
        $info = curl_getinfo($ch);
        $http_result = $info ['http_code'];
        curl_close ($ch);

        return $output;
    }

}
