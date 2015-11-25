<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\Event\EventTrait;

class StoreEvent extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels, EventTrait;

    protected $options;
    protected $callback;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($options, $callback)
    {
        $this->options = $options;
        $this->callback = $callback;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->storeEvent($this->options, $this->callback);
    }
}
