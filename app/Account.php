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
}
