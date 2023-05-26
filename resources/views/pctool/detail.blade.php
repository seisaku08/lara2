@extends('adminlte::page')
@section('title', $machine_details->machine_name.' | 機材詳細')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1>@yield('title')</h1>

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
<article id="list">

	<table id="kizai">
			<tr class="midashi">
				<th colspan="5">{{$machine_details->machine_name}}</th>
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
	<table id="kizai2">
		<tr class="midashi">
			<th colspan="5">貸出予定</th>
		</tr>
		<tr>
			<td class="kizai-left">期間</td>
			<td class="kizai-right">セミナー名</td>
		</tr>
	@foreach($orders as $order)
		<tr>
			<td class="kizai-left">{{$order->order_use_from}}～{{$order->order_use_to}}</td>
			<td class="kizai-right">{{$order->seminar_name}}</td>
		</tr>
	@endforeach

</table>
<table id="form">
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

@endsection
