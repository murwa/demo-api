<?php

namespace App\Http\Controllers;

use App\Account;
use App\Contracts\AccountContract;
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
class AccountController extends Controller implements AccountContract
{
    /**
     * @var \App\User
     */
    protected $user;

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
        $this->user = $this->user();
        $this->account = $account;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function balance(): Response
    {
        return $this->itemResponse($this->account);
    }

    /**
     * @param \App\Http\Requests\DepositRequest $request
     *
     * @return Response
     */
    public function deposit(DepositRequest $request): Response
    {
        $this->account->increment('amount', $request->amount * 1);

        return $this->itemResponse($this->account);
    }

    /**
     * @return \Dingo\Api\Http\Response
     */
    public function withdraw()
    {
        # Do we have enough cash
        $amount = $this->canWithdraw();
        $this->account->decrement('amount', $amount);

        return $this->itemResponse($this->account);
    }

    /**
     * @param \App\Http\Requests\TransferRequest $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function transfer(TransferRequest $request)
    {
        $amount = $this->canWithdraw();
        $account = Account::whereUrl($request->account)->first();
        \DB::transaction(function () use ($amount, $account) {
            $account->increment('amount', $amount);
            $this->account->decrement('amount', $amount);
        });

        return $this->itemResponse($this->account);
    }

    /**
     * @return int
     */
    protected function canWithdraw()
    {
        $amount = intval($this->request->amount * 100);
        if ($amount <= 0) {
            throw new MissingAmountHttpException();
        }
        if ($this->account->amount < $amount) {
            throw new InsufficientFundsHttpException();
        }

        return $amount;
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
