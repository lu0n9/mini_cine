<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đang bảo trì · {{ $settings->site_name }}</title>
    <style>
        body{margin:0;min-height:100vh;display:grid;place-items:center;background:#08090d;color:#f7f5ff;font:16px system-ui,sans-serif}
        main{max-width:560px;margin:24px;padding:40px;border:1px solid #6d45a8;border-radius:20px;background:#15111f;text-align:center}
        h1{margin:0 0 12px;color:#b58aff}p{margin:0;color:#c7c1d2;line-height:1.7}
    </style>
</head>
<body><main><h1>{{ $settings->site_name }} đang bảo trì</h1><p>Website sẽ sớm hoạt động trở lại. Cảm ơn bạn đã thông cảm.</p></main></body>
</html>
