@extends('adminlte::page')
@section('title', '送信内容確認画面')
@section('css')
{{-- <link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css"> --}}
    <link href="{{asset('/css/sendstyle.css')}}" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>
<form method="post" action="./finish">
    @csrf
    @if(count($errors)>0)
    <div>
        <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
    @endif

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
            <td class="w25"><label>セミナー名</label></td>
            <td class="w50">{{ $input->seminar_name }}{{ Form::hidden('seminar_name', $input->seminar_name )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>セミナー開催日</label></td>
            <td class="w25">{{ $input->seminar_day }}{{ Form::hidden('seminar_day', $input->seminar_day )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>予約開始日:</label></td>
            <td class="w25">{{ $input->order_use_from }}{{ Form::hidden('order_use_from', $input->order_use_from ) }}</td>
        </tr>
        <tr>
            <td class="w25"><label>予約終了日:</label></td>
            <td class="w25">{{ $input->order_use_to }}{{ Form::hidden('order_use_to', $input->order_use_to ) }}</td>
        </tr>
<tr class="midashi">
            <th colspan="4">配送先情報</th>
        </tr>
        @if( $input->seminar_venue_pending == true )
        <tr>
            {{ Form::hidden('seminar_venue_pending', $input->seminar_venue_pending )}}
            <td class="w100"><label>＊後日入力＊</label></td>
        </tr>
        @else
        <tr>
            <td class="w25"><label>郵便番号</label></td>
            <td class="w40">{{ $input->venue_zip }}{{ Form::hidden('venue_zip', $input->venue_zip )}}</td> 
        </tr>
        <tr>
            <td class="w25"><label>住所</label></td>
            <td class="w50">{{ $input->venue_addr1 }}{{ Form::hidden('venue_addr1', $input->venue_addr1 )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>施設・ビル名</label></td>
            <td class="w50">{{ $input->venue_addr2 }}{{ Form::hidden('venue_addr2', $input->venue_addr2 )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>会社・部門名１</label></td>
            <td class="w50">{{ $input->venue_addr3 }}{{ Form::hidden('venue_addr3', $input->venue_addr3 )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>会社・部門名２</label></td>
            <td class="w50">{{ $input->venue_addr4 }}{{ Form::hidden('venue_addr4', $input->venue_addr4 )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>配送先担当者</label></td>
            <td class="w40">{{ $input->venue_name }}{{ Form::hidden('venue_name', $input->venue_name )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>配送先電話番号</label></td>
            <td class="w40">{{ $input->venue_tel }}{{ Form::hidden('venue_tel', $input->venue_tel )}}</td>
        </tr>
        <tr>
            <td class="w25"><label>到着希望日時</label></td>
            <td class="w40">
                {{ $input->shipping_arrive_day }}－{{ $input->shipping_arrive_time }}
                {{ Form::hidden('shipping_arrive_day', $input->shipping_arrive_day )}}
                {{ Form::hidden('shipping_arrive_time', $input->shipping_arrive_time )}}
            </td>
        </tr>
        <tr>
            <td class="w25"><label>返送機材発送予定日</label></td>
            <td class="w25">{{ $input->shipping_return_day }}{{ Form::hidden('shipping_return_day', $input->shipping_return_day )}}</td>
        </tr>
@endif
<tr>
    {{ Form::hidden('shipping_special', $input->shipping_special )}}
    <td class="w25"><label>特記事項</label></td>
    <td class="w25">{{ $input->shipping_special == true ? 'あり' : 'なし' }}</td>
</tr>
<tr>
    {{ Form::hidden('shipping_note', $input->shipping_note )}}
    <td class="w25"><label>備考</label></td>
    <td class="w70">{{ $input->shipping_note }}</td>
</tr>

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
                    {{ Form::hidden('id[]', $machine->machine_id )}}
                <div class="row">
                    <div class="col-2">{{$machine->machine_id}}</div>
                    <div class="col-10">{{$machine->machine_name}}</div>
                </div>
                @endforeach
            </td>
        </tr>
    </table>
    <p>
        <button type="submit" name="back" value="back">戻る</button>
        <button type="submit" name="submit" value="submit">上記の内容で送信する</button>
    </p>    
</form>

@endsection