<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Carbon\Carbon;

use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function show(Order $order, Request $request)
    {
//        load() 称为延迟加载，不同点在于load()是在已经查询出来的模型上调用，而with()则是在ORM查询构造器上调用
        $this->authorize('own', $order);
        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function index(Request $request)
    {
        $orders = Order::query()
        ->with(['items.product', 'items.productSku'])
        ->where('user_id', $request->user()->id)
        ->orderBy('created_at', 'desc')
        ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

//    public function store(Request $request, CartService $cartService, OrderService $orderService)
//    {
//        $user = $request->user();
//        //开启一个数据库事务
//        $order = \DB::transaction(function () use($user, $request, $cartService) {
//            $address = UserAddress::find($request->input('address_id'));
//            //更新地址的最后使用时间
//            $address->update(['last_used_at' =>Carbon::now()]);
//            //创建一个订单
//            $order = new Order([
//                'address'   => [
//                    'address'   => $address->full_address,
//                    'zip'       => $address->zip,
//                    'contact_name'  => $address->contact_name,
//                    'contact_phone' => $address->contact_phone,
//
//                ],
//                'remark'        => $request->input('remark'),
//                'total_amount'  => 0,
//            ]);
//            //订单关联当前用户
//            $order->user()->associate($user);
//            //写入数据库
//            $order->save();
//
//            $totalAmount = 0;
//            $items = $request->input('items');
//            //遍历用户提交的sku
//            foreach($items as $data){
//                $sku = ProductSku::find($data['sku_id']);
//                //创建一个OrderItem并直接与当前订单关联
//                $item = $order->items()->make([
//                    'amount'    => $data['amount'],
//                    'price'     => $sku->price,
//                ]);
//                $item->product()->associate($sku->product_id);
//                $item->productSku()->associate($sku);
//                $item->save();
//                $totalAmount += $sku->price * $data['amount'];
//                if ( $sku->decreaseStock($data['amount']) <= 0) {
//                    throw new InvalidRequestException('该商品库存不足');
//                }
//            }
//
//            //更新订单总金额
//            $order->update(['total_amount' => $totalAmount]);
//
//            //将下单的商品从购物车中移除
//            $skuIds = collect($items)->pluck('sku_id');
////            $user->CartItems()->whereIn('product_sku_id', $skuIds)->delete();
//            $cartService->remove($skuIds);
//
//            return $order;
//        });
//
//        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
//
//        return $order;
//    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user    = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));
    }
}