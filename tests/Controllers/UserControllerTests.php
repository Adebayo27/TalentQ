<?php

namespace Tests\Controllers;

use Illuminate\Http\Response;
use Tests\TestCase;

class UserControllerTests extends TestCase
{

    public function testIndexReturnsDataInValidFormat()
    {

        $this->json('get', 'api/user')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(
                [

                    '*' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'role',
                        'category_ids',
                        'otp',
                        'created_at',
                        'updated_at'
                    ]

                ]
            );
    }

    public function testUserIsCreatedSuccessfully()
    {

        $payload = [
            'name' => 'Jane Doe',
            'email' => 'janedoe@gmail.com',
            'password' => '12345',
            'c_password' => '12345',
            'role' => 'user'
        ];
        $this->json('post', 'api/register', $payload)
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(
                [
                    "success" => true,
                    'data' => [
                        'name',
                        'role',
                    ],
                    'message' => "User register successfully."
                ]
            );
        $this->assertDatabaseHas('users', $payload);
    }
}
