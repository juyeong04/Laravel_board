{{-- 라라벨에서 저장돼있는 $errors 라는 객체 사용함 --}}
{{-- validate 체크할때 발생하는 에러 --}}
@if(count($errors) > 0)
        @foreach($errors->all() as $err)
            <div>{{$err}}</div>
        @endforeach
@endif

{{-- 나머지 설정한 에러들 --}}
@if(session()->has('error'))
    <div>{!!session('error')!!}</div>
@endif