@extends('adminlte::page')
@section('title', '機材使用状況一覧ダウンロード')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>
    <form action="{{ url('/download') }}" method="POST">
        @csrf
        <p>現在の予約状況が一覧できるエクセルファイルがダウンロードできます<b>（リアルタイム更新）</b>。<br>下記のボタンよりファイルをダウンロードしてください。</p>
        <button>download</button>
    </form>
    <br>
    <h4>＜凡例＞</h4>
    <table class="table table-bordered col-6">
        <tr>
            <th>
                無色のセル
            </th>
            <td>
                この部分には予約が入っていません。<br>
            </td>
        </tr>
        <tr>
            <th style="background-color: #60ff70;">
                緑色のセル
            </th>
            <td>
                通常の予約が入っています。
            </td>
        </tr>
        <tr>
            <th style="background-color: #ffff00;">
                黄色のセル
            </th>
            <td>
                予約が入っていますが、配送先住所が未入力です。<br>
                ご担当の方は配送先住所をご記入ください。
            </td>
        </tr>
        <tr>
            <th style="background-color: #dddddd;">
                灰色のセル
            </th>
            <td>
                返却までの一連の手続きが完了しています。
            </td>
        </tr>

    </table>
@endsection