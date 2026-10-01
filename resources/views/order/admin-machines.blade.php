@extends('adminlte::page')

@section('title', '管理者用：予約操作')

@section('css')
<link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('content')
    <h1 class="text-center p-2">@yield('title')</h1>

    <div class="container box1000">
        <h4 class="text-bold text-center">新規予約を作成する</h4>
        <p class="text-center">
            <a class="btn btn-primary" href="{{ route('admin.orders.create') }}">新規予約の作成</a>
        </p>

        <h4 class="text-bold text-center">予約操作を行うセミナーを選択</h4>
        <p class="text-center">予約No.をクリックすると、予約操作画面に遷移します。</p>

        @foreach([['status' => '受付済', 'title' => '受付済の予約'], ['status' => '仮登録', 'title' => '仮登録の予約']] as $group)
            <div class="row border">
                <div class="col-12 text-center bg-info"><label>{{ $group['title'] }}</label></div>
            </div>
            <div class="row border bg-secondary">
                <div class="col-3"><label>期間</label></div>
                <div class="col-2"><label>予約No.</label></div>
                <div class="col"><label>セミナー名</label></div>
                <div class="col-2"><label>予約者名</label></div>
                <div class="col-2"><label>現在の状態</label></div>
            </div>
            @forelse($orders->where('order_status', $group['status']) as $order)
                <div class="row border {{ $order->user_id == 2 ? 'text-danger bg-warning' : '' }}">
                    <div class="col-3">{{ $order->order_use_from }}～{{ $order->order_use_to }}</div>
                    <div class="col-2 text-bold"><a href="{{ route('admin.orders.machines.edit', $order->order_id) }}">{{ $order->order_no }}</a></div>
                    <div class="col">{{ $order->seminar_name }}</div>
                    <div class="col-2">{{ $order->user_id == 2 ? $order->temporary_name.'（仮）' : $order->name }}</div>
                    <div class="col-2 {{ $order->user_id == 2 ? 'text-danger text-bold' : '' }}">{{ $order->order_status }}</div>
                </div>
            @empty
                <div class="row">
                    <div class="col-3">データはありません。</div>
                    <div class="col-2"></div>
                    <div class="col"></div>
                    <div class="col-2"></div>
                    <div class="col-2"></div>
                </div>
            @endforelse
            <br>
        @endforeach
    </div>
@endsection