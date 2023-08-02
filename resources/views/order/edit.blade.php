@extends('adminlte::page')
@section('title', 'セミナー情報変更 | 予約No. '.$orders->order_no)
@section('content')
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">
<script src="/js/number.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
<h1 class="p-2">@yield('title')</h1>
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
	{{ Form::hidden('order_use_from',$orders->order_use_from) }}
	{{ Form::hidden('order_use_to',$orders->order_use_to) }}
 {{-- <?php dump($orders); ?>  --}}
	<table id="kizai2">
		<tr class="midashi">
			<th colspan="5">セミナー情報</th>
		</tr>
		<tr>
			<td class="w30"><label>セミナー名</label><span class="red small">＊必須</span></td>
			<td class="w50"><input type="text" name="seminar_name" placeholder="" value="{{!empty(old('seminar_name'))? old('seminar_name'):$orders->seminar_name}}"></td>
		</tr>
		<tr>
			<td class="w30"><label>セミナー開催日</label><span class="red small">＊必須</span></td>
			<td class="w25"><input type="date" name="seminar_day" placeholder="" value="{{!empty(old('seminar_day'))? old('seminar_day'):$orders->seminar_day}}"></td>
		</tr>
		<tr>
			<td class="w30"><label>予約期間</label><br>（変更できません。）</td>
			<td class="">{{$orders->order_use_from}}～{{$orders->order_use_to}}</td>
		</tr>
		<tr class="midashi">
			<th colspan="5">配送先情報</th>
		</tr>
		<tr>
			<td class="w30"><label>郵便番号</label><span class="red small">＊必須</span></td>
			<td class="w40">
				<input type="text" name="venue_zip" id="zip" maxlength="8" placeholder="例）1010047" oninput="value = NUM(value)" value="{{ !empty(old('venue_zip'))? old('venue_zip') :$orders->venue_zip }}">
				<button type="button" onclick="AjaxZip3.zip2addr(venue_zip,'','venue_addr1','venue_addr1');">住所を自動入力</button>
			</td> 
		</tr>
		<tr>
			<td class="w30"><label>住所</label><span class="red small">＊必須</span></td>
			<td class="w50"><input type="text" name="venue_addr1" placeholder="例）東京都千代田区内神田1-7-5" value="{{ !empty(old('venue_addr1'))? old('venue_addr1') :$orders->venue_addr1 }}"></td>
		</tr>
		<tr>
		   <td class="w30"><label>施設・ビル名</label></td>
			<td class="w50"><input type="text" name="venue_addr2" placeholder="例）旭栄ビル 2階" value="{{ !empty(old('venue_addr2'))? old('venue_addr2') :$orders->venue_addr2 }}"></td>
		</tr>
		<tr>
			<td class="w30"><label>会社・部門名１</label></td>
			<td class="w50"><input type="text" name="venue_addr3" placeholder="例）株式会社 大應" value="{{ !empty(old('venue_addr3'))? old('venue_addr3') :$orders->venue_addr3 }}"></td>
		</tr>
		<tr>
			<td class="w30"><label>会社・部門名２</label></td>
			<td class="w50"><input type="text" name="venue_addr4" placeholder="例）●●部" value="{{ !empty(old('venue_addr4'))? old('venue_addr4') :$orders->venue_addr4 }}"></td>
		</tr>
		<tr>
			<td class="w30"><label>配送先担当者</label><span class="red small">＊必須</span></td>
			<td class="w40"><input type="text" name="venue_name" placeholder="" value="{{ !empty(old('venue_name'))? old('venue_name') :$orders->venue_name }}"></td>
		</tr>
		<tr>
			<td class="w30"><label>配送先電話番号</label><span class="red small">＊必須</span></td>
			<td class="w40">
				<input type="tel" name="venue_tel" placeholder="例）0332921488 / 03-3292-1488" oninput="value = NUM(value)" value="{{ !empty(old('venue_tel'))? old('venue_tel') :$orders->venue_tel }}">
			</td>
		</tr>
		<tr>
			<td class="w30"><label>到着希望日時</label><span class="red small">＊必須</span></td>
			<td class="w40">
				<input type="date" name="shipping_arrive_day" placeholder="" value="{{ !empty(old('shipping_arrive_day'))? old('shipping_arrive_day') :$orders->shipping_arrive_day }}">
				<select name="shipping_arrive_time">
					<option value="指定なし"{{ (old('shipping_arrive_time') == "指定なし" | $orders->shipping_arrive_time == "指定なし") ? ' selected' : '' }}>指定なし</option>
					<option value="午前中"{{ (old('shipping_arrive_time') == "午前中" | $orders->shipping_arrive_time == "午前中") ? ' selected' : '' }}>午前中</option>
					<option value="14時～16時"{{ (old('shipping_arrive_time') == "14時～16時" | $orders->shipping_arrive_time == "14時～16時") ? ' selected' : '' }}>14時～16時</option>
					<option value="16時～18時"{{ (old('shipping_arrive_time') == "16時～18時" | $orders->shipping_arrive_time == "16時～18時") ? ' selected' : '' }}>16時～18時</option>
					<option value="18時～20時"{{ (old('shipping_arrive_time') == "18時～20時" | $orders->shipping_arrive_time == "18時～20時") ? ' selected' : '' }}>18時～20時</option>
					<option value="20時～21時"{{ (old('shipping_arrive_time') == "20時～21時" | $orders->shipping_arrive_time == "20時～21時") ? ' selected' : '' }}>20時～21時</option>
				</select>
			</td>
		</tr>
		<tr>
		   <td class="w30"><label>返送機材発送予定日</label><span class="red small">＊必須</span></td>
		   <td class="right-half"><input type="date" name="shipping_return_day" placeholder="" value="{{ !empty(old('shipping_return_day'))? old('shipping_return_day') :$orders->shipping_return_day }}"></td>
		</tr>
		<tr>
			<td class="w100"><label for="special" >事前搬入申請等、荷扱いに特記すべき事項がある場合はチェックを入れてください。→<input type="checkbox" class="dekai" name="shipping_special" placeholder="" id="special" value="true"{{ old('shipping_special') == true ? ' checked' : '' }}{{ $orders->shipping_special == true ? 'checked' : '' }}></label></td>
		</tr>
		<tr>
			<td class="w30"><label>備考</label></td>
			 <td class="w70"><textarea class="fullsize" name="shipping_note" rows="4" placeholder="特記事項やメモ等、申し送る必要のある事柄をお書きください。（200文字まで）" >{{ !empty(old('shipping_note'))? old('shipping_note') :$orders->shipping_note }}</textarea></td>
		 </tr>


</table>
<table id="kizai">
	<tr class="midashi">
		<th colspan="4">選択機材情報
			@if($user->role == 1 || $user->id == $orders->user_id)
			<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.addpc', $orders->order_id) }}">追加</a>
			<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.delpc', $orders->order_id) }}">削除</a>
			@else
			<div class="btn btn-primary btn-sm ml-3 p-1 disabled">予約者以外は編集できません</div>
			@endif
</th>
	</tr>
	<tr>
		<td class="w100">
			<div class="row">
				<div class="col-2"><label>ID</label></div>
				<div class="col-10"><label>機材番号</label></div>
			</div>
			@foreach($machines as $machine)
			<div class="row p-1">
				<div class="col-2">{{$machine->machine_id}}</div>
				<div class="col-10">{{$machine->machine_name}}</div>
			</div>
			@endforeach
		</td>
	</tr>
</table>
<div class="text-center">
	<span class="text-center">{{ Form::submit('前の画面に戻る', ['name' => 'back', 'class' => 'btn btn-primary m-2 p-1', ]) }}</span>
	<button type="submit" class="btn btn-primary m-2 p-1" href="">変更を送信する</button>
	</form>
</div>
</article>


@endsection

@section('footer')
(c)2023
@endsection