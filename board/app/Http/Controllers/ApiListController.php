<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boards;

use Illuminate\Support\Facades\Validator; // validator 사용

class ApiListController extends Controller
{
    function getlist($id) { //어떤데이터 보내줄지 결정해서 만들어야함(데이터 0건 나왔을 때는 ~~)
        $board = Boards::find($id);
        return response()->json([$board], 200);
    }

    function postlist(Request $req) {

        //! post, put Url에 보낼 값 넣으면 안됨!!, body에 넣어서 보내줘야함(input으로 넣은 데이터가 됨)

        //* 유효성 체크
        $validate = Validator::make($req->only('title', 'content'), [
                    'title' => 'required|string'
                    ,'content' => 'required|string'
                ]
            );

        if($validate->fails()) {
            return response()->json([
                'errorcode' => 1
                ,'msg' => 'validation fail'
                ,'errors' => $validate->errors()->all()
            ], 400);
        }

        //--------------------
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

    function putlist(Request $request, $id) {

        // //* 유효성 검사
        // $validate = Validator::make($request->only('title', 'content'), [
        //     'title' => 'required|string'
        //     ,'content' => 'required|string'
        // ]);

        // if($validate->fails()) {
        //     return response()->json([ // response jason으로 안넘기고 배열로 넘기려면 위에 배열 기본값 설정해주고 return 하면 됨
        //         'errorcode' => 1
        //         ,'msg' => 'validation fail'
        //         ,'errors' => $validate->errors()->all()// -> all() : 에러 메세지만 가져오기
        //     ], 400);
        // }

        // $boards = Boards::find($id);
        // if(!$boards) {
        //     return response()->json([
        //         'errorcode' => 1
        //         ,'msg' => 'Board not found'
        //     ], 404);
        // }

        // $boards->title = $request->title;
        // $boards->content = $request->content;
        // $boards->save();

        // $arr['errorcode'] = 0;
        // $arr['msg'] = 'success';
        // $arr['data'] = $boards->only('id', 'title');

        // return $arr;
        
        
            //** 쌤이 한 코드 **

            $arrData = [
                'code' => 0
                ,'msg' => ''
            ];

            $data = $request->only('title', 'content');
            $data['id'] = $id; // 세그먼트는 배열이 아니라서 따로 배열로 넣어주기 위해서

            //** 유효성 체크 **
            $validate = Validator::make($data, [
            'id' => 'required|integer|exists:boards,id'
            ,'title' => 'required|between:3,30'
            ,'content' => 'required|max:2000'
        ]);

            if($validate->fails()) {
                $arrData['code'] = 'E01';
                $arrData['msg'] = 'Validate Error';
                $arrData['errmsg'] = $validate->errors()->all();
                return $arrData;
            }
            else {
                // 업데이트 처리 
                $boards = Boards::find($id);
                if($boards) { // id 값 있으면
                    $boards->title = $request->title;
                    $boards->content = $request->content;
                    $boards->save();
                    $arrData['code'] = 0; // 위 배열에 기본으로 세팅돼있기 때문에 다시 적어줄 필요 없는데 혹시나 해서 적어줌 
                    $arrData['msg'] = 'success';
                }
                else { // 이미 지운 경우
                    $arrData['code'] = 'E02';
                    $arrData['msg'] = 'Already Deleted';
                }
            }
            
            return $arrData;
    }


    function deletelist($id) {
        // $boards = Boards::find($id);
        // if(!$boards){
        //         return response()->json([
        //             'errorcode' => 1
        //             ,'msg' => 'Board not found'
        //         ], 404);
        // }
        // else {
        //     $boards->delete(); 
        //     $arr['errorcode'] = 0;
        //     $arr['msg'] = 'success';
        //     return $arr;
        // }

         //** 쌤코드 **
            $arrData = [
                'code' => 0
                ,'msg' => ''
            ];
            $data['id'] = $id;
            $validate = Validator::make($data, [
            'id' => 'required|integer|exists:boards,id' // id 컬럼값 안넣어주면 softdelete로 삭제된 데이터인지 아닌지 확인못함 
        ]);

        if($validate->fails()) {
                $arrData['code'] = 'E01';
                $arrData['msg'] = 'Validate Error';
                $arrData['errmsg'] = 'id not found';
        }
        else {
            $boards = Boards::find($id); 
            if($boards) { // id값 있으면
                $boards->delete(); 
                $arrData['errorcode'] = 0;
                $arrData['msg'] = 'success';
            }
            else { // 이미 지운 데이터인 경우
                $arrData['code'] = 'E02';
                $arrData['msg'] = 'Already Deleted';
            }
        
        }
        return $arrData;
        
    }
}
