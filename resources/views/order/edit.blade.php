@extends('adminlte::page')
@section('title', 'セミナーID:'.$orders->order_no.' | セミナー情報変更')
@section('content')
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">
<h1>@yield('title')</h1>
@if(count($errors)>0)
<div>
	<ul>
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
<article id="list">
	<form action="{{ route('order.update', $orders->order_id) }}" method="post">
		@csrf
		@method('put')
	{{ Form::hidden('order_id',$orders->order_id) }}
	{{ Form::hidden('shipping_id',$orders->shipping_id) }}
	{{ Form::hidden('venue_id',$orders->venue_id) }}

	<table id="kizai2">
		<tr class="midashi">
			<th colspan="5">セミナーID：{{ $orders->order_no }}</th>
		</tr>
		<tr>
			<td class="col-3">セミナー名</td>
			<td class=""><input type="text" name="seminar_name" placeholder="" value="{{ !empty(old('seminar_name'))? old('seminar_name') :$orders->seminar_name }}"></td>
		</tr>
		<tr>
			<td class="col-3">使用期間<br>（変更できません。）</td>
			<td class="">{{$orders->order_use_from}}～{{$orders->order_use_to}}</td>
		</tr>
		<tr class="midashi">
			<th colspan="5">配送先情報</th>
		</tr>
		<tr>
			<td class="left">セミナー開催日</td>
			<td class="right-half"><input type="date" name="seminar_day" placeholder="" value="{{ !empty(old('seminar_day'))? old('seminar_day') : $orders->seminar_day }}"></td>
		</tr>
		<tr>
			<td class="left"><label>郵便番号</label></td>
			<td class="right">
				<input type="text" name="venue_zip" id="zip" maxlength="8" placeholder="例）1010047" oninput="value = NUM(value)" value="{{ !empty(old('zip'))? old('zip') :$orders->venue_zip }}">
				<button type="button" onclick="AjaxZip3.zip2addr(venue_zip,'','venue_addr1','venue_addr1');">住所を自動入力</button>
			</td> 
		</tr>
		<tr>
			<td class="left"><label>住所</label></td>
			<td class="right-half"><input type="text" name="venue_addr1" placeholder="例）東京都千代田区内神田1-7-5" value="{{ !empty(old('venue_addr1'))? old('venue_addr1') :$orders->venue_addr1 }}"></td>
			<td class="left"><label>ビル名</label></td>
			<td class="right-half"><input type="text" name="venue_addr2" placeholder="例）旭栄ビル 2階" value="{{ !empty(old('venue_addr2'))? old('venue_addr2') :$orders->venue_addr2 }}"></td>
		</tr>
		<tr>
			<td class="left"><label>施設名</label></td>
			<td class="right-half"><input type="text" name="venue_addr3" placeholder="例）株式会社 大應" value="{{ !empty(old('venue_addr3'))? old('venue_addr3') :$orders->venue_addr3 }}"></td>
			<td class="left"><label>部署名</label></td>
			<td class="right-half"><input type="text" name="venue_addr4" placeholder="例）●●部" value="{{ !empty(old('venue_addr4'))? old('venue_addr4') :$orders->venue_addr4 }}"></td>
		</tr>
		<tr>
			<td class="left"><label>配送先担当者</label></td>
			<td class="right-half"><input type="text" name="venue_name" placeholder="" value="{{ !empty(old('venue_name'))? old('venue_name') :$orders->venue_name }}"></td>
		</tr>
		<tr>
			<td class="left">配達先電話番号</td>
			<td class="right-half">
				<input type="tel" name="venue_tel" placeholder="例）0332921488 / 03-3292-1488" oninput="value = NUM(value)" value="{{ !empty(old('venue_tel'))? old('venue_tel') :$orders->venue_tel }}">
			</td>
		</tr>
		<tr>
			<td class="left">到着希望日時</td>
			<td class="right-half">
				<input type="date" name="shipping_arrive_day" placeholder="" value="{{ !empty(old('shipping_arrive_day'))? old('shipping_arrive_day') :$orders->shipping_arrive_day }}">
				<select name="shipping_arrive_time">
					<option value="指定なし"{{ (old('shipping_arrive_time') == "指定なし" | $orders->shipping_arrive_day == "指定なし") ? ' selected' : '' }}>指定なし</option>
					<option value="午前中"{{ (old('shipping_arrive_time') == "午前中" | $orders->shipping_arrive_day == "午前中") ? ' selected' : '' }}>午前中</option>
					<option value="14時～16時"{{ (old('shipping_arrive_time') == "14時～16時" | $orders->shipping_arrive_day == "14時～16時") ? ' selected' : '' }}>14時～16時</option>
					<option value="16時～18時"{{ (old('shipping_arrive_time') == "16時～18時" | $orders->shipping_arrive_day == "16時～18時") ? ' selected' : '' }}>16時～18時</option>
					<option value="18時～20時"{{ (old('shipping_arrive_time') == "18時～20時" | $orders->shipping_arrive_day == "18時～20時") ? ' selected' : '' }}>18時～20時</option>
					<option value="20時～21時"{{ (old('shipping_arrive_time') == "20時～21時" | $orders->shipping_arrive_day == "20時～21時") ? ' selected' : '' }}>20時～21時</option>
				</select>
			</td>
			<td class="left">返送予定日</td>
			<td class="right-half"><input type="date" name="shipping_return_day" placeholder="" value="{{ !empty(old('shipping_return_day'))? old('shipping_return_day') :$orders->shipping_return_day }}"></td>
		</tr>

</table>
<table id="form">
	<tr class="midashi">
		<th colspan="5">使用機材情報（機材情報の変更はできません。）</th>
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
<div class="txt-center"><button type="submit" class="btn btn-primary btn-sm mb-2 p-1" href="">変更を送信する</button>
</form>
<form action="{{ route('order.destroy', $orders->order_id) }}" method="post">
		@csrf
		@method('DELETE')
		<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('オーダーを削除します。元には戻せませんがよろしいですか？');">このオーダーを削除する</button>
		</form>
		</div>
</article>


@endsection

@section('footer')
(c)2023
@endsection