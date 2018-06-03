<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Agents\Kernel as Agents;
use App\Jobs\DownloadEvents;

class TriggerEventDownload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Triggers the DownloadEvents job for every active agent.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $agents = Agents::get();

        if (!$agents) {
            return false;
        }

        foreach ($agents as $agent) {
            DownloadEvents::dispatch(new $agent);
        }
    }
}
