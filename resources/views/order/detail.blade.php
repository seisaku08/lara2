@extends('adminlte::page')
@section('title', 'セミナー詳細 | ID:'.$orders->order_no)
@section('content')
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">
<h1 class="p-2">@yield('title')</h1>
<article id="list">
{{-- <?php dd($orders);?> --}}
	<table id="kizai2">
		<tr class="midashi">
			<th colspan="4">セミナーID：{{ $orders->order_no }}</th>
		</tr>
		<tr>
			<td class="kizai-left">セミナー名</td>
			<td class="kizai-right">{{$orders->seminar_name}}</td>
		</tr>
		<tr>
			<td class="w25"><label>セミナー開催日</label></td>
			<td class="w25">{{ $orders->seminar_day }}</td>
		</tr>
		<tr>
			<td class="kizai-left">使用期間</td>
			<td class="kizai-right">{{$orders->order_use_from}}～{{$orders->order_use_to}}</td>
		</tr>
		<tr class="midashi">
			<th colspan="4">配送先情報<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.edit', $orders->order_id) }}">編集</a></th>
		</tr>
		 <tr>
			<td class="left">セミナー名</td>
			<td class="right-half">{{ $orders->seminar_name }}
		 </tr>
		<tr>
			<td class="left"><label>郵便番号</label></td>
			<td class="right-half">{{ $orders->venue_zip }}
			</td>
		</tr>
		<tr>
			<td class="left"><label>住所</label></td>
			<td class="right-half">{{ $orders->venue_addr1 }}
			</td>
			<td class="left"><label>施設・ビル名</label></td>
			<td class="right-half">{{ $orders->venue_addr2 }}
			</td>
		</tr>
		<tr>
			<td class="left"><label>会社・部門名１</label></td>
			<td class="right-half">{{ $orders->venue_addr3 }}
			</td>
			<td class="left"><label>会社・部門名２</label></td>
			<td class="right-half">{{ $orders->venue_addr4 }}
			</td>
		</tr>
		<tr>
			<td class="left"><label>配送先担当者</label></td>
			<td class="right-half">{{ $orders->venue_name }}
		</tr>
		<tr>
			<td class="left">配送先電話番号</td>
			<td class="right-half">{{ $orders->venue_tel }}
			</td>
		</tr>
	   <tr>
			<td class="left">到着希望日時</td>
			<td class="right-half">{{ $orders->shipping_arrive_day }} 
			</td>
			<td class="left">返送機材発送予定日</td>
			<td class="right-half">{{ $orders->shipping_return_day }}
			</td>
		</tr>
  
</table>
<table id="form">
	<tr class="midashi">
		<th colspan="4">使用機材情報</th>
	</tr>
<tr>
		<td class="kizai-left">機材ID</td>
		<td class="kizai-right">機材名</td>
	</tr>
	@foreach($machines as $machine)
		<tr>
			<td class="kizai-left">{{$machine->machine_id}}</td>
			<td class="kizai-right"><a href="/pctool/detail/{{$machine->machine_id}}" target="_blank">{{$machine->machine_name}}</a></td>
		</tr>
	@endforeach
	</tr>

</table>
<div class="text-center"></div>
</article>
</form>

@endsection

@section('footer')
(c)2023
@endsection