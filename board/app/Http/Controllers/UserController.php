<?php
/******************************
 * 프로젝트명   : laravel_board
 * 디렉토리     : Controllers
 * 파일명       : UserController.php 
 * 이력         : v001 0530 주영 (new)
 ******************************/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // 비밀번호 해쉬화
use Illuminate\Support\Facades\Auth; // 유저 인증 작업
use App\Models\User; //유저 정보 습득

class UserController extends Controller
{
    function login() {
        return view('login');
    }

    function loginpost(Request $req) {
        $req->validate([
            'email' => 'required|email|max:100'
            ,'password' => 'regex:/^(?=.*[A-Za-z])(?=.*[@$!%*#-^])(?=.*[0-9]).{8,20}$/'
        ]);

        // 유저정보 습득
        $user = User::where('email', $req->email)->first(); // 제일 첫번째거만 가져옴
        //쿼리문에서 where역할(where 'email' = $req->email)

        if(!$user || !(Hash::check($req->password, $user->password))) { // 유저가 없거나, 패스워드가 같지 않거나
            //! Hash::check() : 평문(암호화 하지 않은 데이터)이 주어진 해시와 일치하는지 확인합니다.
            $errors[] = '아이디와 비밀번호를 확인해주세요';
            return redirect()->back()->with('errors',collect($errors)); // errorsvalidate.blade.php에서 $errors->all() 쓸려고 collect써서 객체로 변환해줬음, collect안쓰면 all()못씀
        }

        // *유저 인증 작업
        Auth::login($user);
        if(Auth::check()) {
            // session에 올릴 수 있는 거는 아무 의미 없는 pk
            session([$user->only('id')]); // 세션에 인증된 회원pk 등록
            return redirect()->intended(route('boards.index')); // 필요없는 정보들 날려주면서 redirect 해줌
        }
        else {
            $errors[] = '인증작업 에러';//?????????????????????????
            return redirect()->back()->with('errors',collect($errors));
        }
    }


    // 파라미터 없음, 화면 띄워주는 애
    function registration() {
        return view('registration');
    }

    // 유저가 폼에 입력한 내용 들어있음
    function registrationpost(Request $req) { 
        //* 유효성 체크
        $req->validate([
            'name' => 'required|regex:/^[가-힣]+$/|min:2|max:30' // 한글만
            ,'email' => 'required|email|max:100'
            ,'password' => 'same:passwordchk|regex:/^(?=.*[A-Za-z])(?=.*[@$!%*#-^])(?=.*[0-9]).{8,20}$/' // 비교해서 똑같지 않으면 에러남 / required_with:passwordchk| 없어도 정규식에서 걸러줘서 빈값 넣어도 걸러줌
        ]);

        // $data['name'] = $req->input('name'); // 위, 아래 방법 동일함!
        $data['name'] = $req->name;
        $data['email'] = $req->email;
        $data['password'] = Hash::make($req->password); // 비밀번호 해쉬화

        $user = User::create($data); // insert되는 데이터가 변수에 담김
        if(!$user) {
            $errors[] = '시스템 에러가 발생하여, 회원가입에 실패했습니다.';
            $errors[] = '잠시후에 다시 회원가입을 시도해 주세요';
            return redirect()
                ->route('users.registration') // redirect 안에 넣어서 써도 됨
                ->with('errors', collect($errors)); // $errors[]가 일반 배열이기 때문에 collection 객체로 변환 필요함
        }

        //* 회원가입 완료, 로그인 페이지로 이동
        //TODO 내일!!
        return redirect()
                ->route('users.login')
                ->with('success', '화원가입을 완료했습니다<br>로그인 해주세요');

    }
}
