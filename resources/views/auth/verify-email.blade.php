@extends('layouts.app')

@section('title', 'Подтверждение Email - Business database')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center mb-4">Подтверждение Email</h3>

                <div class="alert alert-info">
                    <strong>Проверьте свою почту!</strong><br>
                    Мы отправили 6-значный код подтверждения на ваш email.
                    Код действителен в течение 10 минут.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.email.verify') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="code" class="form-label">Введите код подтверждения</label>
                        <input type="text"
                               class="form-control text-center @error('code') is-invalid @enderror"
                               id="code"
                               name="code"
                               placeholder="000000"
                               pattern="[0-9]{6}"
                               maxlength="6"
                               required
                               autocomplete="off"
                               autofocus
                               style="font-size: 24px; letter-spacing: 10px;">
                        <div class="form-text">6-значный код из письма</div>
                        @error('code')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Подтвердить</button>
                </form>

                <div class="mt-3 text-center">
                    <form method="POST" action="{{ route('verification.email.resend') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link">Отправить код повторно</button>
                    </form>
                </div>

                <div class="mt-3 text-center">
                    <p><a href="{{ route('login') }}">Вернуться к входу</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
