@extends('adminlte::page')
@section('title', 'マイページ')
@section('content')
    <h1>@yield('title')</h1>
        {{-- <p>Under Constructiion!</p> --}}
        {{-- <?php dump($orders);?> --}}
        <p>こちらはマイページです。</p>
        <p>ユーザー個人の登録セミナー一覧や、各種情報（の概要）を要約したページにする予定です。</p>
        <h5>今後の予定</h5>
        <ul>
            <li>メール送信機能周りの実装</li>
            <ul>
                <li>登録時メール認証</li>
                <li>オーダー登録時の返送メール</li>
                <li>リマインダメール</li>
            </ul>
            <li>オーダー一覧ページ</li>
        </ul>
        </p>
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
        <?php dump($orders);?>
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