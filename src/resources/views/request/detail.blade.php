<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>申請詳細</title>
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
</head>

<body>

<header class="header">
    <div class="header-inner">
        <img src="{{ asset('images/logo.png') }}" class="logo">

        <nav class="nav">

            @if(auth('admin')->check())

                <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
                <a href="{{ route('admin.request.list') }}">申請一覧</a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="nav-button">ログアウト</button>
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

@if ($errors->any())
    <div style="color:red; margin-bottom:15px;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<main class="main">

    <h1 class="title">申請詳細</h1>

    @if(session('message'))
        <p style="color:green; text-align:center;">
            {{ session('message') }}
        </p>
    @endif

    <div class="detail-wrapper">
        <table class="detail-table">

            <tr>
                <th>名前</th>
                <td>{{ $request->attendance->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>{{ $request->attendance->date->isoFormat('YYYY年M月D日') }}</td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    {{ $request->request_data['clock_in'] ?? '-' }}

                <span class="wave">〜</span>

                    {{ $request->request_data['clock_out'] ?? '-' }}
                </td>
            </tr>

            @php
                $breaks = $request->request_data['breaks'] ?? [];
            @endphp

            <tr>
                <th>休憩1</th>
                <td>
                    @if(isset($breaks[0]))
                        {{ $breaks[0]['start'] ?? '-' }} 〜 {{ $breaks[0]['end'] ?? '-' }}
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>休憩2</th>
                <td>
                    @if(isset($breaks[1]))
                        {{ $breaks[1]['start'] ?? '-' }} 〜 {{ $breaks[1]['end'] ?? '-' }}
                    @else
                        -
                    @endif
                </td>
            </tr>

            <tr>
                <th>備考</th>
                <td>
                    {{ $request->request_data['note'] ?? '（未入力）' }}
                </td>
            </tr>

        </table>
    </div>

    @if(auth('admin')->check() && $request->status === 'pending')
        <div class="button-area">
            <form method="POST" action="{{ route('admin.request.approve', $request->id) }}">
                @csrf
                <button type="submit" class=edit-button>
                    承認
                </button>
            </form>
        </div>
    @endif

</main>

</body>
</html>
