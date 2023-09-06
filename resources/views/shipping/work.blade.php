@extends('adminlte::page')
@section('title', '発送詳細 | 予約No. '.$orders->order_no)
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">
<link href="/css/sendstyle.css" rel="stylesheet" type="text/css">

@endsection
{{-- <?php dd($orders); ?> --}}
@section('content')
<h1 class="text-center p-2">@yield('title')</h1>
        {{-- <?php dump($orders);?> --}}
    <div class="box1000">
        <p>以下のセミナーの発送処理を行います。</p>
        @if($orders->seminar_venue_pending == 1)
        <p class='text-danger text-bold'>配送先住所が未記入です。<br>開催期日が差し迫っている場合は、ご担当者様に連絡してください。</p>

        @else
        <p>送り状番号を入力してください。</p>
        {{ Form::open(['route'=>['shipping.invoice']]) }}
        1. <input type="text" name="no[]" class="invoice">
         2. <input type="text" name="no[]" class="invoice">
        {{--3. <input type="text" name="no[]" class="invoice">
        4. <input type="text" name="no[]" class="invoice">
        5. <input type="text" name="no[]" class="invoice"> --}}

        {{-- <p>また、発送を行った旨のメールがご担当者様に送られ<span class="text-danger text-bold">（未実装）</span>、送り状用B2・納品書excelファイルが生成されます<span class="text-danger text-bold">（未実装）</span>。</p> --}}
        <p>よろしいですか？</p>
        {{ Form::submit('選択', ['name' => 'submit', 'class' => 'btn btn-primary p-1']) }}
        {{ Form::hidden('id', $orders->order_id) }}
        {{ Form::hidden('shipping_id', $orders->shipping_id) }}
        {{ Form::close() }}
        @endif
    </div>
      @if(count($errors)>0)
      <div class="container col-8">
          <ul class="text-red">
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
          <th colspan="4">配送先情報</th>
      </tr>
      @if( $orders->seminar_venue_pending == true )
      <tr>
          <td class="w100 text-center"><label>後日入力</label></td>
      </tr>
      @else
      <tr>
          <td class="w25"><label>郵便番号</label></td>
          <td class="w40">{{ $orders->venue_zip }}</td> 
      </tr>
      <tr>
          <td class="w25"><label>住所</label></td>
          <td class="w50">{{ $orders->venue_addr1 }}</td>
      </tr>
      <tr>
          <td class="w25"><label>施設・ビル名</label></td>
          <td class="w50">{{ $orders->venue_addr2 }}</td>
      </tr>
      <tr>
          <td class="w25"><label>会社・部門名１</label></td>
          <td class="w50">{{ $orders->venue_addr3 }}</td>
      </tr>
      <tr>
          <td class="w25"><label>会社・部門名２</label></td>
          <td class="w50">{{ $orders->venue_addr4 }}</td>
      </tr>
      <tr>
          <td class="w25"><label>配送先担当者</label></td>
          <td class="w40">{{ $orders->venue_name }}</td>
      </tr>
      <tr>
          <td class="w25"><label>配送先電話番号</label></td>
          <td class="w40">{{ $orders->venue_tel }}</td>
      </tr>
      <tr>
          <td class="w25"><label>到着希望日時</label></td>
          <td class="w40">
              {{ $orders->shipping_arrive_day }} {{ $orders->shipping_arrive_time }}
          </td>
      </tr>
      <tr>
          <td class="w25"><label>返送機材発送予定日</label></td>
          <td class="w25">{{ $orders->shipping_return_day }}</td>
      </tr>
  @endif
  <tr>
      <td class="w25"><label>特記事項</label></td>
      <td class="w25">{{ $orders->shipping_special == true ? 'あり' : 'なし' }}</td>
  </tr>
  <tr>
      <td class="w25"><label>備考</label></td>
      <td class="w70">{{ $orders->shipping_note }}</td>
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
                  <div class="col-10"><a href="/pctool/detail/{{$machine->machine_id}}" target="_blank">{{$machine->machine_name}}</a></div>
              </div>
              @endforeach
          </td>
      </tr>
  </table>    {{-- <?php dump($records, $orders->session());?> --}}
    @endsection