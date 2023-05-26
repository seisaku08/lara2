@extends('adminlte::page')
@section('title', '送信内容確認画面')
@section('css')
{{-- <link href="/css/style.css" rel="stylesheet" type="text/css"> --}}

@endsection
@section('content')
<h1>@yield('title')</h1>
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
          <td class="left">ご担当者氏名</td>
          <td class="right-half">{{ $user->name }}</td>
          <td class="left">所属部署</td>
          <td class="right-half">{{ $user->user_group }}</td>
      </tr>
      <tr>
          <td class="left">メールアドレス</td>
          <td class="right-half">{{ $user->email }}
          <td class="left">電話番号</td>
          <td class="right-half">{{ $user->user_tel }}
          </td>
      </tr>
      <tr class="midashi">
          <th colspan="5">配送先情報</th>
      </tr>
       <tr>
         <td class="left">セミナー開催日</td>
          <td class="right-half">{{ $input->seminar_day }}{{ Form::hidden('seminar_day', $input->seminar_day )}}
          </td>
      </tr>
       <tr>
          <td class="left">セミナー名</td>
          <td class="right-half">{{ $input->seminar_name }}{{ Form::hidden('seminar_name', $input->seminar_name )}}
          </td>
       </tr>
      <tr>
          <td class="left"><label>郵便番号</label></td>
          <td class="right-half">{{ $input->venue_zip }}{{ Form::hidden('venue_zip', $input->venue_zip )}}
          </td>
      </tr>
      <tr>
          <td class="left"><label>住所</label></td>
          <td class="right-half">{{ $input->venue_addr1 }}{{ Form::hidden('venue_addr1', $input->venue_addr1 )}}
          </td>
          <td class="left"><label>ビル名</label></td>
          <td class="right-half">{{ $input->venue_addr2 }}{{ Form::hidden('venue_addr2', $input->venue_addr2 )}}
          </td>
      </tr>
      <tr>
          <td class="left"><label>施設名</label></td>
          <td class="right-half">{{ $input->venue_addr3 }}{{ Form::hidden('venue_addr3', $input->venue_addr3 )}}
          </td>
          <td class="left"><label>部署名</label></td>
          <td class="right-half">{{ $input->venue_addr4 }}{{ Form::hidden('venue_addr4', $input->venue_addr4 )}}
          </td>
      </tr>
      <tr>
          <td class="left"><label>配送先担当者</label></td>
          <td class="right-half">{{ $input->venue_name }}{{ Form::hidden('venue_name', $input->venue_name )}}
          </td>
      </tr>
      <tr>
          <td class="left">配達先電話番号</td>
          <td class="right-half">{{ $input->venue_tel }}{{ Form::hidden('venue_tel', $input->venue_tel )}}
          </td>
      </tr>
     <tr>
          <td class="left">到着希望日時</td>
          <td class="right-half">{{ $input->shipping_arrive_day }} {{ $input->shipping_arrive_time }}
            {{ Form::hidden('shipping_arrive_day', $input->shipping_arrive_day )}}
            {{ Form::hidden('shipping_arrive_time', $input->shipping_arrive_time )}}
          </td>
          <td class="left">返送予定日</td>
          <td class="right-half">{{ $input->shipping_return_day }}{{ Form::hidden('shipping_return_day', $input->shipping_return_day )}}
          </td>
      </tr>
    </table>
    <table id="kizai">
        <tr class="midashi">
          <th colspan="5">選択機材情報</th>
      </tr>
      <tr>
        <td>From:{{ $input->order_use_from }}{{ Form::hidden('order_use_from', $input->order_use_from ) }}</td>
        <td>To:{{ $input->order_use_to }}{{ Form::hidden('order_use_to', $input->order_use_to ) }}</td>
    </tr>
<tr>
          <td class="kizai-left">機材番号</td>
          <td class="kizai-right">機種</td>
      </tr>
    @foreach($machines as $machine)
      <tr>
          <td class="kizai-left">{{$machine->machine_id}}</td>
          <td class="kizai-right">{{$machine->machine_name}}</td>
          {{ Form::hidden('id[]', $machine->machine_id )}}
      </tr>
    @endforeach
  </table>
      <p>
          <button type="submit" name="back" value="back">戻る</button>
          <button type="submit" name="submit" value="submit">上記の内容で送信する</button>
      </p>
      </form>

@endsection