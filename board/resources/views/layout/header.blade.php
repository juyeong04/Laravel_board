<h2>header</h2>

{{-- @auth 인증된 유저만 나타남(로그인중) --}}
@auth
    <div><a href="{{route('users.logout')}}">로그아웃</a></div>
    <div><a href="{{route('users.edit')}}">회원정보수정</a></div>
@endauth

{{-- 비로그인 상태 --}}
@guest
    <div><a href="{{route('users.login')}}">로그인</a></div>
@endguest
<hr>
