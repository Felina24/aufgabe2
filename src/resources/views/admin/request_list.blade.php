<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>申請詳細</title>

<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
</head>

<body>

<main class="main">

<h1 class="title">申請詳細</h1>

<form method="POST" action="{{ route('request.approve', $request->id) }}">
@csrf

<div class="detail-wrapper">
<table class="detail-table">

<tr>
<th>名前</th>
<td>{{ $request->attendance->user->name }}</td>
</tr>

<tr>
<th>日付</th>
<td>{{ $request->attendance->date->format('Y年m月d日') }}</td>
</tr>

<tr>
<th>出勤・退勤</th>
<td>
<input type="time"
value="{{ $request->request_data['clock_in'] ?? '' }}"
class="time-box" disabled>

<span class="wave">〜</span>

<input type="time"
value="{{ $request->request_data['clock_out'] ?? '' }}"
class="time-box" disabled>
</td>
</tr>

@foreach(($request->request_data['breaks'] ?? []) as $index => $break)

<tr>
<th>休憩{{ $index + 1 }}</th>
<td>
<input type="time"
value="{{ $break['start'] ?? '' }}"
class="time-box" disabled>

<span class="wave">〜</span>

<input type="time"
value="{{ $break['end'] ?? '' }}"
class="time-box" disabled>
</td>
</tr>

@endforeach

<tr>
<th>備考</th>
<td>
<textarea class="note" disabled>{{ $request->request_data['note'] ?? '' }}</textarea>
</td>
</tr>

</table>
</div>

<div class="button-area">
<button type="submit" class="edit-button">
承認
</button>
</div>

</form>

</main>

</body>
</html>