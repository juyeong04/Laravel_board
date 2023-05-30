<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write</title>
</head>
<body>
    {{-- 에러메세지 include --}}
    @include('layout.errorsvalidate')


    <form action="{{route('boards.store')}}" method="post">
        @csrf
        <label for="title">제목 : </label>
        <input type="text" name="title" id="title" value="{{old('title')}}"> 
        {{-- name 속성이 old(직전 세션안에 있는 값 있으면 찾아서 출력해줌, 없으면 빈값 출력)안에 프로퍼티로 들어가야함 --}}
        <br>
        <label for="content">내용 : </label>
        <textarea name="content" id="content">{{old('content')}}</textarea>
        <br>
        <button type="submit">작성</button>
    </form>
</body>
</html>