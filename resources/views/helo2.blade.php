<!doctype html>
<html>
<head>
    <title>Sample</title>
    <style>
    body { color:gray; }
    h1 { font-size:18pt; font-weight:bold; }
    </style>
</head>
<body>
    <h1>Sample</h1>
    <form action="{{ url('/download') }}" method="POST">
        @csrf
        <h2>下記のボタンを押下してExcelファイルをダウンロードしてください。</h2>
        <button>download</button>
    </form>
</body>