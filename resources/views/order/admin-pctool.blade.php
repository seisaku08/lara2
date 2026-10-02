@extends('adminlte::page')
@section('title', '管理者用：新規予約作成')
@section('css')
<link href="{{ asset('/css/style.css') }}" rel="stylesheet" type="text/css">
@endsection
<script src="{{ asset('js/pctool_hide.js') }}"></script>

@section('content')
<h1 class="p-2">@yield('title')</h1>
<div class="box1000">
    <p>
        使用期間を入力すると、期間内に使用可能な機材が一覧表示されます。<br>
        管理者用予約では、セミナー開催日は本日以降、予約開始日・予約終了日の営業日制限は適用されません。
    </p>
    <p><b>＜参考＞</b>荷物の配送所要日数は<a href="http://date.kuronekoyamato.co.jp/date/Main?LINK=TK" target="_blank"><b>こちら</b></a>から検索できます（ヤマト運輸のサイトが開きます）</p>
</div>

<form method="post" action="{{ route($searchRoute) }}">
    @csrf
    <div class="container darkgray box1000">
        <div class="row">
            <div class="column col-8">
                <div class="row">
                    <div class="col text-center p-1">
                        <label>セミナー開催日（複数日の場合は初日）</label>
                        <input type="date" name="seminar_day" value="{{ $input->seminar_day }}{{ old('seminar_day') }}" onchange="submit(this.form)">
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center p-1">
                        <label>予約開始日</label>
                        <input type="date" name="from" value="{{ $input->from }}{{ old('from') }}" onchange="submit(this.form)">
                    </div>
                    <div class="col text-center p-1">
                        <label>予約終了日</label>
                        <input type="date" name="to" value="{{ $input->to }}{{ old('to') }}" onchange="submit(this.form)">
                    </div>
                </div>
            </div>
            <div class="column text-center align-middle p-1">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="show_used">
                    <label class="custom-control-label" for="show_used">予約中の機材も表示する</label>
                </div>
            </div>
        </div>
    </div>
</form>

@if(count($errors) > 0)
    <div class="container col-8">
        <ul class="text-red">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(!empty($inUse))
    {{ implode(',', $inUse) }}
@endif

<div id="list">
    {{ Form::open(['route' => 'admin.orders.create.cart', 'id' => 'admin-pctool-cart-form']) }}
    {{ Form::hidden('user_id', $user->id) }}
    {{ Form::hidden('seminar_day', $input->seminar_day) }}
    {{ Form::hidden('from', $input->from) }}
    {{ Form::hidden('to', $input->to) }}
    <table class="table table-striped table-sm" id="admin-pctool-machine-table">
        <tr class="midashi">
            <th>　</th>
            <th>ID</th>
            <th>機材番号</th>
            <th>型番</th>
            <th>導入年月</th>
            <th>OS</th>
            <th>CPU</th>
            <th>メモリ</th>
            <th>モニタ</th>
            <th>PPT</th>
            <th>カメラ</th>
            <th>BD/DVD</th>
            <th>Video</th>
            <th>toWin11</th>
            <th>備考</th>
        </tr>
        @foreach($records as $record)
            <tr class="{{ in_array($record->machine_id, $usage) ? 'trused' : '' }}">
                @php
                    $selected_arr = [];
                    if (isset($selectedIds) && is_array($selectedIds)) $selected_arr = array_map('strval', $selectedIds);
                    if (is_array(old('id'))) $selected_arr = array_unique(array_merge($selected_arr, array_map('strval', old('id'))));
                    if (is_array($input->id)) $selected_arr = array_unique(array_merge($selected_arr, array_map('strval', (array) $input->id)));
                @endphp
                <td class="text-center"><input type="checkbox" name="id[]" value="{{ $record->machine_id }}" class="{{ in_array($record->machine_id, $usage) ? 'chused' : '' }}" @if(in_array((string) $record->machine_id, $selected_arr)) checked @endif></td>
                <td>{{ $record->machine_id }}</td>
                <td><a href="{{ route('pctool.detail', $record->machine_id) }}" target="_blank">{{ $record->machine_name }}</a></td>
                <td>{{ $record->machine_spec }}</td>
                <td>{{ Carbon\Carbon::parse($record->machine_since)->format('Y-m') }}</td>
                <td>{{ $record->machine_os }}</td>
                <td>{{ $record->machine_cpu }}</td>
                <td>{{ $record->machine_memory }}</td>
                <td>{{ $record->machine_monitor }}</td>
                <td>{{ $record->machine_powerpoint }}</td>
                <td>{{ $record->machine_camera == true ? '有' : '無' }}</td>
                <td>{{ $record->machine_hasdrive == true ? '有' : '無' }}</td>
                <td>{{ $record->machine_connector }}</td>
                <td>{{ $record->machine_canto11 }}</td>
                <td>{{ $record->machine_memo }}</td>
            </tr>
        @endforeach
    </table>
    {{ Form::close() }}
</div>
<p class="text-center p-2 m-0"><button type="submit" form="admin-pctool-cart-form" class="m-1">カートに入れる</button></p>
@endsection