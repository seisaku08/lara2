@extends('adminlte::page')
@section('title', '機材予約フォーム')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">
@endsection
<script src="{{ asset('js/pctool_hide.js') }}"></script>

@section('content')
<h1 class="p-2">@yield('title')</h1>
  <div class="box1000 ">
    <p>
      使用期間を入力すると、期間内に使用可能な機材が一覧表示されます。<br>
      準備・配送に要する期間がございますので、入力できる年月日には制限がございます。<a class="" data-toggle="collapse" href="#scheduleinfo" role="button" aria-expanded="false" aria-controls="scheduleinfo">詳細（クリックで開く）</a>
    </p>
    <div class="collapse" id="scheduleinfo">
      <div class="card card-body">
        <p>
        「セミナー開催日（複数日開催の場合、その初日）」は<b>本日より4営業日以降（{{ App\Libs\Common::dayafter(today(),4)->isoFormat('YYYY年M月D日（ddd）'); }}）</b><br>
        「予約開始日」は<b>セミナー開催日の3営業日以前（上記の場合、{{ App\Libs\Common::dayafter(today(),1)->isoFormat('YYYY年M月D日（ddd）'); }}）</b><br>
        「予約終了日」は<b>セミナー開催日（複数日開催、または連続使用の場合はその最終日）の3営業日以降（上記の場合、{{ App\Libs\Common::dayafter(today(),7)->isoFormat('YYYY年M月D日（ddd）'); }}）</b>
      </p>
      </div>
    </div>
  </div>
  <form method="post" action="/pctool">
    @csrf
<div class="container darkgray box1000">
    <div class="row">
      <div class="column col-8">
        <div class="row">
          <div class="col text-center p-1">
            <label>セミナー開催日</label>
            <input type="date" name="seminar_day" value="{{$input->seminar_day}}{{ old('seminar_day') }}" onchange="submit(this.form)">
          </div>
        </div>
        <div class="row">
          <div class="col text-center p-1">
          <label>予約開始日</label>
          <input type="date" name="from" value="{{$input->from}}{{ old('from') }}" onchange="submit(this.form)">
          </div>
          <div class="col text-center p-1">
            <label>予約終了日</label>
            <input type="date" name="to" value="{{$input->to}}{{ old('to') }}" onchange="submit(this.form)">
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

    {{-- <table id="days">
      <tr>
        <td colspan="2" class="center">
          <label>セミナー開催日</label>
          <input type="date" name="seminar_day" value="{{$input->seminar_day}}{{ old('seminar_day') }}" onchange="submit(this.form)">
        </td>
        <td rowspan="2" class="text-center"><button type="submit" form="pctool" class="m-1">カートに入れる</button><br><button type="button" onclick="location.href='/cart'" class="m-1">カートの中を見る</button></td>
      </tr>
      <tr>
        <td class="center">
          <label>予約開始日</label>
          <input type="date" name="from" value="{{$input->from}}{{ old('from') }}" onchange="submit(this.form)">
        </td>
        <td class="center">
          <label>予約終了日</label>
          <input type="date" name="to" value="{{$input->to}}{{ old('to') }}" onchange="submit(this.form)">
        </td>
      </tr>
    </table>
 --}}
</form>
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
  @if(!empty($inUse))
    {{implode(',', $inUse)}}
  @endif
<div id='list'>
  {{ Form::open(['route' => 'addCart', 'id' => 'pctool']) }}
    {{ Form::hidden('user_id', $user->id) }}
    {{ Form::hidden('seminar_day', $input->seminar_day)}}
    {{ Form::hidden('from', $input->from)}}
    {{ Form::hidden('to', $input->to)}}
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
          <td class="p-1 text-center"><input type="checkbox" name="id[]" value="{{$record->machine_id}}"
            class="{{ in_array($record->machine_id, $usage)? 'chused' : '' }}"
            @if ($input->id <> null)
              {{ in_array($record->machine_id, $input->id)? ' checked' : '' }}
            @endif></td>
          <td class="p-1">{{$record->machine_id}}</td>
          <td class="p-1"><a href="pctool/detail/{{$record->machine_id}}" target="_blank">{{$record->machine_name}}</a></td>
          {{-- <td class="p-1">{{$record->machine_status}}</td> --}}
          <td class="p-1">{{$record->machine_spec}}</td>
          {{-- <td>{{$record->machine_os}}</td>
          <td>{{$record->machine_since}}</td>
          <td>{{$record->machine_memo}}</td> --}}
        </tr>
      @endforeach
    </table>
</div>
  <p class="text-center p-2 m-0"><button type="submit" form="pctool" class="m-1">カートに入れる</button></p>
  {{ Form::Close() }}
</div>
@endsection

{{-- @section('footer')
(c)2023 Dai-oh Co., Ltd.
@endsection --}}
