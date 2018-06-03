<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\DownloadEvents;
use App\Agents\Kernel;

class TriggerEventDownloadTest extends TestCase
{
    /** @test */
    public function it_dispatches_job_with_all_agents()
    {
        Queue::fake();

        Artisan::call('events:download');

        $agents = Kernel::get();

        Queue::assertPushed(DownloadEvents::class, function ($job) use ($agents) {
            return ($job->agent->fullyQualifiedClassName() === $agents[0]);
        });
    }
}
