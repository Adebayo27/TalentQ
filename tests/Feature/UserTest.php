<?php

namespace Tests\Feature;

use Faker\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    
    protected $token = "Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiYWQ4ODk0OWJjNTAwZmVlMzAyZGNkNTEyZjZmM2Q1ODIwZjJhMzEyNzVmMzg0OTM3MDcyYTFjMjE2ZjQwNmU1MmU0ZmYyYTZjNTA5M2E3NmMiLCJpYXQiOjE2MTk4NjU0MjUuNzUyMTkxLCJuYmYiOjE2MTk4NjU0MjUuNzUyMiwiZXhwIjoxNjUxNDAxNDI1Ljc0MzMwOSwic3ViIjoiMSIsInNjb3BlcyI6W119.F4kNGpNzCCwsPooMC6CWVt-USkziXqeqFVCGuk458PVl8rq552PAwc2g8yrcFMoUYF8bT0jZoCk9ur-3E1Q88vimO7sZ8F0m14uOV1UwPgtHMTvGjdlJCNQXK0TgExa8FJ0sLuOy2Hx0d5I0wvbAXAeQ2nB9nzxyQyHssP8g1zp_ZACZ8shCzwSD6HVNg2vg8qNhq2QXSh8cAPuRMyhjq3KO5rCv_6DDaPkA9tf3QmtWuP2CywpqNkQ6uXDqnZkiswjntb_cXVbgSXXn5hP9bdbR1DylYnU3CZCc9Q-Po2_ulr-FOENbRPZwIl1-HF2_SniaZd_fYzvpXlksG7p6yb7gfuoLBP7vkpc3yItJbsjwOtjVQFscoTnQ-ZZ18EwBEmYiHvD7MPDhGaUy02B5-BhJgiI-dF9CsEB_nJlInELuGhAHyj5OHlKnAdYmwCZiZ6TVICfuTDQ-QNpGWIMWxOulsLXiBEUs__jyFj00xx_LzCaSyARXKD8KQxL6aSC8V7w1NYK5s9nlfJCOdZqf_2vj5Y231SiGJggrkRIrZZRTFXyBHLuq60frcJ7EgMfpX9kV6BFV2RkgBunjeMGGiZYgTPrRsM6f3IiRTzF8WUfqVLMhzOfVZdSnQanOy1k9dyrfV_Cz63EpGNP0CknGd_xclTbP17kg8jcha217HG0";
    
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
    

    public function testUserIsCreatedSuccessfully()
    {
        $faker = \Faker\Factory::create();
        $payload = [
            'name' => $faker->name(),
            'email' => $faker->email(),
            'password' => 'required',
            'role' => 'user'
        ];
        $response = $this->post('api/register', $payload);
        $response->assertStatus(200);
        
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = [
            'email' => 'jane@gmail.com',
            'password' => '12345',
        ];
        $response = $this->post('api/login', $user);
        $response->assertStatus(200);
        // $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_create_request()
    {
        $payload = [
            'user_id' => 1,
            'category_id' => 1,
            'description' => 'Description',
        ];
        
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->post('api/make_request', $payload);
        $response->assertStatus(200);
    }

    public function test_get_my_requests()
    {
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->get('api/get_my_requests');
        $response->assertStatus(200);
    }


    public function test_get_requests()
    {
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->get('api/get_requests');
        $response->assertStatus(200);
    }

    public function test_user_can_respond_to_request()
    {
        Storage::fake('avatars');
        $payload = [
            'photo_request_id' => 1,
            'user_id' => 1,
            'image' => UploadedFile::fake()->image('avatar.jpg'),
        ];
        
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->post('api/respond_to_request', $payload);
        $response->assertStatus(200);
    }

    public function test_view_response()
    {
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->get('api/view_response/1');
        $response->assertStatus(200);
    }

    public function test_users_can_accept_or_reject()
    {
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->get('api/view_response/1/2/accept');
        $response->assertStatus(200);
    }

    public function test_users_can_get_pending_requests()
    {
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->get('api/get_pending_responses');
        $response->assertStatus(200);
    }

    public function test_users_can_get_photos()
    {
        $response = $this->withHeaders([
            'Authorization'=> $this->token,
        ])->get('api/get_my_photos');
        $response->assertStatus(200);
    }
    

}
