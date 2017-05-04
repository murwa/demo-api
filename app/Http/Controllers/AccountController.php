<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contracts\AccountContract;
use App\Exceptions\ATMException;
use App\Exceptions\InsufficientFundsHttpException;
use App\Exceptions\MissingAmountHttpException;
use App\Http\Requests\DepositRequest;
use App\Http\Requests\TransferRequest;
use App\Transformers\AccountTransformer;
use Dingo\Api\Http\Response;
use Illuminate\Http\Request;

/**
 * Class AccountController
 *
 * @package App\Http\Controllers
 */
class AccountController extends Controller
{
    /**
     * @var \App\Account
     */
    protected $account;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @Resource("User Account", uri="/accounts")
     * AccountController constructor.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Account             $account
     */
    public function __construct(Request $request, Account $account)
    {
        $this->middleware('api.auth');
        $this->account = $account;
        $this->request = $request;
    }

    /**
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     */
    public function balance(Account $account): Response
    {
        return $this->itemResponse($account);
    }

    /**
     * @param \App\Http\Requests\DepositRequest $request
     *
     * @param \App\Account                      $account
     *
     * @return \Dingo\Api\Http\Response
     */
    public function deposit(DepositRequest $request, Account $account): Response
    {
        $account->increment('amount', $request->amount * 1);

        return $this->itemResponse($account);
    }

    /**
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     */
    public function withdraw(Account $account)
    {
        # Do we have enough cash
        $this->canWithdraw($account);
        $account->decrement('amount', $this->request->amount * 1);

        return $this->itemResponse($account);
    }

    /**
     * @param \App\Http\Requests\TransferRequest $request
     *
     * @param \App\Account                       $account
     *
     * @return \Dingo\Api\Http\Response
     *
     */
    public function transfer(TransferRequest $request, Account $account)
    {
        $this->canWithdraw($account);
        $to = Account::whereUrl($request->account)->first();
        if ($to) {
            if ($to->url === $account->url) {
                throw new ATMException('You cannot transfer to the same account');
            }
            \DB::transaction(function () use ($to, $account) {
                $amount = $this->request->amount * 1;
                $to->increment('amount', $amount);
                $account->decrement('amount', $amount);
            });

            return $this->itemResponse($account);
        }
        throw new ATMException('Cannot find specified account');
    }

    /**
     * @param \App\Account $account
     *
     * @return int
     */
    protected function canWithdraw(Account $account)
    {
        $amount = $this->request->amount;
        if ($amount <= 0) {
            throw new MissingAmountHttpException();
        }
        if ($account->amount * 100 < intval(round($amount * 100))) {
            throw new InsufficientFundsHttpException();
        }

        return true;
    }


    /**
     * @param \App\Account $account
     *
     * @return Response
     */
    protected function itemResponse(Account $account): Response
    {
        return $this->response()->item($account, new AccountTransformer());
    }
}
