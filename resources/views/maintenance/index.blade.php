@extends('adminlte::page')
@section('title', '機材メンテナンス')
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">

@endsection

@section('content')
<h1 class="text-center p-2">@yield('title')</h1>
        {{-- <?php dump($orders);?> --}}
    <div class="box1000">
        <p>機材メンテナンス用ページ</p>
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

    <h4 class="text-bold text-center">セミナーからメンテナンス機材を選択する</h4>
    <div id='list2'>
      <?php use App\Libs\Common;?>
      <table id="kizai2" class="table table-striped table-sm caption-top">
            <thead class="thead-light">
              <tr>
                <th>　</th>
                <th scope="col">セミナー開催日</td>
                <th scope="col">予約No. </td>
                <th scope="col">状態</td>
                <th scope="col">セミナー名</td>
            </tr>
        </thead>
        </thead>
            @if(isset($orders))
            {{-- <?php dump($orders);  ?> --}}
                @foreach($orders as $order)
                    <tr>
                        <td class="p-1 text-center">
                            {{ Form::open(['route'=>['selorder']]) }}
                            {{ Form::submit('選択', ['name' => 'delete_machine_id', 'class' => 'btn btn-primary p-1']) }}
                            {{ Form::hidden('order_id', $order->order_id) }}
                            {{ Form::close() }}
                        </td>
                        <td class="kizai-left">{{$order->seminar_day}}（{{ Common::businessdaycheck($order->seminar_day) }}）</td>
                        <td class="kizai-right"><a href="order/detail/{{$order->order_id}}" target="_blank">{{$order->order_no}}</a></td>
                        <td class="kizai-right">{{$order->order_status}}</td>
                        <td class="kizai-right">{{$order->seminar_name}}</td>
                    </tr>
                @endforeach
            @else
                <td>　</td>
                <td class="kizai-left">データはありません。</td>
                <td class="kizai-right"></td>
                <td class="kizai-right"></td>
            @endif
    
        </table>
          </div>
      <h4 class="text-bold text-center">機材を個別に選択する</h4>
    
    {{-- <?php dump($records, $input->session());?> --}}
    <div id='list'>
      {{ Form::open(['route' => 'selpc', 'id' => 'selectpc']) }}
        <table class="table table-striped">
          <tr class="midashi">
            <th>　</th>
            <th>ID</th>
            <th>機材番号</th>
            <th>規格</th>
          </tr>
          @foreach($records as $record)
            <tr class="">
              <td class="p-1 text-center"><input type="checkbox" name="id[]" value="{{$record->machine_id}}" class=""></td>
              <td class="p-1">{{$record->machine_id}}</td>
              <td class="p-1"><a href="pctool/detail/{{$record->machine_id}}" target="_blank">{{$record->machine_name}}</a></td>
              <td class="p-1">{{$record->machine_spec}}</td>
            </tr>
          @endforeach
        </table>
    </div>
      <p class="text-center p-2 m-0"><button type="submit" form="selectpc" class="m-1">カートに入れる</button></p>
      {{ Form::Close() }}
    @endsection