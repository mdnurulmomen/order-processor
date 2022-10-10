<?php

namespace App\Jobs;

use Exception;
use App\Models\Order;
use App\Models\Rider;
use App\Events\UpdateRider;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Support\Facades\Log;

class NotifyRiders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order, $rider;

    /**
     * Delete the job if its models no longer exist.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 25;

    /**
     * The maximum number of exceptions to allow before failing.
     *
     * @var int
     */
    // public $maxExceptions = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, Rider $rider)
    {
       $this->order = $order; 
       $this->rider = $rider; 
    }

    /**
     * Determine the time at which the job should timeout.
     *
     * @return \DateTime
     */
/*
    public function retryUntil()
    {
        return now()->addSeconds(30);
    }
*/

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {  
        if ($this->order->in_progress==1 && ! $this->order->riderAssigned()->exists()) {
            
            // initially cancelling order delivery request
            $riderNewDeliveryRecord = $this->order->riderAssigned()->create([
                'is_accepted' => 0,
                'rider_id' => $this->rider->id
            ]);

            // setting paused time to related rider for this cancelled delivery request
            $riderNewDeliveryRecord->rider()->update([
                'paused_at' => now()
            ]);

            // Notify Riders
            // Log::warning('NotifyRiders to Rider id : '.$this->rider->id.', at : '.date("h:i:sa"));

            // Broadcast to Rider for order request
            event(new UpdateRider($riderNewDeliveryRecord));
            
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }

}
