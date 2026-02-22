<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>管理者ログイン</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap"
        rel="stylesheet"
    />

    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
</head>

<body>

<header class="header">
    <img src="{{ asset('images/logo.png') }}" alt="COACHTECH ロゴ" class="logo" />
</header>

<main class="container">
    <h1 class="title">管理者ログイン</h1>

    <form method="POST" action="{{ route('admin.login.post') }}" class="login-form">
        @csrf

        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}">
            @error('email')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" id="password" name="password">
            @error('password')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="login-button">
            管理者ログインする
        </button>
    </form>
</main>
</body>
</html>
