<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠打刻</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<header class="header">
    <div class="header-inner">
        <img src="{{ asset('images/logo.png') }}" alt="COACHTECH" class="logo">

        <nav class="nav">
            <a href="{{ route('attendance.index') }}">勤怠</a>
            <a href="{{ route('attendance.list') }}">勤怠一覧</a>
            <a href="{{ route('request.list') }}">申請</a>

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="nav-button">ログアウト</button>
            </form>
        </nav>
    </div>
</header>

<main class="main">

    <div class="status">
        @if (!$attendance || !$attendance->clock_in)
            勤務前
        @elseif ($attendance->clock_out)
            退勤済
        @elseif ($attendance->breakTimes->whereNull('end_time')->count())
            休憩中
        @else
            出勤中
        @endif
    </div>

    <div class="date">{{ now()->isoFormat('Y年M月D日（ddd）') }}</div>
    <div class="time" id="current-time">{{ now()->format('H:i') }}</div>

    @if ($attendance && $attendance->clock_out)
        <div class="thanks-message">
            お疲れ様でした。
        </div>
    @endif

    <div class="button-group">

        @if (!$attendance || !$attendance->clock_in)
            <form method="POST" action="/attendance/clock-in">
                @csrf
                <button class="btn btn-black">出勤</button>
            </form>

        @elseif (!$attendance->clock_out)

            <form method="POST" action="/attendance/clock-out">
                @csrf
                <button class="btn btn-black">退勤</button>
            </form>

            @if (!$attendance->breakTimes->whereNull('end_time')->count())
                <form method="POST" action="/attendance/break-in">
                    @csrf
                    <button class="btn btn-white">休憩入</button>
                </form>
            @else
                <form method="POST" action="/attendance/break-out">
                    @csrf
                    <button class="btn btn-white">休憩戻</button>
                </form>
            @endif

        @endif

    </div>
</main>

</body>
</html>

<script>
    function updateTime() {
        const now = new Date();

        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        document.getElementById('current-time').textContent =
            `${hours}:${minutes}`;
    }

    updateTime();
    setInterval(updateTime, 1000);
</script>