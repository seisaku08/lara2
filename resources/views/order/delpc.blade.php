@extends('adminlte::page')
@section('title', '選択機材削除 | 予約No. '.$orders->order_no)
@section('css')
<link href="{{asset('/css/sendstyle.css')}}" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>


<article id="list">
@if(count($errors)>0)
<div class="container col-8">
	<ul class="text-red">
		@foreach($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
	</ul>
</div>
@endif
{{-- <?php dd($orders, $user, $user->id == $orders->user_id);?> --}}
<table id="kizai2">
	<tr class="midashi">
		<th colspan="4">予約者情報</th>
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
</table>

<table id="kizai">
	<form action="{{ route('order.delprocess', $orders->order_id) }}" method="post" id="delpc">
		@csrf

	<tr class="midashi">
		<th colspan="4">選択機材情報</th>
	</tr>

	<tr>
		<td class="w100">
			<div class="row">
				<div class="col-1"><label>削除</label></div>
				<div class="col-1"><label>ID</label></div>
				<div class="col-10"><label>機材番号</label></div>
			</div>
			@foreach($machines as $machine)
			<div class="row">
				<div class="col-1"><input type="checkbox" name="id[]" value="{{$machine->machine_id}}"></div>
				<div class="col-1">{{$machine->machine_id}}</div>
				<div class="col-10"><a href="/pctool/detail/{{$machine->machine_id}}" target="_blank">{{$machine->machine_name}}</a></div>
			</div>
			@endforeach
		</td>
	</tr>
</table>
</article>
	<p>
		<span class="text-center">{{ Form::submit('前の画面に戻る', ['name' => 'back', 'class' => 'btn btn-primary m-2 p-1', 'form' => 'delpc']) }}</span>
		<span class="text-center">{{ Form::submit('選択した機材を削除', ['name' => 'delete_machine', 'class' => 'btn btn-danger m-2 p-1', 'form' => 'delpc', 'onclick'=>"return confirm('選択した機材を削除しますか？この操作は元に戻せません（削除後に改めて追加することは可能です）。');"]) }}</span>
	</p>
</form>

@endsection

@section('footer')
(c)2023
@endsection