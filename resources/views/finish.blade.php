@extends('adminlte::page')
@section('title', 'オーダーを受け付けました')
@section('css')
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>
  <p>オーダーIDは、「{{ $order_no }}」です。</p>
  <table id="form">
      <tr class="midashi">
          <th colspan="5">ご担当者様情報</th>
      </tr>
      <tr>
        <td class="w20"><label>ご担当者氏名</label></td>
        <td class="w30">{{$user->name}}</td>
        <td class="w20"><label>所属部署</label></td>
        <td class="w30">{{$user->user_group}}</td>
    </tr>
    <tr>
        <td class="w20"><label>メールアドレス</label></td>
        <td class="w30">{{$user->email}}</td>
        <td class="w20"><label>電話番号</label></td>
        <td class="w30">{{$user->user_tel}}</td>
    </tr>
    <tr class="midashi">
        <th colspan="4">セミナー情報</th>
    </tr>
    <tr>
        <td class="w25"><label>セミナー開催日</label></td>
        <td class="w25">{{ $input->seminar_day }}</td>
    </tr>
    <tr>
        <td class="w25"><label>セミナー名</label></td>
        <td class="w50">{{ $input->seminar_name }}</td>
    </tr>
    <tr class="midashi">
        <th colspan="4">配送先情報</th>
    </tr>
    @if( $input->seminar_venue_pending == true )
    <tr>
        <td class="w100 text-center"><label>後日入力</label></td>
    </tr>
    @else
    <tr>
        <td class="w25"><label>郵便番号</label></td>
        <td class="w40">{{ $input->venue_zip }}</td> 
    </tr>
    <tr>
        <td class="w25"><label>住所</label></td>
        <td class="w50">{{ $input->venue_addr1 }}</td>
    </tr>
    <tr>
        <td class="w25"><label>施設・ビル名</label></td>
        <td class="w50">{{ $input->venue_addr2 }}</td>
    </tr>
    <tr>
        <td class="w25"><label>会社・部門名１</label></td>
        <td class="w50">{{ $input->venue_addr3 }}</td>
    </tr>
    <tr>
        <td class="w25"><label>会社・部門名２</label></td>
        <td class="w50">{{ $input->venue_addr4 }}</td>
    </tr>
    <tr>
        <td class="w25"><label>配送先担当者</label></td>
        <td class="w40">{{ $input->venue_name }}</td>
    </tr>
    <tr>
        <td class="w25"><label>配送先電話番号</label></td>
        <td class="w40">{{ $input->venue_tel }}</td>
    </tr>
    <tr>
        <td class="w25"><label>到着希望日時</label></td>
        <td class="w40">
            {{ $input->shipping_arrive_day }} {{ $input->shipping_arrive_time }}
        </td>
    </tr>
    <tr>
        <td class="w25"><label>返送機材発送予定日</label></td>
        <td class="w25">{{ $input->shipping_return_day }}</td>
    </tr>
@endif
    <tr class="midashi">
        <th colspan="4">選択機材情報</th>
    </tr>
    <tr>
        <td class="w100">
            <div class="row">
                <div class="col-6 text-center"><label>使用開始日:</label>{{ $input->order_use_from }}</div>
                <div class="col-6 text-center"><label>使用終了日:</label>{{ $input->order_use_to }}</div>
            </div>
            <div class="row">
                <div class="col-2"><label>ID</label></div>
                <div class="col-10"><label>機材番号</label></div>
            </div>
            @foreach($machines as $machine)
                {{ Form::hidden('id[]', $machine->machine_id )}}
            <div class="row">
                <div class="col-2">{{$machine->machine_id}}</div>
                <div class="col-10">{{$machine->machine_name}}</div>
            </div>
            @endforeach
        </td>
    </tr>
</table>


@endsection