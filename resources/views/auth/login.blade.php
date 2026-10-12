@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="login-card">
    <h1>Medical Equipment Inventory</h1>
    <p class="page-subtitle">Sign in to continue</p>

    @include('partials.flash')

    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div class="form-stack">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="field-error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
        </div>
        <div class="checkbox-row">
            <input type="checkbox" id="remember" name="remember" value="1">
            <label for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
    </form>
</div>
@endsection
