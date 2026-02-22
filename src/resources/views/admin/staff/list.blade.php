<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>スタッフ一覧</title>
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
</head>

<body>

<header class="header">
    <div class="header-inner">
        <img src="{{ asset('images/logo.png') }}" class="logo">

        <nav class="nav">
            <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
            <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
            <a href="{{ route('admin.request.list') }}">申請一覧</a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-button">ログアウト</button>
            </form>
        </nav>
    </div>
</header>

<main class="main">

<h1 class="title">スタッフ一覧</h1>

<table class="table-wrapper">
    <thead>
        <tr>
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
    </thead>

    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td class="detail">
                <a href="{{ route('admin.staff.attendance', $user->id) }}">
                    詳細
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</main>

</body>
</html>