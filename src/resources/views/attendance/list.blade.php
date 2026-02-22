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
            <a href="{{ url('/attendance') }}">勤怠</a>
            <a href="{{ url('/attendance/list') }}">勤怠一覧</a>
            <a href="{{ route('request.list') }}">申請</a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="nav-button">ログアウト</button>
            </form>
        </nav>
    </div>
</header>

<main class="main">
    <h1 class="title">勤怠一覧（{{ $month->format('Y年m月') }}）</h1>

    <div class="date-nav">
        <a href="?month={{ $month->copy()->subMonth()->format('Y-m') }}">← 前月</a>

        <input
            type="month"
            id="month-picker"
            value="{{ $month->format('Y-m') }}"
            class="date-current"
        >

        <a href="?month={{ $month->copy()->addMonth()->format('Y-m') }}">翌月 →</a>
    </div>

    <script>
        document.getElementById('month-picker').addEventListener('change', function () {
            window.location.href = `?month=${this.value}`;
        });
    </script>

    <div class="table-wrapper">
        <table>
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
            @foreach ($days as $day)

            @php
                $attendance = $day['attendance'];
            @endphp

            <tr>
                <td>{{ $day['date']->isoFormat('MM/DD (dd)') }}</td>

                <td>
                    {{ $attendance?->clock_in?->format('H:i') ?? '-' }}
                </td>

                <td>
                    {{ $attendance?->clock_out?->format('H:i') ?? '-' }}
                </td>

                <td>
                    @if($attendance && $attendance->clock_in)
                        {{ $attendance->total_break_time }}
                    @else
                        -
                    @endif
                </td>

                <td>
                    @if($attendance && $attendance->clock_in && $attendance->clock_out)
                        {{ $attendance->working_time }}
                    @else
                        -
                    @endif
                </td>

                <td class="detail">
                    <a href="{{ route('attendance.detail', $day['date']->format('Y-m-d')) }}">
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