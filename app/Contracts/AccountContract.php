<?php
/**
 * Created by PhpStorm.
 * User: mxgel
 * Date: 5/3/17
 * Time: 10:44 PM
 */

namespace App\Contracts;


use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;

/**
 * Interface AccountContract
 *
 * @package App\Contracts
 */
interface AccountContract
{
    /**
     * Get account balance
     *
     * @return \Dingo\Api\Http\Response
     */
    public function balance();

    /**
     * Deposit money in the account
     *
     * @param \App\Http\Requests\DepositRequest $request
     *
     * @return mixed
     */
    public function deposit(DepositRequest $request);

    /**
     * Withdraw money from the account
     *
     * @return mixed
     */
    public function withdraw();

    /**
     * Transfer money across accounts
     *
     * @param \App\Http\Requests\TransferRequest $request
     *
     * @return mixed
     */
    public function transfer(TransferRequest $request);
}