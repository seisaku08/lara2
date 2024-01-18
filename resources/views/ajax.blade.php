@extends('adminlte::page')
@section('title', '予約一覧')
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">
<script src="{{ asset('/js/ajax_test.js') }}"></script>
@endsection
@section('content')
    <h1 class="text-center p-2">@yield('title')</h1>

    <div class="container box1000">
        <form method="get" action="/order.index">
            {{-- <form method="post" action="/order"> --}}
        @csrf
            {{-- <div class="row border">
            <div class="col">並び方を変える
                {{ Form::radio('orderby', 'seminar_name') }}
            </div>
            <div class="col"><input type="submit"></div>
            </div> --}}
        <div class="row border">
            <div class="col-12 text-center bg-info"><label>進行中（貸出中）の予約</label></div>
        </div>
        <div class="row border bg-secondary">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セaミナー名</label></div>
            <div class="col-2"><label>予約者名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
        <div id="all_show_result">
            
        </div>

    </div>

@endsection