{{-- 라라벨에서 저장돼있는 $errors 라는 객체 사용함 --}}
@if(count($errors) > 0)
        @foreach($errors->all() as $err)
            <div>{{$err}}</div>
        @endforeach
@endif