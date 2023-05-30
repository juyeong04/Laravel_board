<?php
// ! 제일 상단에 '이력 남기기!!'
//! 기존 소스코드 무조건 남겨둔다!!!!!!!!!!!!!!!!
//! 코드 리뷰 다같이 하고 난 다음 version 업 해야함 원래는...
// 근데 2차는 v001로 하고 3차는 v002로 하라고 했음 쌤이...
/******************************
 * 프로젝트명   : laravel_board
 * 디렉토리     : Controllers
 * 파일명       : BoardsController.php 
 * 이력         : v001 0526 주영 (new)
 *                v002 0530 주영 (유효성 체크 추가)
 ******************************/

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Boards;

use Illuminate\Support\Facades\Validator; // validator 사용

class BoardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $result = Boards::all(); ==> 다 불러오는것 보다 필요한거 불러오는게 더 효율적!
        $result = Boards::select(['id', 'title', 'hits', 'created_at', 'updated_at'])
                ->orderBy('hits', 'desc')
                ->get();
        return view('list')->with('data', $result); // with로 view에 보내주기
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('write');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {

        // v002 add start :유효성 체크
        $req->validate([
             //! required : 필수입력사항 (라라벨에서 지원해주는 유효성 체크 사용해서 만듦)
             //! 최소, 최대글자 설정 : min max사용 또는 between 사용
             // 체크하고 자동으로 redirect됨!
            'title' => 'required|between:3,30'
            ,'content' => 'required|max:2000'
        ]);

        // v002 add end

        //! DB에 질의하는게 아니라 insert라서 새로운 엘로퀀트 객체를 생성해서 사용해야하기 때문에 'new' 사용해줌!
        $boards = new Boards([
            'title' => $req->input('title')
            , 'content' => $req->input('content')
        ]);
        $boards->save(); // insert
        return redirect('/boards');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // 디테일 페이지
        //! find()
        $boards = Boards::find($id);// 해당 id에 맞는 정보 가져옴
        $boards->hits++;
        $boards->save();

        return view('detail')->with('data', Boards::findOrFail($id)); //findOfFail은 예외 처리 자동으로 해줌
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $boards = Boards::find($id);
        return view('edit')->with('data', $boards); // find()는 예외 발생하면 false로 와서 error 셋팅 설정 해줘야함
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //* id값 validate도 해야됨! : 개발자 모드에서 사용자가 id값에 공격스크립트 넣을수 있어서 막아야함!(숫자만 가능하게)
        // v002 add start
        // ID를 리퀘스트객체에 머지
        $arr = ['id' => $id];
        // $request->merge($arr); // title, content랑 한꺼번에 체크하려고 merge시킴
        $request->request->add($arr); // merge보다 더 빠른 방법
        // v002 add end


        // 유효성 검사 방법 1
            $request->validate([
                'title' => 'required|between:3,30'
                ,'content' => 'required|max:2000'
                ,'id' => 'required|integer' // v002 add //! numeric은 완전 숫자만, integer는 문자형이더라도 정수값을 가지는지 확인함
            ]); //==> 에러나면 바로 리턴, 리다이렉트

        // 유효성 검사 방법 2
            // $validator = Validator::make(
            //     $request->only('id', 'title', 'content')
            //     , [
            //         'title' => 'required|between:3,30'
            //         ,'content' => 'required|max:2000'
            //         ,'id' => 'required|integer'
            //     ]
            // ); //==> 에러나면 바로 리턴하지 않고, 에러값 담음

            // if($validator->fails()) {
            //     return redirect()
            //             ->back()
            //             ->withErrors($validator)
            //             ->withInput($request->only('title', 'content')); // request에 있는 모든 정보 session에 저장
            // }



        // 내가 하다가 실패한거
        // $boards = new Boards([
        //     'title' => $req->input('title')
        //     , 'content' => $req->input('content')
        // ]);
        // $boards->save();
        // return view('detail', ['boards' => $id])->with('data', $boards);

        // $boards = Boards::find($id);
        // $arronly = $request->only(['title', 'content']);
        // $boards->update(['title' => $arronly['title']
        //                 ,'content' => $arronly['content']
        //                 ]);
        // $boards->save();
        // return view('detail', ['boards' => $id])->with('data', $boards);

        //--------------------------------------------------------------
        //! return 할때 detail 페이지로 넘어가면 show랑 update url이 같게됨(안바뀜).... ==> redirect 해줘야함 / Url이 바뀐다??? ==> redirect!!!!!!
        // ! 모델 객체 사용했으면(Board) : orm / 사용 안했으면 (DB) : 쿼리빌더

        $result = Boards::find($id);
        $result->title = $request->title;
        $result->content = $request->content;
        $result->save();

        // * 중간에 문제생기면 db에는 갱신이 안되고 rollback 상태임 $result에는 update하고 싶은 데이터가 들어있음 
        // * ==> 그래서 db에서 새로 select해서 가져와야함
        // 방법1
        // return redirect('/boards/'.$id);
        // 방법2
        return redirect()->route('boards.show',['board' => $id]);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 방법1
        // Boards::destroy($id);

        // 방법2
        // $board = Boards::find($id);
        // $board->delete();
        Boards::find($id)->delete(); //! 객체(Boards::find($id))를 먼저 받고 delete() 써야함, delete 받는게 없기 때문
        //! orm 쓰면 softdelete 사용할 수 있음!! / orm 안쓰면 DB::update() 사용해서 해야함(플래그 관리), DB::delete() 하면 레코드 물리적 삭제됨!
        return redirect('/boards');
        // $boards = Boards::withTrashed($id)->get();

    }
}
