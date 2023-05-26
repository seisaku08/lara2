@extends('adminlte::page')
@section('title', 'オーダーを受け付けました')
@section('css')
{{-- <link href="/css/style.css" rel="stylesheet" type="text/css"> --}}

@endsection
@section('content')
<h1>@yield('title')</h1>
  <p>オーダーIDは、「{{ $order_no }}」です。</p>
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
          <td class="right-half">{{ $input->seminar_day }}
          </td>
      </tr>
       <tr>
          <td class="left">セミナー名</td>
          <td class="right-half">{{ $input->seminar_name }}
          </td>
       </tr>
      <tr>
          <td class="left"><label>郵便番号</label></td>
          <td class="right-half">{{ $input->venue_zip }}
          </td>
      </tr>
      <tr>
          <td class="left"><label>住所</label></td>
          <td class="right-half">{{ $input->venue_addr1 }}
          </td>
          <td class="left"><label>ビル名</label></td>
          <td class="right-half">{{ $input->venue_addr2 }}
          </td>
      </tr>
      <tr>
          <td class="left"><label>施設名</label></td>
          <td class="right-half">{{ $input->venue_addr3 }}
          </td>
          <td class="left"><label>部署名</label></td>
          <td class="right-half">{{ $input->venue_addr4 }}
          </td>
      </tr>
      <tr>
          <td class="left"><label>配送先担当者</label></td>
          <td class="right-half">{{ $input->venue_name }}
          </td>
      </tr>
      <tr>
          <td class="left">配達先電話番号</td>
          <td class="right-half">{{ $input->venue_tel }}
          </td>
      </tr>
     <tr>
          <td class="left">到着希望日時</td>
          <td class="right-half">{{ $input->shipping_arrive_day }}　{{  $input->shipping_arrive_time }}
          </td>
          <td class="left">返送予定日</td>
          <td class="right-half">{{ $input->shipping_return_day }}
          </td>
      </tr>
    </table>
    <table id="kizai">
        <tr class="midashi">
          <th colspan="5">選択機材情報</th>
      </tr>
      <tr>
          <td class="kizai-left">機材番号</td>
          <td class="kizai-right">機種</td>
      </tr>
    @foreach($machines as $machine)
      <tr>
          <td class="kizai-left">{{$machine->machine_id}}</td>
          <td class="kizai-right">{{$machine->machine_name}}</td>
      </tr>
    @endforeach
  </table>

@endsection