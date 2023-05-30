<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique(); // pk는 보통 정수형, 문자열은 서버 부하 많이 걸림
            $table->string('password');// 최대 60자까지 라라벨이 암호화해줌
            $table->string('name');

            $table->timestamp('email_verified_at')->nullable(); // 이메일 인증된 회원 갱신됨(지금 수업때는 안쓸거임)
            $table->rememberToken(); // 로그인 유지하기 기능, 엘로컨트 이용하면 자동으로 저장해서 유저 정보랑 비교해줌
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
