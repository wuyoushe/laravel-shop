<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use App\Models\CartItem;
use App\Models\ProductSku;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        //with()方法来预加载购物车里的商品和SKU信息
        $cartItems = $this->cartService->get();
//        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return view('cart.index', ['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));
//        $user = $request->user();
//        $skuId = $request->input('sku_id');
//        $amount = $request->input('amount');
//
//        //从数据库中查询该商品是否已经在购物车中
//        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()){
//            //如果存在则直接叠加商品数量
//            $cart->update([
//                'amount'    => $cart->amount + $amount,
//            ]);
//        }else{
//            //创建一个新的购物车记录
//            $cart = new CartItem(['amount' => $amount]);
//            $cart->user()->associate($user);
//            $cart->productSku()->associate($skuId);
//            $cart->save();
//        }

        return [];
    }

    public function remove(ProductSku $sku, Request $request)
    {
        $this->cartService->remove($sku->id);
//        $request->user()->cartItems()->where('product_sku_id', $sku->id)->delete();

        return [];
    }
}
