<?php

/**
 * UserAuthControllerTest
 * PHP version 8.1
 *
 * @category Test/Feature
 * @package  Tests\Feature\Controllers\API\v1\Auth
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */

namespace Tests\Feature\Controllers\API\v1\Auth;

use App\Enums\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

/**
 * Class UserAuthControllerTest
 *
 * @category Tests
 * @package  Tests\Feature\Controllers\API\v1\Auth
 * @author   Parth Gajjar <parthgajjar34@gmail.com>
 */
class UserAuthControllerTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @var array
     */
    private $requestStub;

    /**
     * The setUp function that arranges the tests
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStub = [
            'name'      =>  'Test User',
            'email'     =>  'test@mail.com',
            'password'  =>  '123456',
            'role_id'   =>  RolesEnum::Customer,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Tests register method create a customer
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testRegisterCustomer(): void
    {
        $response = $this->post("api/v1/register", $this->requestStub);
        $userData = User::where('email', $this->requestStub['email'])->first();

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
        $this->assertNotEmpty($userData);
        $this->assertSame($userData->role_id, RolesEnum::Customer);
    }

    /**
     * Tests register method create a lender
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testRegisterLender(): void
    {
        $this->requestStub['role_id'] = RolesEnum::Lender;
        $response = $this->post("api/v1/register", $this->requestStub);
        $userData = User::where('email', $this->requestStub['email'])->first();

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);
        $this->assertNotEmpty($userData);
        $this->assertSame($userData->role_id, RolesEnum::Lender);
    }

    /**
     * Tests Login User
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testLoginUser(): void
    {
        User::factory()->create([
            'email'    => $this->requestStub['email'],
            'password' => bcrypt($this->requestStub['password']),
        ]);

        $response = $this->post("api/v1/login", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => true]);;
    }

    /**
     * Tests Login Invalid User
     *
     * @author Parth Gajjar <parthgajjar34@gmail.com>
     * @return void
     */
    public function testLoginUserInvalid(): void
    {
        $response = $this->post("api/v1/login", $this->requestStub);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJsonFragment(["success" => false]);;
    }
}
