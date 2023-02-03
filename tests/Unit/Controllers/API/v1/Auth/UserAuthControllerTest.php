<?php

/**
 * UserAuthControllerTest
 * PHP version 8.1
 *
 * @category Test/Unit
 * @package  Tests\Unit\Controllers\API\v1\Auth
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Unit\Controllers\API\v1\Auth;

use App\Enums\RolesEnum;
use App\Models\User;
use App\Services\Authentication\AuthService;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Mockery;

/**
 * Class UserAuthControllerTest
 *
 * @category Tests
 * @package  Tests\Unit\Controllers\API\v1\Auth
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class UserAuthControllerTest extends TestCase
{
    use WithoutMiddleware;

    /**
     * @var array
     */
    private $requestStub;
    /**
     * @var User|Mockery\MockInterface
     */
    private $userMock;

    /**
     * @var AuthService|Mockery\MockInterface
     */
    private $authServiceMock;

    /**
     * @var string
     */
    private $tokenStub;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->userMock = Mockery::mock(User::class);
        $this->app->instance(User::class, $this->userMock);

        $this->authServiceMock = Mockery::mock(AuthService::class);
        $this->app->instance(AuthService::class, $this->authServiceMock);

        $this->requestStub = [
            'name'      =>  'Test User',
            'email'     =>  'test@mail.com',
            'password'  =>  '123456',
            'role_id'   =>  RolesEnum::Customer,
        ];

        $this->tokenStub = md5(time());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests register sends validation errors
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testRegisterValidationFailed(): void
    {
        $this->userMock->shouldReceive('create')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/register", []);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests register method create a user
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testRegister(): void
    {
        $this->userMock->shouldReceive('create')->once();
        $response = $this->mockMiddleware()->post("api/v1/register", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
    }

    /**
     * Tests register throws an exception
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testRegisterThrowsAnException(): void
    {
        $this->userMock->shouldReceive('create')
            ->once()
            ->andThrow(\Exception::class);
        $response = $this->mockMiddleware()->post("api/v1/register", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests login sends validation errors
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testLoginValidationFailed(): void
    {
        $this->authServiceMock->shouldReceive('authUserByEmailAndPassword')->times(0);
        $response = $this->mockMiddleware()->post("api/v1/login", []);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests login successfully authenticate user and returns auth token
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testLogin(): void
    {
        $this->authServiceMock->shouldReceive('authUserByEmailAndPassword')
            ->once()
            ->andReturn($this->tokenStub);
        $response = $this->mockMiddleware()->post("api/v1/login", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true, 'data' => ['token' => $this->tokenStub]]);
    }

    /**
     * Tests login successfully throws an exception
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testLoginThrowsAnException(): void
    {
        $this->authServiceMock->shouldReceive('authUserByEmailAndPassword')
            ->once()
            ->andThrow(\Exception::class);
        $response = $this->mockMiddleware()->post("api/v1/login", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Tests login doesn't authenticate user and return auth token
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testLoginFailed(): void
    {
        $this->authServiceMock->shouldReceive('authUserByEmailAndPassword')
            ->once()
            ->andReturn(null);
        $response = $this->mockMiddleware()->post("api/v1/login", $this->requestStub);
        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);
    }

    /**
     * Mock Middleware
     * @return UserAuthControllerTest
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     */
    private function mockMiddleware(): UserAuthControllerTest
    {
        $user = User::factory()->make(['id' => 1]);
        return $this->withoutMiddleware(
            [
                CheckForMaintenanceMode::class,
            ]
        )
            ->actingAs($user, 'api');
    }
}
