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
 * @Resource("User Account", uri="/accounts")
 */
class AccountController extends Controller implements AccountContract
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
     * Balance
     *
     * Get the account balance.
     *
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     * @Get("/{account}/balance")
     */
    public function balance(Account $account): Response
    {
        return $this->itemResponse($account);
    }

    /**
     * Deposit
     *
     * Add money to the account
     *
     * @param \App\Http\Requests\DepositRequest $request
     *
     * @param \App\Account                      $account
     *
     * @return \Dingo\Api\Http\Response
     * @Post("/{account}/deposit")
     */
    public function deposit(DepositRequest $request, Account $account): Response
    {
        $account->fill(['amount' => $account->amount + $this->request->amount * 1])->save();

        return $this->itemResponse($account);
    }

    /**
     * Withdrawal
     *
     * Withdraw from the Account. The amount to withdraw must be a positive integer, and not more than the available
     * balance
     *
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     * @Post("/{account}/withdraw")
     */
    public function withdraw(Account $account): Response
    {
        # Do we have enough cash
        $this->canWithdraw($account);
        $account->fill(['amount' => $account->amount - $this->request->amount * 1])->save();

        return $this->itemResponse($account);
    }

    /**
     * Transfer
     *
     * Move money from Account A to another account B. The amount to be transfered from Account A must be lower than
     * the balance in A, and be positive amount
     *
     * @param \App\Http\Requests\TransferRequest $request
     *
     * @param \App\Account                       $account
     *
     * @return \Dingo\Api\Http\Response
     * @Post("/{account}/transfer")
     *
     */
    public function transfer(TransferRequest $request, Account $account): Response
    {
        $this->canWithdraw($account);
        $to = Account::whereUrl($request->account)->first();
        if ($to) {
            if ($to->url === $account->url) {
                throw new ATMException('You cannot transfer to the same account');
            }
            \DB::transaction(function () use ($to, &$account) {
                $amount = $this->request->amount * 1;
                $to->fill(['amount' => $to->amount + $amount])->save();
                $account->fill(['amount' => $account->amount - $amount])->save();
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

    /**
     * User Accounts
     *
     * List user's accounts
     *
     * @return \Dingo\Api\Http\Response
     * @Get("/")
     */
    public function index()
    {
        $accounts = $this->user()->accounts;

        return $this->response()->collection($accounts, new AccountTransformer());
    }

    /**
     * Account
     *
     * Get a single account by account number
     *
     * @param \App\Account $account
     *
     * @return \Dingo\Api\Http\Response
     * @Get("/{account}")
     */
    public function show(Account $account)
    {
        return $this->itemResponse($account);
    }
}
