<?php
/**
 * Created by PhpStorm.
 * User: mxgel
 * Date: 5/3/17
 * Time: 10:44 PM
 */

namespace App\Contracts;


use App\Account;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use Dingo\Api\Http\Response;

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
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     */
    public function balance(Account $account): Response;

    /**
     * Deposit money in the account
     *
     * @param \App\Http\Requests\DepositRequest $request
     *
     * @param \App\Account                      $account
     *
     * @return \Dingo\Api\Http\Response
     */
    public function deposit(DepositRequest $request, Account $account): Response;

    /**
     * Withdraw money from the account
     *
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     */
    public function withdraw(Account $account): Response;

    /**
     * Transfer money across accounts
     *
     * @param \App\Http\Requests\TransferRequest $request
     *
     * @param \App\Account                       $account
     *
     * @return \Dingo\Api\Http\Response|mixed
     */
    public function transfer(TransferRequest $request, Account $account): Response;
}