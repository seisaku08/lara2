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
				<th colspan="5">主要諸元</th>
		</tr>
		<tr>
			<td class="kizai-left">機種</td>
			<td class="kizai-right">{{$machine_details->machine_spec}}</td>
		</tr>
		<tr>
			<td class="kizai-left">導入年月</td>
			<td class="kizai-right">{{$machine_details->machine_since}}</td>
		</tr>
		<tr>
			<td class="kizai-left">備考</td>
			<td class="kizai-right">{{$machine_details->machine_memo}}</td>
		</tr>
		<tr>
			<td class="kizai-left">状態</td>
			<td class="kizai-right">{{$machine_details->machine_status}}</td>
		</tr>
	</table>
	<table class="full">
		<tr class="midashi">
			<th colspan="5">貸出予定</th>
		</tr>
		<tr>
			<td class="w25"><label>予約期間</label></td>
			<td class="w15"><label>予約No. </label></td>
			<td class="w50"><label>セミナー名</label></td>
		</tr>
	@foreach($orders as $order)
		<tr>
			<td class="w25">{{$order->order_use_from}}～{{$order->order_use_to}}</td>
			<td class="w15">{{$order->order_no}}</td>
			<td class="w60">{{$order->seminar_name}}</td>
		</tr>
	@endforeach
</table>
<table class="full">
	<tr class="midashi">
			<th colspan="5">メンテナンス履歴</th>
	</tr>
	@foreach($maintenances as $mainte)
	<tr>
			<td class="kizai-left">{{$mainte->maintenance_day}}</td>
			<td class="kizai-right">{{$mainte->maintenance_detail}}</td>
	</tr>
	@endforeach

</table>
</article>
</form>
<p class="text-center mt-3" ><button onclick="window.close();">詳細画面を閉じる</button></p>

@endsection
