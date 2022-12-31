<?php

namespace App\Jobs;

use App\Models\Order;
use App\Events\UpdateAdmin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MonitorOrderProgression implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

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
    public $maxExceptions = 5;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (! $this->order->riderAssigned()->exists()) {
            
            foreach ($this->order->merchants()->where('is_self_delivery', 0)->get() as $merchantOrder) {
                
                $this->disableMerchantOrder($merchantOrder);

            }

            if (! $this->order->merchants()->where('is_self_delivery', 1)->exists()) {  // no other merchant left which has self deliery service
                
                $this->disableOrderStatus($this->order);

            }
            else if (! $this->order->merchants()->where('in_progress', 1)->exists()) {    // no-other merchant has left which hasn't got status yet
                
                $this->stopOrderProgression($this->order);

            }

            $this->notifyAdmin($this->order);
            
        }
    }

    private function stopOrderProgression($order)
    {
        $totalMerchantOrders = $order->merchants()->count();
        $accomplishedMerchantOrder = $order->merchants()->where('is_completed', 1)->count();

        $order->update([
            'in_progress' => 0,
            'success_rate' => $accomplishedMerchantOrder > 0 ? (($accomplishedMerchantOrder / $totalMerchantOrders) * 100) : 0     // -1 (pending) / 1 (complete) / 0 (incomplete)
        ]);
    }

    private function disableMerchantOrder($merchantOrder)
    {
        $merchantOrder->update([
            'is_rider_available' => 0,
            'in_progress' => 0,
            'is_completed' => 0     // -1 (pending) / 1 (complete) / 0 (incomplete)
        ]);
    }

    private function disableOrderStatus($order)
    {
        $order->update([
            'in_progress' => 0,
            'success_rate' => 0     // -1 (pending) / 1 (complete) / 0 (incomplete)
        ]);
    }

    private function notifyAdmin(Order $order) 
    {
        event(new UpdateAdmin(Order::with(['merchants.products.merchantProduct', 'merchants.products.variation.merchantProductVariation.variation', 'merchants.products.addons.merchantProductAddon.addon', 'merchants.products.customization', 'riderAssigned', 'collections.merchant', 'serve', 'merchantOrderCancellations.canceller'])->find($order->id)));
    }
}
