@extends('adminlte::page')
@section('title', '機材追加 | 予約No. '.$order->order_no)
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">
@endsection
<script src="{{ asset('js/pctool_hide.js') }}"></script>

@section('content')
<h1 class="p-2">@yield('title')</h1>
  <div class="box1000 ">
    <p>
      選択されたセミナーの予約期間内に使用可能な機材が一覧表示されます。<br>
      <span class="text-red">※予約期間の変更はできません。</span>
    </p>
  </div>
<div class="container darkgray box1000">
    <div class="row">
      <div class="column col-12">
        <div class="col text-center p-1">
          <span class="larger"><label>セミナー名:
          {{ $order->seminar_name }}</label></span>
        </div>
      </div>
        <div class="column col-8">
          <div class="row">
          <div class="col text-center p-1">
            <label>セミナー開催日:</label>
            <span class="larger">{{ $order->seminar_day }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col text-center p-1">
          <label>予約開始日:</label>
          <span class="larger">{{ $order->order_use_from }}</span>
        </div>
          <div class="col text-center p-1">
            <label>予約終了日:</label>
          <span class="larger">{{ $order->order_use_to }}</span>
          </div>
        </div>
      </div>
      <div class="column text-center align-middle p-1">
        <div class="custom-control custom-switch">
          <input type="checkbox" class="custom-control-input" id="show_used" ><label class="custom-control-label" for="show_used">予約中の機材も表示する</label>
        </div>
      </div>
    </div>
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

{{-- <?php dump($records,$usage,$input->from, $input->session());?> --}}
<div id='list'>
	<form action="{{ route('order.addprocess', $order->order_id) }}" method="post" id="addpc">
    @csrf
    <table class="table table-striped">
      <tr class="midashi">
        <th>　</th>
        <th>ID</th>
        <th>機材番号</th>
        {{-- <th>状態</th> --}}
        <th>規格</th>
        {{-- <th>OS/PW</th>
        <th>導入年月</th>
        <th>備考</th> --}}
      </tr>
      @foreach($records as $record)
        <tr class="{{ in_array($record->machine_id, $usage)? 'trused' : '' }} ">
          <td class="p-1 text-center"><input type="checkbox" id="{{$record->machine_id}}" name="id[]" value="{{$record->machine_id}}"
            class="{{ in_array($record->machine_id, $usage)? 'chused' : '' }}"
            {{-- @if ($input->id <> null)
              {{ in_array($record->machine_id, $input->id)? ' checked' : '' }}
            @endif --}}
            >
          </td>
          <td class="p-1">{{$record->machine_id}}</td>
          <td class="p-1"><a href="pctool/detail/{{$record->machine_id}}" target="_blank">{{$record->machine_name}}</a></td>
          {{-- <td class="p-1">{{$record->machine_status}}</td> --}}
          <td class="p-1"><label class="thin" for="{{$record->machine_id}}">{{$record->machine_spec}}</label></td>
          {{-- <td>{{$record->machine_os}}</td>
          <td>{{$record->machine_since}}</td>
          <td>{{$record->machine_memo}}</td> --}}
        </tr>
      @endforeach
    </table>
</div>
<p class="text-center">
  <span>{{ Form::submit('前の画面に戻る', ['name' => 'back', 'class' => 'btn btn-primary m-2 p-1', 'form' => 'addpc']) }}</span>
  <span>{{ Form::submit('選択した機材を追加', ['name' => 'add_machine', 'class' => 'btn btn-primary m-2 p-1', 'form' => 'addpc','onclick'=>"return confirm('選択した機材を追加しますか？');"]) }}</span>
</p>

{{ Form::Close() }}
</div>
@endsection

{{-- @section('footer')
(c)2023 Dai-oh Co., Ltd.
@endsection --}}
