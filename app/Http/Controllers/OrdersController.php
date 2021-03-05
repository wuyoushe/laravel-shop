<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\CouponCodeUnavailableException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
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
        $coupon = null;

        //如果用户提交了优惠码
        if($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if(!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'), $coupon);
    }

    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        //校验订单是否属于当前用户
        $this->authorize('own', $order);
        //判断订单是否已付款
        if(!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付, 不可退款');
        }

        //判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        //将用户输入的退款理由放到订单的extra字段中
        $extra = $order->extra ?:[];
        $extra['refund_reason'] = $request->input('reason');

        //将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;
    }


    public function received(Order $order, Request $request)
    {
        //校验权限
        $this->authorize('own', $order);

        //判断订单是否为已发货的状态
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        //更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        //返回原页面
//        return redirect()->back();
        return $order;
    }

    public function review(Order $order)
    {
        $this->authorize('own', $order);
        //判断是否已经支付
        if( !$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可评价');
        }

        //使用load方法加载关联数据，避免N+1问题
        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, SendReviewRequest $request)
    {
        $this->authorize('own', $order);
        if(!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //判断是否已经评价
        if($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reeviews');
        //开启事务
        \DB::transaction(function () use($reviews, $order) {
            //遍历用户提交的数据
            foreach($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                //保持评分和评价
                $orderItem->update([
                    'rating'    => $review['rating'],
                    'review'    => $review['review'],
                    'reviewed_at'=> Carbon::now(),
                ]);
            }
            $order->update(['reviewed' => true]);
        });
        event(new OrderReviewed($order));

        return redirect()->back();
    }
}
