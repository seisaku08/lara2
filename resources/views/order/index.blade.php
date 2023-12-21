@extends('adminlte::page')
@section('title', '予約一覧')
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">

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
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>予約者名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($sent))
        @foreach($sent as $order)
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
    
<br>

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
            <div class="col-12 text-center bg-info"><label>受付済の予約</label></div>
        </div>
        <div class="row border bg-secondary">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>予約者名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($accept))
        @foreach($accept as $order)
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
    
<br>

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
            <div class="col-12 text-center bg-info"><label>終了済の予約</label></div>
        </div>
        <div class="row border bg-secondary">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>予約者名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($end))
        @foreach($end as $order)
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