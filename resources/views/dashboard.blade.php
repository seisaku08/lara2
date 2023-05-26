@extends('adminlte::page')
@section('title', 'マイページ')
@section('content')
    <h1>@yield('title')</h1>
        {{-- <p>Under Constructiion!</p> --}}
        {{-- <?php dump($orders);?> --}}
        <p>直近セミナーまでの残り日数など、簡易的な各種情報を要約したページにする予定</p>
        <table id="kizai2">
            <tr class="midashi">
                <th colspan="5">登録済セミナー</th>
            </tr>
            <tr>
                <td class="kizai-left">期間</td>
                <td class="kizai-right">セミナーNo.</td>
                <td class="kizai-right">セミナー名</td>
            </tr>
        @if(isset($orders))
            @foreach($orders as $order)
                <tr>
                    <td class="kizai-left">{{$order->order_use_from}}～{{$order->order_use_to}}</td>
                    <td class="kizai-right"><a href="order/detail/{{$order->order_id}}" target="_blank">{{$order->order_no}}</a></td>
                    <td class="kizai-right">{{$order->seminar_name}}</td>
                </tr>
            @endforeach
        @else
            <td class="kizai-left">データはありません。</td>
            <td class="kizai-right"></td>
            <td class="kizai-right"></td>
        @endif

    </table>
    
@endsection