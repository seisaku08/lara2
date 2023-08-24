<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="{{ asset('css/sendstyle.css') }}" type="text/css">

<body class="mail">
    <p class="text-left">{{ $orderdata['user']['name'] }}様</p>
    <p class="text-left">※このメールはシステムからの自動送信です</p>
    <p class="text-left">お世話になっております。<br>
        株式会社大應【機材管理システム】より送信させていただいております。</p>
    <p class="text-left">
        予約No. {{ $orderdata['order']['order_no'] }}として承りましたセミナーが、開催日の{{ Carbon\Carbon::today()->diffindays($orderdata['order']['seminar_day']);}}営業日{{ Carbon\Carbon::create($orderdata['order']['seminar_day'])->isFuture()?'前':'過ぎ'; }}となりましたのでご連絡いたします。
        予約内容の変更・取消などございましたら、下記よりお手続きをお願いいたします。</p>
    {{-- 送り先未入力の場合、解消されるまで送られ続けることへの注意書 --}}
    @if( $orderdata['order']['seminar_venue_pending'] == true )
        <p class="text-left">また、本予約につきましては<span class="text-red text-bold">配送先住所が未入力となっております。</span><br>このままでは配送のお手続きを進めることができませんので、ご入力をお願いいたします。</p>
        <p class="text-left">なお、未入力の状態が解消されるまで、1日ごとに本メールが送信されます。ご了承ください。</p>
    @endif
    {{-- https://daioh-pc.com/order/detail/{{$orderdata['order']['order_id']}} --}}
        <a href="{{route("order.detail",$orderdata['order']['order_id'])}}">変更はこちら（webブラウザが開きます）</a><br>
        <h5 class="mt-3 mb-2">【予約の変更・取消について】</h5>
<p class="text-left">システムを利用しての予約変更・取消は、セミナー開催日の3営業日前まで受付可能です。<br>
    システムによる受付締切後のご相談や、ご不明な点がございましたら、下記お問い合わせ先までご連絡くださいますようお願いいたします。</p>
    
<table id="form">

    <tr class="midashi">
        <th colspan="5">ご担当者様情報</th>
    </tr>
    <tr>
        <td class="w20"><label>ご担当者氏名</label></td>
        <td class="w30">{{ $orderdata['user']['name'] }}</td>
        <td class="w20"><label>所属部署</label></td>
        <td class="w30">{{ $orderdata['user']['user_group'] }}</td>
    </tr>
    <tr>
        <td class="w20"><label>メールアドレス</label></td>
        <td class="w30">{{ $orderdata['user']['email'] }}</td>
        <td class="w20"><label>電話番号</label></td>
        <td class="w30">{{ $orderdata['user']['user_tel'] }}</td>
    </tr>
    <tr class="midashi">
        <th colspan="4">セミナー情報（予約No. {{ $orderdata['order']['order_no'] }}）</th>
    </tr>
    <tr>
        <td class="w25"><label>セミナー名</label></td>
        <td class="w50">{{ $orderdata['order']['seminar_name'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>セミナー開催日</label></td>
        <td class="w25">{{ $orderdata['order']['seminar_day'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>予約開始日:</label></td>
        <td class="w25">{{ $orderdata['order']['order_use_from'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>予約終了日:</label></td>
        <td class="w25">{{ $orderdata['order']['order_use_to'] }}</td>
    </tr>
    <tr class="midashi">
        <th colspan="4">配送先情報</th>
    </tr>
    @if( $orderdata['order']['seminar_venue_pending'] == true )
    <tr>
        <td class="w100 text-center text-red"><label>未入力</label></td>
    </tr>
    @else
    <tr>
        <td class="w25"><label>郵便番号</label></td>
        <td class="w40">{{ $orderdata['venue']['venue_zip'] }}</td> 
    </tr>
    <tr>
        <td class="w25"><label>住所</label></td>
        <td class="w50">{{ $orderdata['venue']['venue_addr1'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>施設・ビル名</label></td>
        <td class="w50">{{ $orderdata['venue']['venue_addr2'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>会社・部門名１</label></td>
        <td class="w50">{{ $orderdata['venue']['venue_addr3'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>会社・部門名２</label></td>
        <td class="w50">{{ $orderdata['venue']['venue_addr4'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>配送先担当者</label></td>
        <td class="w40">{{ $orderdata['venue']['venue_name'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>配送先電話番号</label></td>
        <td class="w40">{{ $orderdata['venue']['venue_tel'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>到着希望日時</label></td>
        <td class="w40">
            {{ $orderdata['shipping']['shipping_arrive_day'] }} {{ $orderdata['shipping']['shipping_arrive_time'] }}
        </td>
    </tr>
    <tr>
        <td class="w25"><label>返送機材発送予定日</label></td>
        <td class="w25">{{ $orderdata['shipping']['shipping_return_day'] }}</td>
    </tr>
@endif
    <tr class="midashi">
        <th colspan="4">選択機材情報</th>
    </tr>
    <tr>
        <td class="w100">
            <div class="row">
                <div class="col-2"><label>ID</label></div>
                <div class="col-10"><label>機材番号</label></div>
            </div>
            @foreach($orderdata['machines'] as $machine)
            <div class="row">
                <div class="col-2">{{$machine->machine_id}}</div>
                <div class="col-10">{{$machine->machine_name}}</div>
            </div>
            @endforeach
        </td>
    </tr>
</table>
<h4 class="text-center mt-3">【お問い合わせ先】</h4>
<p>株式会社 大應<br>
機材管理システム　管理チーム<br>
〒101-0047　
東京都千代田区内神田1-7-5<br>
TEL: 03-3292-1488<br>
e-mail: <a href="support@daioh-pc.com">support@daioh-pc.com</a>
  </body>
</html>
{{-- <?php dd($orderdata);?> --}}
