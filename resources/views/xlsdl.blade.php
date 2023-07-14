@extends('adminlte::page')
@section('title', '機材使用状況一覧エクセルシートダウンロード')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>
    <form action="{{ url('/download') }}" method="POST">
        @csrf
        <h2>下記のボタンを押下してファイルをダウンロードしてください。</h2>
        <button>download</button>
    </form>
@endsection