@extends('adminlte::page')
@section('title', 'セミナー詳細 | 予約No. '.$orders->order_no)
@section('css')
<link href="{{asset('/css/sendstyle.css')}}" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>
<article id="list">
{{-- <?php dd($orders, $user, $user->id == $orders->user_id);?> --}}
	@if(count($errors)>0)
	<div>
		<ul>
			@foreach($errors->all() as $error)
				<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif
<?php use App\Libs\Common; use Carbon\Carbon;?>

	<table id="kizai2">
		<tr class="midashi">
			<th colspan="4">予約者情報
				@if($orders->user_id == 2)
				<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.changetome', $orders->order_id) }}" onclick="return confirm('この予約の担当者を変更します。');">担当者を自分に変更する</a>
				@elseif($orders->user_id != 2 && $orders->temporary_name != null)
				<a class="btn btn-danger btn-sm ml-3 p-1" href="{{ route('order.changetojohn', $orders->order_id) }}" onclick="return confirm('この予約を仮登録の状態に戻します。誤って予約者を変更してしまった場合のみこのコマンドを実行してください。');">仮登録に戻す</a>
				{{-- @elseif($user->id == $orders->user_id)
				<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.addpc', $orders->order_id) }}">担当者を変更する</a>
				@else
				<div class="btn btn-primary btn-sm ml-3 p-1 disabled">予約者以外は変更できません</div> --}}
				@endif
			</th>
		</tr>
		<tr>
			<td class="w20"><label>ご担当者氏名</label></td>
			<td class="w30">{{$orders->name}}</td>
			<td class="w20"><label>所属部署</label></td>
			<td class="w30">{{$orders->user_group}}</td>
		</tr>
		<tr>
			<td class="w20"><label>メールアドレス</label></td>
			<td class="w30">{{$orders->email}}</td>
			<td class="w20"><label>電話番号</label></td>
			<td class="w30">{{$orders->user_tel}}</td>
		</tr>
	<tr class="midashi">
			<th colspan="4">セミナー情報
				@if($user->role == 1 || $user->id == $orders->user_id)
				<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.edit', $orders->order_id) }}">編集</a>
				@else
				<div class="btn btn-primary btn-sm ml-3 p-1 disabled">予約者以外は編集できません</div>
				@endif
			</th>
		</tr>
		<tr>
			<td class="w30"><label>セミナー名</label></td>
			<td class="w50">{{$orders->seminar_name}}</td>
		</tr>
		<tr>
			<td class="w30"><label>現在の状態</label></td>
			<td class="w25">{{ $orders->order_status }}</td>
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
			<th colspan="4">配送先情報
				@if($user->role == 1 || $user->id == $orders->user_id)
				<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.edit', $orders->order_id) }}">編集</a>
				@else
				<div class="btn btn-primary btn-sm ml-3 p-1 disabled">予約者以外は編集できません</div>
				@endif
			</th>
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
		<tr>
			<td class="w30"><label>特記事項</label></td>
			<td class="w25">{{ $orders->shipping_special == true ? 'あり' : 'なし' }}</td>
		</tr>
		<tr>
			<td class="w30"><label>備考</label></td>
			<td class="w70">{{ $orders->shipping_note }}</td>
		</tr>
		
</table>
<table id="kizai">
	<tr class="midashi">
		<th colspan="4">選択機材情報
			@if($user->role == 1 || $user->id == $orders->user_id)
				@if(Carbon::today() > Common::daybefore(Carbon::parse($orders->seminar_day),4))
				<div class="btn btn-primary btn-sm ml-3 p-1 disabled">変更可能期間を過ぎたため編集できません</div>

				@else
					<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.addpc', $orders->order_id) }}">追加</a>
					<a class="btn btn-primary btn-sm ml-3 p-1" href="{{ route('order.delpc', $orders->order_id) }}">削除</a>
				@endif
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
			<div class="row">
				<div class="col-2">{{$machine->machine_id}}</div>
				<div class="col-10"><a href="/pctool/detail/{{$machine->machine_id}}" target="_blank">{{$machine->machine_name}}</a></div>
			</div>
			@endforeach
		</td>
	</tr>
</table>
</form>
<form action="{{ route('order.destroy', $orders->order_id) }}" method="post">
	@csrf
	@method('DELETE')
<div>
	@if($user->id == $orders->user_id)
	<p>
		<button type="submit" class="text-center m-3 btn btn-danger" onclick="return confirm('予約を削除します。元には戻せませんがよろしいですか？');">この予約を削除する</button>
	</p>
	@endif
</div>
</form>
</article>

@endsection

@section('footer')
(c)2023
@endsection