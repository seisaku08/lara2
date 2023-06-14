@extends('adminlte::page')
@section('title', '機材発送依頼フォーム')
@section('css')
{{-- <link href="/css/style.css" rel="stylesheet" type="text/css"> --}}

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>

    <div class="container">
        <link href="/css/sendstyle.css" rel="stylesheet" type="text/css">
        <script src="/js/number.js"></script>
        <script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
     
    @if(count($errors)>0)
    <div>
        <ul>
            @foreach($errors->all() as $error)
                <li class="red">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    {{ Form::open(['url'=>'confirm', 'id' => 'sendto']) }}
        <table id="form">
            <tr class="midashi">
                <th colspan="4">ご担当者様情報</th>
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
                <th>セミナー情報</th>
            </tr>
           <tr>
                <td class="w30"><label>セミナー開催日</label></td>
                <td class="w25">{{ $input->seminar_day }}{{ Form::hidden('seminar_day', $input->seminar_day) }}{{ old('seminar_day') }}</td>
            </tr>
            <tr>
                <td class="w30"><label>使用開始日:</label></td>
                <td class="w25">{{ $input->order_use_from }}{{ Form::hidden('order_use_from', $input->order_use_from) }}{{ old('order_use_from') }}</td>
            </tr>
            <tr>
                <td class="w30"><label>使用終了日:</label></td>
                <td class="w25">{{ $input->order_use_to }}{{ Form::hidden('order_use_to', $input->order_use_to) }}{{ old('order_use_to') }}</td>
            </tr>
            <tr>
                <td class="w30"><label>セミナー名</label><span class="red small">＊必須</span></td>
                <td class="w50"><input type="text" name="seminar_name" placeholder="" value="{{old('seminar_name')}}"></td>
            </tr>
            <tr class="midashi">
                <th>配送先情報</th>
            </tr>
            <tr>
                <td class="w100">配送先情報を後日入力する場合は、チェックボックスにチェックを入れてください。→<input type="checkbox" name="seminar_venue_pending" placeholder="" value="true"{{ old('seminar_venue_pending') == true ? ' checked' : '' }}></td>
            </tr>
            <tr>
                <td class="w30"><label>郵便番号</label><span class="red small">＊必須</span></td>
                <td class="w40">
                    <input type="text" name="venue_zip" id="zip" maxlength="8" placeholder="例）1010047" oninput="value = NUM(value)" value="{{old('venue_zip')}}">
                    <button type="button" onclick="AjaxZip3.zip2addr(venue_zip,'','venue_addr1','venue_addr1');">住所を自動入力</button>
                </td> 
            </tr>
            <tr>
                <td class="w30"><label>住所</label><span class="red small">＊必須</span></td>
                <td class="w50"><input type="text" name="venue_addr1" placeholder="例）東京都千代田区内神田1-7-5" value="{{old('venue_addr1')}}"></td>
            </tr>
            <tr>
               <td class="w30"><label>施設・ビル名</label></td>
                <td class="w50"><input type="text" name="venue_addr2" placeholder="例）旭栄ビル 2階" value="{{old('venue_addr2')}}"></td>
            </tr>
            <tr>
                <td class="w30"><label>会社・部門名１</label></td>
                <td class="w50"><input type="text" name="venue_addr3" placeholder="例）株式会社 大應" value="{{old('venue_addr3')}}"></td>
            </tr>
            <tr>
                <td class="w30"><label>会社・部門名２</label></td>
                <td class="w50"><input type="text" name="venue_addr4" placeholder="例）●●部" value="{{old('venue_addr4')}}"></td>
            </tr>
            <tr>
                <td class="w30"><label>配送先担当者</label><span class="red small">＊必須</span></td>
                <td class="w40"><input type="text" name="venue_name" placeholder="" value="{{old('venue_name')}}"></td>
            </tr>
            <tr>
                <td class="w30"><label>配送先電話番号</label><span class="red small">＊必須</span></td>
                <td class="w40">
                    <input type="tel" name="venue_tel" placeholder="例）0332921488 / 03-3292-1488" oninput="value = NUM(value)" value="{{old('venue_tel')}}">
                </td>
            </tr>
            <tr>
                <td class="w30"><label>到着希望日時</label><span class="red small">＊必須</span></td>
                <td class="w40">
                    <input type="date" name="shipping_arrive_day" placeholder="" value="{{old('shipping_arrive_day')}}">
                    <select name="shipping_arrive_time">
                        <option value="指定なし"{{ old('shipping_arrive_time') == "指定なし" ? ' selected' : '' }}>指定なし</option>
                        <option value="午前中"{{ old('shipping_arrive_time') == "午前中" ? ' selected' : '' }}>午前中</option>
                        <option value="14時～16時"{{ old('shipping_arrive_time') == "14時～16時" ? ' selected' : '' }}>14時～16時</option>
                        <option value="16時～18時"{{ old('shipping_arrive_time') == "16時～18時" ? ' selected' : '' }}>16時～18時</option>
                        <option value="18時～20時"{{ old('shipping_arrive_time') == "18時～20時" ? ' selected' : '' }}>18時～20時</option>
                        <option value="20時～21時"{{ old('shipping_arrive_time') == "20時～21時" ? ' selected' : '' }}>20時～21時</option>
                    </select>
                </td>
            </tr>
            <tr>
               <td class="w30"><label>返送機材発送予定日</label><span class="red small">＊必須</span></td>
                <td class="w25"><input type="date" name="shipping_return_day" placeholder="" value="{{old('shipping_return_day')}}"></td>
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
                    @foreach($records as $record)
                    <div class="row">
                        <input type="hidden" name="id[]" value="{{$record->machine_id}}">
                        <div class="col-2">{{$record->machine_id}}</div>
                        <div class="col-10">{{$record->machine_name}}</div>
                    </div>
                    @endforeach
                </td>
            </tr>
        </table>
    {{ Form::Close() }}
        <p class="p-2">
            <button type="submit" id="hidebutton" name="dummy" value="dummy" disabled>ダミー</button>
            <button type="submit" name="back" value="back">戻る</button>
            <button type="submit" name="submit" value="submit">入力内容の確認</button>
        </p>

@endsection