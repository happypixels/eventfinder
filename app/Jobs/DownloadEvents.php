<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Event;
use App\Contracts\AgentContract;
use App\Models\Venue;

class DownloadEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $agent;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(AgentContract $agent)
    {
        $this->agent = $agent;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->agent->gatherEvents() as $item) {
            $event = $this->agent->mapEvent($item);
            $event = array_merge($event, $this->agent->mapPrices($item));
            $event = array_merge($event, $this->agent->downloadAndMapImage($item));

            // If the venue doesn't exist, create it.
            $event['venue_id'] = $this->createOrUpdateVenue($item)->id;

            // If the event doesn't exist, create it.
            $model = Event::firstOrNew([
                'agent_class'          => $this->agent->fullyQualifiedClassName(),
                'agent_event_id'       => $event['agent_event_id']
            ]);
            $model->fill($event);
            $model->save();
        }

        $this->agent->cleanup();
    }

    private function createOrUpdateVenue($item)
    {
        $venue = $this->agent->mapVenue($item);

        $model = Venue::where('title', $venue['title'])
            ->where('address', $venue['address'])
            ->where('city', $venue['city'])
            ->first();

        if (!$model) {
            $model = new Venue;
        }

        $model->fill($venue);
        $model->save();

        return $model;
    }
}
