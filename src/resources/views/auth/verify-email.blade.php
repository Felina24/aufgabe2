<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メール認証</title>
</head>
<body style="text-align:center; padding:50px;">

    <h2>メール認証が必要です</h2>

    <p>
        登録したメールアドレスに認証リンクを送信しました。
        メールをご確認ください。
    </p>

    @if (session('status') == 'verification-link-sent')
        <p>
            新しい認証リンクを送信しました。
        </p>
    @endif

    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button type="submit">
            認証メールを再送する
        </button>
    </form>

</body>
</html>
