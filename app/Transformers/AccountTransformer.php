<?php
/**
 * Created by PhpStorm.
 * User: mxgel
 * Date: 5/3/17
 * Time: 9:09 PM
 */

namespace App\Transformers;


use App\Account;
use League\Fractal\TransformerAbstract;

/**
 * Class AccountTransformer
 *
 * @package App\Transformers
 */
class AccountTransformer extends TransformerAbstract
{
    /**
     * @param \App\Account $account
     *
     * @return array
     */
    public function transform(Account $account)
    {
        return $account->toArray();
    }
}