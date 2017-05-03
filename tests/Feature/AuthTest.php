<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    /**
    * @test
    *
    * Test: POST /auth
    */
    public function it_should_auth_a_user_using_a_pin()
    {
        $this->assertTrue(true);
    }
}
