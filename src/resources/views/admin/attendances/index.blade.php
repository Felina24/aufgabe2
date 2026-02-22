<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠一覧</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />

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
    <h1 class="title">{{ $date->format('Y年m月d日') }}の勤怠</h1>

    <div class="date-nav">
        <a href="?date={{ $date->copy()->subDay()->toDateString() }}">← 前日</a>

        <input
        type="date"
        id="date-picker"
        value="{{ $date->format('Y-m-d') }}"
        class="date-current"
    >
        <a href="?date={{ $date->copy()->addDay()->toDateString() }}">翌日 →</a>
    </div>

    <script>
        document.getElementById('date-picker').addEventListener('change', function() {
            const selectedDate = this.value;
            window.location.href = `?date=${selectedDate}`;
        });
    </script>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                    <tr>
                        <td>{{ $attendance->user->name }}</td>
                        <td>{{ optional($attendance->clock_in)->format('H:i') }}</td>
                        <td>{{ optional($attendance->clock_out)->format('H:i') }}</td>
                        <td>{{ $attendance->total_break_time ?? '-' }}</td>
                        <td>{{ $attendance->working_time ?? '-' }}</td>
                        <td class="detail">
                            <a href="{{ route('admin.attendance.detail', [
                                'user' => $attendance->user_id,
                                'date' => $attendance->date->format('Y-m-d')
                            ]) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>

</body>
</html>