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
use Illuminate\Support\Facades\Session; // 세션
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
            // $errors[] = '아이디와 비밀번호를 확인해주세요'; // 0531 del
            $error = '아이디와 비밀번호를 확인해주세요';
            // return redirect()->back()->with('errors',collect($errors)); // errorsvalidate.blade.php에서 $errors->all() 쓸려고 collect써서 객체로 변환해줬음, collect안쓰면 all()못씀 // 0531 del
            return redirect()->back()->with('error', $error); //! redirect 해서 session에 값 넣어줌
        }

        // *유저 인증 작업
        Auth::login($user);
        if(Auth::check()) {
            // session에 올릴 수 있는 거는 아무 의미 없는 pk
            session($user->only('id')); // 세션에 인증된 회원pk 등록
            return redirect()->intended(route('boards.index')); // 필요없는 정보들 날려주면서 redirect 해줌
        }
        else {
            $error = '인증작업 에러';//?????????????????????????
            return redirect()->back()->with('error', $error); 
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
            $error = '시스템 에러가 발생하여, 회원가입에 실패했습니다. 잠시후에 다시 회원가입을 시도해 주세요';
            return redirect()
                ->route('users.registration') // redirect 안에 넣어서 써도 됨
                ->with('error', $error); // $errors[]가 일반 배열이기 때문에 collection 객체로 변환 필요함
        }

        //* 회원가입 완료, 로그인 페이지로 이동
        return redirect()
                ->route('users.login')
                ->with('success', '화원가입을 완료했습니다<br>로그인 해주세요'); //! redirect 하면서 session에 저장됨

    }

    // 로그아웃
    function logout() {
        Session::flush(); // 세션 파기
        Auth::logout(); // 로그아웃
        return redirect()->route('users.login');
    }

    // 회원탈퇴
    function withdraw() { // 원래는 post로 받아서 request로 id값 받아와야함
        $id = session('id');
        $result = User::destroy($id); //! destroy 하다가 에러나면 에러처리 해줘야됨!!(trycatch 아니면 라라벨 errorhandling)
        // return var_dump(session()->all());
        Session::flush(); // 세션 파기
        Auth::logout(); // 로그아웃
        return redirect()->route('users.login');
    }

    // 회원정보 수정
    function useredit() {
        $id = session('id');
        $users = User::find($id);
        return view('useredit')->with('data', $users);
    }

    // function usereditpost(Request $req) {
    //     // Todo : success 하면 메세지 뜨게
    //     //* 유효성 체크
    //     $req->validate([
    //         'name' => 'required|regex:/^[가-힣]+$/|min:2|max:30' // 한글만
    //         ,'email' => 'required|unique:App\Models\User|email|max:100' // 이메일 유니크 체크!
    //         ,'password' => 'same:passwordchk|regex:/^(?=.*[A-Za-z])(?=.*[@$!%*#-^])(?=.*[0-9]).{8,20}$/'
    //     ]);

    //     if(!(Hash::check($req->password, Auth::User()->password))) {
    //         $error = '비밀번호를 수정해주세요';
    //         return redirect()->back()->with('error', $error); // redirect 돼서 old안먹음...

        
    //     $result = User::find(Auth::User()->id);
    //     // return var_dump(Auth::User());
    //     $result->name = $req->name;
    //     $result->email = $req->email;
    //     $result->password =  Hash::make($req->password);


    //     $result->save();
    //     Session::flush(); // 세션 파기
    //     Auth::logout(); // 로그아웃
    //     return redirect()->route('users.login'); // 로그인 페이지로 이동
    //     }
    // }

    //! 쌤 코드
    function editpost(Request $req) {
        $arrKey = []; // 수정할 항목을 배열에 담는 변수

        $baseUser = User::find(Auth::User()->id); // 기존 데이터 획득

        // 기존 패스워드 체크
        if(!Hash::check($req->bpassword, $baseUser->password)) {
            return redirect()->back()->with('error', '기존 비밀번호를 확인해 주세요.');
        }

        // 수정할 항목을 배열에 담는 처리 (값이 같지 않으면 수정된 값임!!)
        if($req->name !== $baseUser->name) {
            $arrKey[] = 'name';
        }
        if($req->email !== $baseUser->email) {
            $arrKey[] = 'email';
        }
        if(isset($req->password)) {
            $arrKey[] = 'password';
        }


        // 유효성-----------------------------------
        // 유효성체크를 하는 모든 항목 리스트 : 새로 넣은 값(bpassword : 그 전 확인용 비밀번호)
        $chkList = [
            'name'      => 'required|regex:/^[가-힣]+$/|min:2|max:30'
            ,'email'    => 'required|email|max:100'
            ,'bpassword'=> 'regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
            ,'password' => 'same:passwordchk|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        ];

        // 유효성 체크할 항목 셋팅하는 처리
        $arrchk['bpassword'] = $chkList['bpassword']; // 기본값 설정, $arrchk 배열 만들어줌
        foreach($arrKey as $val) {
            $arrchk[$val] = $chkList[$val]; // ex) $arrchk[name] = $chkList[name]
        }

        //유효성 체크
        $req->validate($arrchk); //validate '배열'로 체크
        // --------------------------------------------------


        // 수정할 데이터 셋팅
        foreach($arrKey as $val) {
            if($val === 'password') {
                $baseUser->$val = Hash::make($req->$val);
                continue; //! 밑에 다음거 실행 안하고 다시 루프로 돌아감
            }
            $baseUser->$val = $req->$val;
        }
        $baseUser->save(); // update

        return redirect()->route('users.edit');
    }

}
