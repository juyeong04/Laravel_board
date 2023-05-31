@extends('layout.layout')

@section('title', 'Login')

@section('contents')
    <h1>login</h1>
    @include('layout.errorsvalidate')
    {{-- <div>{{isset($success) ? $success : ""}}</div> --}}
    <div>{!! session()->has('success') ? session('success') : "" !!}</div>
    {{-- 중괄호 대신 !! 쓰면 특수문자 이스케이프 안해줘서 br태그 먹음! but 보안상 취약할수 있음 --}}
    <form action="{{route('users.login.post')}}" method="post">
        @csrf
        <label for="email">Email : </label>
        <input type="text" name="email" id="email">
        <label for="password">Password : </label>
        <input type="password" name="password" id="password">
        <br>
        <button type="submit">Login</button>
        <button type="button" onclick="location.href = '{{route('users.registration')}}'">Registration</button>
    </form>
@endsection