<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

     protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        //设置延迟的时间，delay()方法的参数代表多少秒后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->order->paid_at) {
            return;
        }
        //通过事务执行sql
        \DB::transaction(function () {
            //将订单的closed字段标记为true，即将关闭
            $this->order->update(['closed' => true]);
            //循环遍历订单中的商品SKU，将订单中的数量加回到SKU的库存中去
            foreach($this->order->items as $item) {
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
