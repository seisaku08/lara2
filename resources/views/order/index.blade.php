@extends('adminlte::page')
@section('title', '予約一覧')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
    <h1 class="text-center p-2">@yield('title')</h1>
        {{-- <?php dump($orders);?> --}}
    <div class="container box1000">
    <form method="get" action="/order.index">
        @csrf
        <div class="row border">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>予約者名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($orders))
        {{-- <?php dump($orders);?> --}}
        @foreach($orders as $order)
        <div class="row border {{$order->user_id == 2? "text-danger bg-warning" : ""}}">
            <div class="col-3">{{$order->order_use_from}}～{{$order->order_use_to}}</div>
            <div class="col-2 text-bold"><a href="order/detail/{{$order->order_id}}">{{$order->order_no}}</a></div>
            <div class="col">{{$order->seminar_name}}</div>
            <div class="col-2">{{$order->user_id == 2? $order->temporary_name."（仮）" : "$order->name"}}</div>
            <div class="col-2 {{$order->user_id == 2? "text-danger text-bold" : ""}}">{{$order->order_status}}</div>
        </div>
        @endforeach
    @else
        <div class="row">
            <div class="col">データはありません。</div>
            <div class="col"></div>
            <div class="col"></div>
            <div class="col"></div>
        </div>
    @endif

    </div>
    
@endsection