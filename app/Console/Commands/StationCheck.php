<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use \App\Events\NotifyStatus;
use \App\Library\Valenbisi;

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $station = $this->argument('station');
        $to = $this->option('to');
        $notify = $this->option('notify');

        $valenbisi = new Valenbisi();
        $status = (array) $valenbisi->getStation($station);

        if(!isset($status['available'])) {
            $this->error('The station was not found.');
            return;
        }

        if($to == 'park'){
            if($status['free'] == 0) {
                $message = "Station #$station is full. Go to an alternative station.";
                $this->error($message);
            } elseif($status['free'] <= 3) {
                $message = "Only " . $status['free'] . " available parking spots at station #$station. Proceed at your own risk.";
                $this->error($message);
            } else {
                $message = "Station #$station has " . $status['free'] . " available parking spots.";
                $this->info($message);
            }
        } else {
            if($status['available'] == 0) {
                $message = "Station #$station is empty. Go to an alternative station.";
                $this->error($message);
            } elseif($status['available'] <= 3) {
                $message = "Only " . $status['available'] . " available bikes at station #$station. Proceed at your own risk.";
                $this->error($message);
            } else {
                $message = "Station #$station has " . $status['available'] . " bikes.";
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
}
