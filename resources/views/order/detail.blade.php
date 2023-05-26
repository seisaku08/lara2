@extends('adminlte::page')
@section('title', 'セミナーID:'.$orders->order_no.' | セミナー詳細')
@section('content')
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">
<h1>@yield('title')</h1>
<article id="list">
{{-- <?php dd($orders);?> --}}
	<table id="kizai2">
		<tr class="midashi">
			<th colspan="5">セミナーID：{{ $orders->order_no }}</th>
		</tr>
		<tr>
			<td class="kizai-left">セミナー名</td>
			<td class="kizai-right">使用期間</td>
		</tr>
		<tr>
			<td class="kizai-left">{{$orders->seminar_name}}</td>
			<td class="kizai-right">{{$orders->order_use_from}}～{{$orders->order_use_to}}</td>
		</tr>
		<tr class="midashi">
			<th colspan="5">配送先情報</th>
		</tr>
		 <tr>
		   <td class="left">セミナー開催日</td>
			<td class="right-half">{{ $orders->seminar_day }}
			</td>
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
			<td class="left"><label>ビル名</label></td>
			<td class="right-half">{{ $orders->venue_addr2 }}
			</td>
		</tr>
		<tr>
			<td class="left"><label>施設名</label></td>
			<td class="right-half">{{ $orders->venue_addr3 }}
			</td>
			<td class="left"><label>部署名</label></td>
			<td class="right-half">{{ $orders->venue_addr4 }}
			</td>
		</tr>
		<tr>
			<td class="left"><label>配送先担当者</label></td>
			<td class="right-half">{{ $orders->venue_name }}
		</tr>
		<tr>
			<td class="left">配達先電話番号</td>
			<td class="right-half">{{ $orders->venue_tel }}
			</td>
		</tr>
	   <tr>
			<td class="left">到着希望日時</td>
			<td class="right-half">{{ $orders->shipping_arrive_day }} 
			</td>
			<td class="left">返送予定日</td>
			<td class="right-half">{{ $orders->shipping_return_day }}
			</td>
		</tr>
  
</table>
<table id="form">
	<tr class="midashi">
		<th colspan="5">使用機材情報</th>
	</tr>
<tr>
		<td class="kizai-left">機材ID</td>
		<td class="kizai-right">機材名</td>
	</tr>
	@foreach($machines as $machine)
		<tr>
			<td class="kizai-left">{{$machine->machine_id}}</td>
			<td class="kizai-right">{{$machine->machine_name}}</td>
		</tr>
	@endforeach
	</tr>

</table>
<div class="txt-center"><a class="btn btn-primary btn-sm mb-2 p-1" href="{{ route('order.edit', $orders->order_id) }}">編集</a></div>
</article>
</form>

@endsection

@section('footer')
(c)2023
@endsection