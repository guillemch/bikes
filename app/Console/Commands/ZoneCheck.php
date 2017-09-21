<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use App\Zone;

class ZoneCheck extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'zone:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Returns available bikes and parking spots for specified stations';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $stations = $this->argument('stations');
        $to = $this->option('to');
        $notify = $this->option('notify');

        $zone = new Zone($stations);
        $statuses = $zone->getStatuses();
        $message = $zone->getNotificationMessage($to);

        if($notify) {
            $zone->notify($to);
        }

        $this->info($message);

        $headers = ['Station', 'Bikes', 'Docks'];
        $rows = [];

        foreach($statuses as $id => $station) {
            $rows[] = ['#' . $id, $station->available, $station->free];
        }

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
            ['stations', InputArgument::REQUIRED, 'The IDs of the station.'],
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
