<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>会員登録</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}?v=1">
</head>

<body>

<header class="header">
    <img src="{{ asset('images/logo.png') }}" alt="COACHTECH ロゴ" class="logo" />
</header>

<main class="container">
    <h1 class="title">会員登録</h1>

    <form method="POST" action="{{ route('register') }}" class="login-form">
        @csrf

        <div class="form-group">
            <label for="name">名前</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
            />
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
            />
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input
                type="password"
                id="password"
                name="password"
            />
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">パスワード確認</label>
            <input
                type="password"
                id="password_confirmation"
                name="password_confirmation"
            />
        </div>

        <button type="submit" class="login-button">
            登録する
        </button>
    </form>

    <p class="form-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</main>

</body>
</html>