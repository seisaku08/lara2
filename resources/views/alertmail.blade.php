<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <link rel="stylesheet" href="{{ asset('css/sendstyle.css') }}" type="text/css">
    <?php use App\Libs\Common; use Carbon\Carbon; use App\Models\User;
    // Ensure $alertdata exists to avoid undefined variable warnings in direct view renders
    $alertdata = $alertdata ?? [];
    ?>
    {{-- {{ dd($alertdata ?? null) }} --}}
<body class="mail">
    <p class="text-left">※このメールはシステムからの自動送信です</p>
    <p class="text-left">株式会社大應【機材管理システム】からご連絡いたします。</p>
    <p class="text-left">
        イレギュラーな予約を検出しました。<br>
        <ul>
            @if(isset($alertdata['duplicates']) && count($alertdata['duplicates']) > 0)
            <li>トークン重複（連続クリック等による多重登録の可能性が高いもの）が{{ count($alertdata['duplicates']) }}件</li>
            @endif
            @if(isset($alertdata['incomplete_orders']) && count($alertdata['incomplete_orders']) > 0)
                <li>セミナー開催日、予約開始日、予約終了日のいずれかに空白のある予約が{{ count($alertdata['incomplete_orders']) }}件</li>
            @endif
        </ul>
        です。</p>
 <?php dump($alertdata ?? null);?>
   
@if(isset($alertdata['duplicates']) && count($alertdata['duplicates']) > 0)
<h5 class="mt-3 mb-2">【トークンが重複しているセミナー】</h5>
<table id="form">
    @foreach($alertdata['duplicates'] as $duplicate)
        <tr class="midashi">
            <th colspan="4">Token:{{ $duplicate['token'] }}</th>
        </tr>
            @foreach($duplicate['orders'] as $order)
                <tr>
                    <td class="w25"><label>予約No. {{ $order['order_no'] }}<br>(order_id: {{ $order['order_id'] }})</label></td>
                    <td class="w70">{{ $order['seminar_name'] ?? '' }}</td>
                </tr>
            @endforeach
    @endforeach
</table>
@endif
@if(isset($alertdata['incomplete_orders']) && count($alertdata['incomplete_orders']) > 0)
<h5 class="mt-3 mb-2">【予約日が異常なセミナー】</h5>
    <table id="form">
        @foreach($alertdata['incomplete_orders'] as $order)
            <tr class="midashi">
                <th colspan="4">予約No. {{ $order['order_no'] }}　
                セミナー名：{{ $order['seminar_name'] ?? '' }}<br>(order_id: {{ $order['order_id'] ?? '' }})</th>
            </tr>
            <tr>
                <td class="w25"><label>ご担当者氏名</label></td>
                <td class="w25">{{ $order['user_name'] ?? '不明' }}</td>
            </tr>
            <tr>
            <td class="w25"><label>セミナー開催日</label></td>
                <td class="w25">{{ $order['seminar_day'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="w25"><label>予約開始日:</label></td>
                <td class="w25">{{ $order['order_use_from'] ?? '' }}</td>
                <td class="w25"><label>予約終了日:</label></td>
                <td class="w25">{{ $order['order_use_to'] ?? '' }}</td>
            </tr>
            @endforeach
    </table>
    @endif
<h4 class="text-center mt-3">【お問い合わせ先】</h4>
<p>株式会社 大應<br>
機材管理システム　管理チーム<br>
〒101-0047　
東京都千代田区内神田1-7-5<br>
TEL: 03-3292-1488<br>
e-mail: <a href="mailto:support@daioh-pc.com">support@daioh-pc.com</a>
  </body>
</html>
