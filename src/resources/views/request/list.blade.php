<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>申請一覧</title>
    <link rel="stylesheet" href="{{ asset('css/request.css') }}">
</head>

<body>

<header class="header">
    <div class="header-inner">

        <img src="{{ asset('images/logo.png') }}" class="logo">

        <nav class="nav">

        @if(auth()->guard('admin')->check())
            <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
            <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
            <a href="{{ route('admin.request.list') }}">申請一覧</a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="nav-button">ログアウト</button>
            </form>

        @else
            <a href="{{ route('attendance.index') }}">勤怠</a>
            <a href="{{ route('attendance.list') }}">勤怠一覧</a>
            <a href="{{ route('request.list') }}">申請</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-button">ログアウト</button>
            </form>
        @endif

        </nav>

    </div>
</header>

<main class="main">

    <h1 class="title">申請一覧</h1>

    @if (session('message'))
        <div class="flash-message">
            {{ session('message') }}
        </div>
    @endif

    <div class="tab-menu">

        <a href="?status=pending"
           class="{{ $status === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>

        <a href="?status=approved"
           class="{{ $status === 'approved' ? 'active' : '' }}">
            承認済み
        </a>

    </div>

    <table class="request-table">

        <thead>
            <tr>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($requests as $request)

            <tr>
                <td>
                    {{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}
                </td>

                <td>
                    {{ $request->attendance->user->name }}
                </td>

                <td>
                    {{ $request->attendance->date->format('Y/m/d') }}
                </td>

                <td>
                    {{ $request->request_data['note'] ?? '（未入力）' }}
                </td>

                <td>
                    {{ $request->requested_at->format('Y/m/d') }}
                </td>

                <td>
                    <a href="{{ auth('admin')->check()
                        ? route('admin.request.show', $request->id)
                        : route('request.show', $request->id) }}"
                        class="detail-link">
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