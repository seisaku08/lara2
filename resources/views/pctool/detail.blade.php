@extends('adminlte::page')
@section('title', '機材詳細 | '.$machine_details->machine_name)
@section('css')
<link href="{{ asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-3">@yield('title')</h1>

{{-- <!doctype html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$machine_details->machine_name}} | 機材詳細</title>

		<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">

</head>

<body>
<h1>機材詳細</h1> --}}
<article id="detail">

	<table class="full">
			<tr class="midashi">
				<th colspan="4">主要諸元</th>
		</tr>
		<tr>
			<td class="p-1 w25">機種</td>
			<td colspan="3">{{$machine_details->machine_spec}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">状態</td>
			<td colspan="3">{{$machine_details->machine_status}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">導入年月</td>
			<td class="p-1 w25">{{$machine_details->machine_since}}</td>
			<td class="p-1 w25">OS</td>
			<td class="p-1 w25">{{$machine_details->machine_os}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">CPU</td>
			<td class="p-1 w25">{{$machine_details->machine_cpu}}</td>
			<td class="p-1 w25">メモリ</td>
			<td class="p-1 w25">{{$machine_details->machine_memory}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">モニタサイズ</td>
			<td class="p-1 w25">{{$machine_details->machine_monitor}}</td>
			<td class="p-1 w25">PowerPointバージョン</td>
			<td class="p-1 w25">{{$machine_details->machine_powerpoint}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">内蔵カメラ</td>
			<td class="p-1 w25">{{$machine_details->machine_camera == true ? '有' : '無'}}</td>
			<td class="p-1 w25">光学ドライブ</td>
			<td class="p-1 w25">{{$machine_details->machine_hasdrive == true ? '有' : '無'}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">映像出力端子</td>
			<td class="p-1 w25">{{$machine_details->machine_connector}}</td>
			<td class="p-1 w25">Win11アップグレード</td>
			<td class="p-1 w25">{{$machine_details->machine_canto11}}</td>
		</tr>
		<tr>
			<td class="p-1 w25">備考</td>
			<td colspan="3">{{$machine_details->machine_memo}}</td>
		</tr>

	</table>
	<table class="full">
		<tr class="midashi">
			<th colspan="2">付属品</th>
		</tr>
		<tr>
			<td class="w40"><label>品名</label></td>
			<td class="w60"><label>備考</label></td>
		</tr>

	@if()@foreach($supplies as $supply)
		<tr>
			<td class="w40">{{$supply->supply_name}}</td>
			<td class="w60">{{$supply->supply_memo}}</td>
		</tr>
	@endforeach
</table>
	<table class="full">
		<tr class="midashi">
			<th colspan="5">貸出予定</th>
		</tr>
		<tr>
			<td class="w25"><label>予約期間</label></td>
			<td class="w15"><label>予約No. </label></td>
			<td class="w60" colspan="3"><label>セミナー名</label></td>
		</tr>
	@foreach($orders as $order)
		<tr>
			<td class="w25">{{$order->order_use_from}}～{{$order->order_use_to}}</td>
			<td class="w15">{{$order->order_no}}</td>
			<td class="w60" colspan="3">{{$order->seminar_name}}</td>
		</tr>
	@endforeach
</table>
<table class="full">
	<tr class="midashi">
			<th colspan="5">メンテナンス履歴</th>
	</tr>
	@foreach($maintenances as $mainte)
	<tr>
			<td>{{$mainte->maintenance_day}}</td>
			<td>{{$mainte->maintenance_detail}}</td>
	</tr>
	@endforeach

</table>
</article>
</form>
<p class="text-center mt-3" ><button onclick="window.close();">詳細画面を閉じる</button></p>

@endsection
