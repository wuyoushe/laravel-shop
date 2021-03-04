<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    use DefaultDatetimeFormat;
    //用常量的方式定义支持的优惠券类型
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];
    protected $casts = [
        'enabled' => 'boolean',
    ];

//模型中的 $casts 属性提供了一个便利的方法来将属性转换为常见的数据类型。
//$casts 属性应是一个数组，且数组的键是那些需要被转换的属性名称，值则是你希望转换的数据类型。
//支持转换的数据类型有： integer, real， float，double， decimal:<digits>，string, boolean， object, array，collection， date， datetime， 和 timestamp。
//当需要转换为 decimal 类型时，你需要定义小数位的个数，如： decimal:2
    protected $dates = ['not_before', 'not_after'];

    protected $appends = ['description'];

    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }

        return $str.'减'.str_replace('.00', '', $this->value);
    }

    public static function findAvailableCode($length = 16)
    {
        do {
            //生成一个指定长度的随机字符串，并转成大写
            $code = strtoupper(Str::random($length));
            //如果生成的码已经存在就继续循环
        }while(self::query()->where('code', $code)->exists());

        return $code;
    }


}
