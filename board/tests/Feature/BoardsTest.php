<?php

namespace Tests\Feature;

use App\Models\Boards;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BoardsTest extends TestCase
{
    //! 테스트 파일 생성 : php artisan make:test BoardsTest
    //! 이름 끝이 Test로 '끝날'것!!

    use RefreshDatabase; // 트랜잭션 시작 롤백, 테스트 완료 후 DB초기화를 위한 트레이트(class안에서 사용하는 객체)
    use DatabaseMigrations; // 테스트용 DB 마이그레이션

    /**
     * A basic feature test example.
     *
     * @return void
     */
    //! '메소드'는 test로 '시작'해야 작동함!!!!
    public function test_index_게스트_리다이렉트()
    {
        $response = $this->get('/boards');

        $response->assertRedirect('/users/login');
    }

    public function test_index_유저인증() {
        // 테스트용 유저 생성
        $user = new User([
            'email' => 'aa@aa.aa'
            ,'name' =>'테스트'
            ,'password' => 'sasdfsf'
        ]);
        $user->save();
        $response = $this->actingAs($user)->get('/boards');
        $this->assertAuthenticatedAs($user);
    }

    public function test_index_유저인증_뷰반환() {
        // 테스트용 유저 생성
        $user = new User([
            'email' => 'aa@aa.aa'
            ,'name' =>'테스트'
            ,'password' => 'sasdfsf'
        ]);
        $user->save();
        $response = $this->actingAs($user)->get('/boards');
        $response->assertViewIs('list');
    }

    public function test_index_유저인증_뷰반환_데이터확인() {
        // 테스트용 유저 생성
        $user = new User([
            'email' => 'aa@aa.aa'
            ,'name' =>'테스트'
            ,'password' => 'sasdfsf'
        ]);
        $user->save();

        $board1 = new Boards([
            'title' => 'test1'
            ,'content' => 'content1'
        ]);
        $board1->save();
        $board2 = new Boards([
            'title' => 'test2'
            ,'content' => 'content2'
        ]);
        $board2->save();

        $response = $this->actingAs($user)->get('/boards');

        $response->assertViewHas('data'); // assertViewHas() : 뷰 안에 data라는 키가 있는지 확인
        $response->assertSee('test1'); // assertSee() : response 안에 문자열 있는지 확인
        $response->assertSee('test2');
    }


}
