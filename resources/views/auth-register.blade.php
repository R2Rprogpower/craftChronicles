@extends('layouts.master-without-nav')

@section('title', 'Create account')

@section('body')
    <body>
@endsection

@section('content')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="bg-primary-subtle p-4">
                            <h4 class="text-primary mb-2">Family Chronicles</h4>
                            <p class="mb-0">Create your private account to start collecting family stories.</p>
                        </div>

                        <div class="card-body p-4">
                            @if ($errors->any())
                                <div class="alert alert-danger" role="alert">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('register.submit', [], false) }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input id="name" name="name" type="text" class="form-control"
                                        value="{{ old('name') }}" autocomplete="name" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input id="email" name="email" type="email" class="form-control"
                                        value="{{ old('email') }}" autocomplete="email" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" name="password" type="password" class="form-control"
                                        minlength="12" autocomplete="new-password" required>
                                    <div class="form-text">At least 12 characters.</div>
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Repeat password</label>
                                    <input id="password_confirmation" name="password_confirmation" type="password"
                                        class="form-control" minlength="12" autocomplete="new-password" required>
                                </div>

                                <div class="d-grid">
                                    <button class="btn btn-primary" type="submit">Create account and continue</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <p class="text-center">Already have an account?
                        <a href="{{ route('login', [], false) }}">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
