<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boards;

class ApiListController extends Controller
{
    function getlist($id) { //어떤데이터 보내줄지 결정해서 만들어야함(데이터 0건 나왔을 때는 ~~)
        $board = Boards::find($id);
        return response()->json([$board], 200);
    }

    function postlist(Request $req) {
        // TODO 유효성 체크 필요

        $boards = new Boards([
            'title' => $req->title
            , 'content' => $req->content
        ]);
        $boards->save(); // insert

        // 원래는 이렇게 데이터 가공해서 보내줘야함
        $arr['errorcode'] = 0;
        $arr['msg'] = 'success';
        $arr['data'] = $boards->only('id', 'title');

        // return response()->json($boards, 200);
        return $arr; // 라라벨이 자동으로 header에 들어가는 정보 수정해줌, 배열형태로 넣어도 json 형태로 자동 변환 해줌
        
    }

    function putlist($id) {
        // 배열로 만들어서 바꿔야하나...?
    }
}
