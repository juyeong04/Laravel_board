<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Boards;

class BoardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * 1
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
        return redirect('/boards/'.$id);
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
