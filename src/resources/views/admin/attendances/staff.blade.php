<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>スタッフ勤怠</title>
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

<h1 class="title">
    {{ $user->name }} さんの勤怠一覧
</h1>

<div style="text-align:right; margin-bottom:15px;">
    <a href="{{ route('admin.attendance.csv', [
        'user' => $user->id,
        'month' => $month->format('Y-m')
    ]) }}">
        <button type="button">CSV出力</button>
    </a>
</div>

<div class="date-nav">
    <a href="?month={{ $month->copy()->subMonth()->format('Y-m') }}">← 前月</a>

    <input
        type="month"
        id="month-picker"
        name="month"
        value="{{ $month->format('Y-m') }}"
        class="date-current"
        onchange="location.href='?month=' + this.value"
    >
    <a href="?month={{ $month->copy()->addMonth()->format('Y-m') }}">翌月 →</a>
</div>

<table class="table-wrapper">
    <thead>
        <tr>
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
    </thead>

    <tbody>
    @foreach($days as $day)

        @php $attendance = $day['attendance']; @endphp

        <tr>
            <td>{{ $day['date']->isoFormat('MM/DD (dd)') }}</td>

            <td>{{ $attendance?->clock_in?->format('H:i') ?? '-' }}</td>

            <td>{{ $attendance?->clock_out?->format('H:i') ?? '-' }}</td>

            <td>{{ $attendance?->total_break_time ?? '-' }}</td>

            <td>{{ $attendance?->working_time ?? '-' }}</td>

            <td class="detail">
                <a href="{{ route('admin.attendance.detail', [
                    'user' => $user->id,
                    'date' => $day['date']->format('Y-m-d')
                ]) }}">
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