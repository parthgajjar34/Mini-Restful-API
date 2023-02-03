<?php

/**
 * AuthServiceTest
 * PHP version 8.1
 *
 * @category Test/Unit
 * @package  Tests\Unit\Services\Authentication
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Unit\Services\Authentication;

use App\Models\User;
use App\Services\Authentication\AuthService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Class AuthServiceTest
 *
 * @category Tests
 * @package  Tests\Unit\Services\Authentication
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class AuthServiceTest extends TestCase
{
    /**
     * @var mixed
     */
    private $service;

    /**
     * @var string
     */
    private $emailStub;

    /**
     * @var string
     */
    private $passwordStub;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(AuthService::class);
        $this->emailStub = 'test@mail.com';
        $this->passwordStub = '123456';
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests authUserByEmailAndPassword send empty response upon invalid email or password
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testAuthUserByEmailAndPasswordReturnNull(): void
    {
        Auth::shouldReceive('attempt')->once()->andReturn(false);
        $result = $this->service->authUserByEmailAndPassword(
            $this->emailStub,
            $this->passwordStub
        );
        $this->assertEmpty($result);
    }

    /**
     * Tests authUserByEmailAndPassword sends valid token upon successful authentication
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testAuthUserByEmailAndPassword(): void
    {
        Auth::shouldReceive('attempt')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn(User::factory()->make(['id' => 123]));

        $result = $this->service->authUserByEmailAndPassword(
            $this->emailStub,
            $this->passwordStub
        );
        $this->assertNotEmpty($result);
    }
}
