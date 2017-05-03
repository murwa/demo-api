<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Account
 *
 * @package App
 */
class Account extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['amount'];

    /**
     * Boot
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Account $account) {
            $account->url = random_int(pow(10, 6), pow(10, 7) - 1);

            return $account;
        });
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param $amount
     */
    public function setAmountAttribute($amount)
    {
        $this->attributes['amount'] = intval(round(($amount ?: 0) * 100));
    }

    /**
     * @param $amount
     *
     * @return float|int
     */
    public function getAmountAttribute($amount)
    {
        return $amount ? $amount / 100 : $amount;
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'url';
    }
}
