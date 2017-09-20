<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use \App\Events\NotifyStatus;

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

        $to = $this->option('to');
        $notify = $this->option('notify');

        $xml = $this->getStationInfo();
        $status = simplexml_load_string($xml);
        $status = (array)$status;

        if(!isset($status['available'])) {
            $this->error('The station was not found.');
            return;
        }

        if($to == 'park'){
            if($status['free'] == 0) {
                $message = "Station $station is full. Go to an alternative station.";
                $this->error($message);
            } elseif($status['free'] <= 3) {
                $message = "Station $station might be full soon. Proceed at your own risk.";
                $this->error($message);
            } else {
                $message = "Station $station has free spots to spare.";
                $this->info($message);
            }
        } else {
            if($status['available'] == 0) {
                $message = "Station $station is empty. Go to an alternative station.";
                $this->error($message);
            } elseif($status['available'] <= 3) {
                $message = "Station $station might be empty soon. Proceed at your own risk.";
                $this->error($message);
            } else {
                $message = "Station $station has bikes to spare.";
                $this->info($message);
            }
        }

        if($notify) {
            event(new NotifyStatus($message));
            $this->info('Status notified.');
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

    /**
     * Get the console options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['to', null, InputOption::VALUE_OPTIONAL, 'Wether we\'d like to rent or park a bike', 'rent'],
            ['notify', null, InputOption::VALUE_NONE, 'Notify the response'],
        ];
    }

    protected function getStationInfo()
    {
        $url = $this->url . $this->station;

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
