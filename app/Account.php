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
        $this->attributes['amount'] = round(($amount ?: 0) * 100);
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
}
