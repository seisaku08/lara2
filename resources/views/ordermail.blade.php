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
    <p class="text-left">この度はご予約いただき誠にありがとうございます。<br>
        以下の内容で予約を承りました。</p>
        <h5 class="mt-2 mb-2">【キャンセル・変更について】</h5>
<p class="text-left">キャンセルはセミナー開催日の3営業日前までに予約画面より変更をお願いいたします。<br>
※ご不明な点がございましたら、下記までご連絡くださいますようお願いいたします。</p>

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
        <th colspan="4">セミナー情報</th>
    </tr>
    <tr>
        <td class="w25"><label>セミナー開催日</label></td>
        <td class="w25">{{ $orderdata['input']['seminar_day'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>セミナー名</label></td>
        <td class="w50">{{ $orderdata['input']['seminar_name'] }}</td>
    </tr>
    <tr class="midashi">
        <th colspan="4">配送先情報</th>
    </tr>
    @if( $orderdata['input']['seminar_venue_pending'] == true )
    <tr>
        <td class="w100 text-center"><label>後日入力</label></td>
    </tr>
    @else
    <tr>
        <td class="w25"><label>郵便番号</label></td>
        <td class="w40">{{ $orderdata['input']['venue_zip'] }}</td> 
    </tr>
    <tr>
        <td class="w25"><label>住所</label></td>
        <td class="w50">{{ $orderdata['input']['venue_addr1'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>施設・ビル名</label></td>
        <td class="w50">{{ $orderdata['input']['venue_addr2'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>会社・部門名１</label></td>
        <td class="w50">{{ $orderdata['input']['venue_addr3'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>会社・部門名２</label></td>
        <td class="w50">{{ $orderdata['input']['venue_addr4'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>配送先担当者</label></td>
        <td class="w40">{{ $orderdata['input']['venue_name'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>配送先電話番号</label></td>
        <td class="w40">{{ $orderdata['input']['venue_tel'] }}</td>
    </tr>
    <tr>
        <td class="w25"><label>到着希望日時</label></td>
        <td class="w40">
            {{ $orderdata['input']['shipping_arrive_day'] }} {{ $orderdata['input']['shipping_arrive_time'] }}
        </td>
    </tr>
    <tr>
        <td class="w25"><label>返送機材発送予定日</label></td>
        <td class="w25">{{ $orderdata['input']['shipping_return_day'] }}</td>
    </tr>
@endif
    <tr class="midashi">
        <th colspan="4">選択機材情報</th>
    </tr>
    <tr>
        <td class="w100">
            <div class="row">
                <div class="col-6 text-center"><label>使用開始日:</label>{{ $orderdata['input']['order_use_from'] }}</div>
                <div class="col-6 text-center"><label>使用終了日:</label>{{ $orderdata['input']['order_use_to'] }}</div>
            </div>
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

<p>株式会社 大應<br>
機材管理システム　管理チーム<br>
〒101-0047<br>
東京都千代田区内神田1-7-5<br>
TEL: 03-3292-1488<br>
e-mail: <a href="order@daioh-pc.com">order@daioh-pc.com</a>
  </body>
</html>
{{-- <?php dd($orderdata);?> --}}