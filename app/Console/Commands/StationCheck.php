<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use App\Station;

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

        $station = new Station($station);
        $status = $station->status();
        $message = $station->notificationMessage($to);

        if($notify) {
            $station->notify($to);
        }

        $this->info($message);

        $headers = ['Bikes', 'Docks'];
        $rows = [
            [$status->available, $status->free]
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
