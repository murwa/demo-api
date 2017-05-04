<?php

namespace Tests\Feature;

use App\Account;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use JWTAuth;

/**
 * Class AccountTest
 *
 * @package Tests\Feature
 */
class AccountTest extends TestCase
{
    # use DatabaseMigrations;

    /**
     * @var \App\User
     */
    protected $user;
    /**
     * @var \App\Account
     */
    protected $account;

    /**
     * @var string
     */
    protected $token;

    /**
     * Setup
     */
    protected function setUp()
    {
        parent::setUp();

        # Seed DB
        $this->seed('UserTableSeeder');
        $this->user = User::inRandomOrder()->first();
        $this->account = $this->user->accounts()->inRandomOrder()->first();
        $this->token = JWTAuth::fromUser($this->user);
    }

    /**
     * @test
     *
     * Test: GET /accounts/{account}/balance
     */
    public function it_should_get_account_balance()
    {
        $this->get('api/accounts/' . $this->account->getRouteKey() . '/balance', ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'url',
                    'amount',
                ],
            ]);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/deposit
     */
    public function it_should_deposit_into_an_account()
    {
        $amount = $this->account->amount;
        $deposit = random_int(0, pow(10, 10));
        $response = $this->post('api/accounts/' . $this->account->getRouteKey() . '/deposit', ['amount' => $deposit], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'url',
                    'amount',
                ],
            ]);
        $balance = $amount + $deposit;
        $this->assertEquals($balance, $response->baseResponse->original->amount);
        $this->assertDatabaseHas('accounts', [
            'url'    => $this->account->url,
            'amount' => $balance * 100,
        ]);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/withdraw
     */
    public function it_should_withdraw_from_an_account()
    {
        $amount = $this->account->amount;
        $withdrawal = pow(10, 1);
        $response = $this->post('api/accounts/' . $this->account->getRouteKey() . '/withdraw', ['amount' => $withdrawal], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'url',
                    'amount',
                ],
            ]);
        $balance = $amount - $withdrawal;
        $this->assertEquals($balance, $response->baseResponse->original->amount);
        $this->assertDatabaseHas('accounts', [
            'url'    => $this->account->url,
            'amount' => $balance * 100,
        ]);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/withdraw
     */
    public function it_should_withdraw_positive_amounts_from_an_account()
    {
        $withdrawal = -10;
        $this->post('api/accounts/' . $this->account->getRouteKey() . '/withdraw', ['amount' => $withdrawal], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(412);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/withdraw
     */
    public function it_should_not_allow_negative_balances()
    {
        $withdrawal = pow(10, 5);
        $this->post('api/accounts/' . $this->account->getRouteKey() . '/withdraw', ['amount' => $withdrawal], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(412);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/transfer
     */
    public function it_should_transfer_from_an_account_to_a_different_account()
    {
        $to = Account::where('url', '!=', $this->account->url)->inRandomOrder()->first();
        $withdrawal = pow(10, 1);
        $response = $this->post('api/accounts/' . $this->account->getRouteKey() . '/transfer', [
            'amount'  => $withdrawal,
            'account' => $to->url,
        ], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'url',
                    'amount',
                ],
            ]);
        $balanceFrom = $this->account->amount - $withdrawal;
        $balanceTo = $to->amount + $withdrawal;
        $this->assertEquals($balanceFrom, $response->baseResponse->original->amount);
        $this->assertDatabaseHas('accounts', [
            'url'    => $this->account->url,
            'amount' => $balanceFrom * 100,
        ]);
        $this->assertDatabaseHas('accounts', [
            'url'    => $to->url,
            'amount' => $balanceTo * 100,
        ]);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/transfer
     */
    public function it_should_not_transfer_to_same_account()
    {
        $to = $this->account;
        $withdrawal = pow(10, 1);
        $this->post('api/accounts/' . $this->account->getRouteKey() . '/transfer', [
            'amount'  => $withdrawal,
            'account' => $to->url,
        ], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(412);
    }

    /**
     * @test
     *
     * Test: POST /accounts/{account}/transfer
     */
    public function it_should_not_transfer_to_non_existing_account()
    {
        $withdrawal = pow(10, 1);
        $this->post('api/accounts/' . $this->account->getRouteKey() . '/transfer', [
            'amount'  => $withdrawal,
            'account' => 'asdfasdfasdf',
        ], ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(422);
    }

    /**
     * @test
     *
     * Test: GET /accounts
     */
    public function it_should_list_user_accounts()
    {
        $this->get('api/accounts', ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'url',
                        'amount',
                    ],
                ],
            ]);
    }

    /**
     * @test
     *
     * Test: GET /accounts/{account}
     */
    public function it_should_get_an_account_given_url()
    {
        $this->get('api/accounts/' . $this->account->getRouteKey(), ['Authorization' => 'Bearer ' . $this->token])
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'url',
                    'amount',
                ],
            ]);

    }
}
