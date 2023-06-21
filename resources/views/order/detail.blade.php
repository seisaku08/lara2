@extends('adminlte::page')
@section('title', 'セミナー詳細 | ID:'.$orders->order_no)
@section('css')
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>
<article id="list">
{{-- <?php dd($orders);?> --}}
	<table id="kizai2">
		<tr class="midashi">
			<th colspan="4">セミナー情報</th>
		</tr>
		<tr>
			<td class="w30"><label>セミナー名</label></td>
			<td class="w50">{{$orders->seminar_name}}</td>
		</tr>
		<tr>
			<td class="w30"><label>セミナー開催日</label></td>
			<td class="w25">{{ $orders->seminar_day }}</td>
		</tr>
		<tr>
			<td class="w30"><label>予約開始日:</label></td>
			<td class="w25">{{ $orders->order_use_from }}</td>
		</tr>
		<tr>
			<td class="w30"><label>予約終了日:</label></td>
			<td class="w25">{{ $orders->order_use_to }}</td>
		</tr>
		<tr class="midashi">
			<th colspan="4">配送先情報<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.edit', $orders->order_id) }}">編集</a></th>
		</tr>
		<tr>
			<td class="w30"><label>郵便番号</label></td>
			<td class="w50">{{ $orders->venue_zip }}
			</td>
		</tr>
		<tr>
			<td class="w30"><label>住所</label></td>
			<td class="w50">{{ $orders->venue_addr1 }}
			</td>
		</tr>
		<tr>
			<td class="w30"><label>施設・ビル名</label></td>
			<td class="w50">{{ $orders->venue_addr2 }}
			</td>
		</tr>
		<tr>
			<td class="w30"><label>会社・部門名１</label></td>
			<td class="w50">{{ $orders->venue_addr3 }}
			</td>
		</tr>
		<tr>
			<td class="w30"><label>会社・部門名２</label></td>
			<td class="w50">{{ $orders->venue_addr4 }}
			</td>
		</tr>
		<tr>
			<td class="w30"><label>配送先担当者</label></td>
			<td class="w50">{{ $orders->venue_name }}
		</tr>
		<tr>
			<td class="w30"><label>配送先電話番号</label></td>
			<td class="w50">{{ $orders->venue_tel }}
			</td>
		</tr>
		<tr>
			<td class="w30"><label>到着希望日時</label></td>
			<td class="w50">{{ $orders->shipping_arrive_day }}－{{ $orders->shipping_arrive_time }} 
			</td>
		</tr>
		<tr>
			 <td class="w30"><label>返送機材発送予定日</label></td>
			<td class="w50">{{ $orders->shipping_return_day }}
			</td>
		</tr>
  
</table>
<table id="kizai">
	<tr class="midashi">
		<th colspan="4">選択機材情報</th>
	</tr>
	<tr>
		<td class="w100">
			<div class="row">
				<div class="col-2"><label>ID</label></div>
				<div class="col-10"><label>機材番号</label></div>
			</div>
			@foreach($machines as $machine)
			<div class="row">
				<div class="col-2">{{$machine->machine_id}}</div>
				<div class="col-10"><a href="/pctool/detail/{{$machine->machine_id}}" target="_blank">{{$machine->machine_name}}</a></div>
			</div>
			@endforeach
		</td>
	</tr>
</table>
</article>
</form>

@endsection

@section('footer')
(c)2023
@endsection