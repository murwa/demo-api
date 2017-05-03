<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Class AuthTest
 *
 * @package Tests\Feature
 */
class AuthTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * @var
     */
    protected $user;

    /**
     * @var array
     */
    protected $data = [
        'pin'      => 9876,
        'username' => 'darrasa',
    ];

    /**
     * Setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create(array_merge($this->data, ['pin' => bcrypt(array_get($this->data, 'pin'))]));
    }

    /**
     * @test
     *
     * Test: POST /auth
     */
    public function it_should_auth_a_user_using_a_pin()
    {
        $this->post('api/auth', $this->data)
            ->assertStatus(200)
            ->assertJsonStructure(['token']);
    }
}
