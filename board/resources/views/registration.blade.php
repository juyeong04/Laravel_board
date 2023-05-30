{{-- login.blade.php에서 긁어옴 --}}
@extends('layout.layout')
@section('title', 'Login')

@section('contents')
    <h1>registration</h1>
    @include('layout.errorsvalidate')
    <form action="{{route('users.registration.post')}}" method="post">
        @csrf
        <label for="name">Name : </label>
        <input type="text" name="name" id="name">
        <label for="email">Email : </label>
        <input type="text" name="email" id="email">
        <label for="password">Password : </label>
        <input type="password" name="password" id="password">
        <label for="passwordchk">PasswordChk : </label>
        <input type="password" name="passwordchk" id="passwordchk">
        <br>
        <button type="submit">Registration</button>
        <button type="button" onclick="location.href = '{{route('users.login')}}'">cancel</button>
    </form>
@endsection