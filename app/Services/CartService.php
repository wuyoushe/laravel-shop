<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/11/11 0011
 * Time: 22:21
 */

namespace App\Services;


use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService
{
    public function get()
    {
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId, $amount)
    {
        $user = Auth::user();
        //从数据库中查询该商品是否已经在购物车中
        if($item = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            //如果存在则直接叠加商品数量
            $item->update([
                'amount'    => $item->amount + $amount,
            ]);
        } else {
            //否则创建一个新的购物车记录
            $item = new CartItem(['amount' => $amount]);
            $item->user()->associate($user);
            $item->productSku()->associate($skuId);
            $item->save();
        }

        return $item;
    }

    public function remove($skuIds)
    {
        //可以传单个ID，也可以传ID数组
        if(!is_array($skuIds)) {
            //如果传入的不是数组，那么就转换为数组
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
    }

}



















