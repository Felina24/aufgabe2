<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>勤怠詳細</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
</head>

<body>

<header class="header">
    <div class="header-inner">
        <img src="{{ asset('images/logo.png') }}" class="logo">

        <nav class="nav">

            @if(auth('admin')->check())

                <a href="{{ route('admin.attendance.list') }}">勤怠一覧</a>
                <a href="{{ route('admin.request.list') }}">申請一覧</a>
                <a href="{{ route('admin.staff.list') }}">スタッフ一覧</a>

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

<main class="main">

    <h1 class="title">勤怠詳細</h1>

    @if(session('message'))
        <p style="color:green;margin-bottom:15px;">
            {{ session('message') }}
        </p>
    @endif

    @if ($errors->any())
        <div class="error-message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
        action="{{ auth('admin')->check()
            ? route('admin.attendance.update', $attendance->id)
            : route('attendance.request', $attendance->id)
        }}">
            @csrf

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
                        <input
                            type="time"
                            name="clock_in"
                            value="{{ old(
                                'clock_in',
                                $hasPendingRequest
                                    ? ($pendingRequest->request_data['clock_in'] ?? '')
                                    : $attendance->clock_in?->format('H:i')
                            ) }}"
                            class="time-box"
                            {{ $hasPendingRequest ? 'disabled' : '' }}
                        >

                        <span class="wave">〜</span>

                        <input
                            type="time"
                            name="clock_out"
                            value="{{ old(
                                'clock_out',
                                $hasPendingRequest
                                    ? ($pendingRequest->request_data['clock_out'] ?? '')
                                    : $attendance->clock_out?->format('H:i')
                            ) }}"
                            class="time-box"
                            {{ $hasPendingRequest ? 'disabled' : '' }}
                        >
                    </td>
                </tr>

                @for ($i = 0; $i < 2; $i++)

                    @php
                        if ($hasPendingRequest) {
                            $break = $pendingRequest->request_data['breaks'][$i] ?? null;
                        } else {
                            $break = $attendance->breakTimes[$i] ?? null;
                        }
                    @endphp

                    <tr>
                        <th>休憩{{ $i + 1 }}</th>
                        <td>
                            <input
                                type="time"
                                name="breaks[{{ $i }}][start]"
                                value="{{ old("breaks.$i.start",
                                    $hasPendingRequest
                                        ? ($break['start'] ?? '')
                                        : $break?->start_time?->format('H:i')
                                ) }}"
                                class="time-box"
                                {{ $hasPendingRequest ? 'disabled' : '' }}
                            >

                            <span class="wave">〜</span>

                            <input
                                type="time"
                                name="breaks[{{ $i }}][end]"
                                value="{{ old("breaks.$i.end",
                                    $hasPendingRequest
                                        ? ($break['end'] ?? '')
                                        : $break?->end_time?->format('H:i')
                                ) }}"
                                class="time-box"
                                {{ $hasPendingRequest ? 'disabled' : '' }}
                            >
                        </td>
                    </tr>

                @endfor

                <tr>
                    <th>備考</th>
                    <td>
                        <textarea
                            name="note"
                            class="note"
                            {{ $hasPendingRequest ? 'disabled' : '' }}
                        >{{ old(
                            'note',
                            $hasPendingRequest
                                ? ($pendingRequest->request_data['note'] ?? '')
                                : $attendance->note
                        ) }}</textarea>
                    </td>
                </tr>

            </table>
        </div>

        @if(!$hasPendingRequest)

        <div class="button-area">
            <button
                type="submit"
                class="edit-button"
                onclick="this.disabled=true; this.form.submit();"
            >
                修正
            </button>
        </div>

        @endif

    </form>

    @if($hasPendingRequest)
        <p style="color:red;text-align:center;margin-top:10px;">
            ※承認待ちのため修正はできません。
        </p>
    @endif

</main>

</body>
</html>