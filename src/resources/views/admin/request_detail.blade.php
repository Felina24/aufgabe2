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
            <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
            <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>
            <a href="{{ route('admin.request.list') }}">申請一覧</a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="nav-button">ログアウト</button>
            </form>
        </nav>

    </div>
</header>

<main class="main">

    <h1 class="title">申請詳細</h1>

    @php
        $attendance = $attendanceRequest->attendance;
        $data = $attendanceRequest->request_data;
    @endphp

    <div class="detail-wrapper">
        <table class="detail-table">

            <tr>
                <th>名前</th>
                <td>{{ $attendance->user->name }}</td>
            </tr>

            <tr>
                <th>日付</th>
                <td>{{ $attendance->date->isoFormat('YYYY年M月D日') }}</td>
            </tr>

            <tr>
                <th>出勤・退勤</th>
                <td>
                    <input type="time"
                           value="{{ $data['clock_in'] ?? optional($attendance->clock_in)->format('H:i') }}"
                           class="time-box"
                           readonly>

                    <span class="wave">〜</span>

                    <input type="time"
                           value="{{ $data['clock_out'] ?? optional($attendance->clock_out)->format('H:i') }}"
                           class="time-box"
                           readonly>
                </td>
            </tr>

            @for ($i = 0; $i < 2; $i++)
                @php
                    $break = $data['breaks'][$i] ?? null;
                @endphp

                <tr>
                    <th>休憩{{ $i + 1 }}</th>
                    <td>

                        <input type="time"
                               value="{{ $break['start'] ?? '' }}"
                               class="time-box"
                               readonly>

                        <span class="wave">〜</span>

                        <input type="time"
                               value="{{ $break['end'] ?? '' }}"
                               class="time-box"
                               readonly>

                    </td>
                </tr>
            @endfor

            <tr>
                <th>備考</th>
                <td>
                    <textarea class="note" readonly>{{ $data['note'] ?? $attendance->note }}</textarea>
                </td>
            </tr>

        </table>
    </div>

    <div class="button-area">
    @if($attendanceRequest->status === 'pending')

        <form method="POST" action="{{ route('admin.request.approve', $attendanceRequest->id) }}">
            @csrf

            <button type="submit"
                    class="edit-button">
                承認
            </button>

        </form>

    @else

        <button type="button"
                class="edit-button approved-button"
                disabled>
            承認済み
        </button>

    @endif
    </div>

</main>

</body>
</html>