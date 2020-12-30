<?php

namespace App\Http\Controllers;

use App\Events\OrderPaid;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order, Request $request)
    {
        //判断订单是否属于当前用户
        $this->authorize('own', $order);
        //订单已支付或者已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        //调用支付宝的网页支付
        return app('alipay')->web([
           'out_trade_no'   => $order->no,
           'total_amount'   => $order->total_amount,
           'subject'        => '支付Laravel Shop的订单: ' . $order->no,  //订单标题
        ]);
    }

    //前端回调页面
    public function alipayReturn()
    {
        //校验提交的参数是否合法
        $data = app('alipay')->verify();
        dd($data);
    }

    //服务器端回调
    public function alipayNotify()
    {
        $data = app('alipay')->verify();
        \Log::debug('Alipay notify', $data->all());
    }

    /**
     * 微信支付
     * @param Order $order
     * @param Request $request
     * @return mixed
     * @throws InvalidRequestException
     */
    public function payByWechat(Order $order, Request $request) {
        // 校验权限
        $this->authorize('own', $order);
        // 校验订单状态
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // scan 方法为拉起微信扫码支付
        return app('wechat_pay')->scan([
            'out_trade_no' => $order->no,  // 商户订单流水号，与支付宝 out_trade_no 一样
            'total_fee' => $order->total_amount * 100, // 与支付宝不同，微信支付的金额单位是分。
            'body'      => '支付 Laravel Shop 的订单：'.$order->no, // 订单描述
        ]);
    }

    /**
     * @return string
     */
    public function wechatNotify()
    {
        //校验回调参数是否正确
        $data = app('wechat_pay')->verify();
        //找到对应的订单
        $order = Order::where('no', $data->out_trade_no)->first();
        //订单不存在则告知微信支付
        if(!$order) {
            return 'fail';
        }
        //订单已支付
        if ($order->paid_at) {
            return app('wechat_pay')->success();
        }

        //将订单标记为已支付
        $order->update([
           'paid_at'    => Carbon::now(),
           'payment_method' => 'wechat',
           'payment_no' => $data->transaction_id,
        ]);

        $this->afterPaid($order);

        return app('wechat_pay')->success();
    }

    protected function afterPaid(Order $order) {
        event(new OrderPaid($order));
    }
}
