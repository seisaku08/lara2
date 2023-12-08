@extends('adminlte::page')
@section('title', '機材発送・受領確認')
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">

@endsection

@section('content')
<h1 class="text-center p-2">@yield('title')</h1>
        {{-- <?php dump($orders);?> --}}
    <div class="box1000">
        <p>機材発送・受領確認</p>
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

    <h4 class="text-bold text-center">発送手続きを行うセミナーを選択</h4>
    <div id='list2'>
    <?php use App\Libs\Common;?>
        {{ Form::open(['route' => 'shipping.order', 'id' => 'shipping2']) }}
        <table id="kizai2" class="table table-striped table-sm caption-top">
            <thead class="thead-light">
                <tr>
                    <th>　</th>
                    <th scope="col">セミナー開催日</td>
                    <th scope="col">機材到着希望日時</td>
                    <th scope="col">予約No. </td>
                    <th scope="col">状態</td>
                    <th scope="col">セミナー名</td>
                </tr>
            </thead>
            @if(isset($orders))
            {{-- <?php dump($orders);  ?> --}}
                @foreach($orders as $order)
                    <tr>
                        <td class="p-1 text-center">
                            {{ Form::open(['route'=>['shipping.order']]) }}
                            {{ Form::submit('選択', ['name' => 'shiporder', 'class' => 'btn btn-primary p-1']) }}
                            {{ Form::hidden('id', $order->order_id) }}
                            {{ Form::close() }}
                        </td>
                        <td class="kizai-left">{{$order->seminar_day}}（{{ Common::businessdaycheck($order->seminar_day) }}）</td>
                        <td class="kizai-left">{{$order->shipping_arrive_day}}（{{ Common::businessdaycheck($order->shipping_arrive_day) }}）</td>
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

    <h4 class="text-bold text-center">返却手続きを行うセミナーを選択</h4>
    <div id='list3'>
        {{ Form::open(['route' => 'shipping.return', 'id' => 'shipping3']) }}
        <table id="kizai2" class="table table-striped table-sm caption-top">
            <thead class="thead-light">
                <tr>
                    <th>　</th>
                    <th scope="col">セミナー開催日</td>
                    <th scope="col">機材返送予定日</td>
                    <th scope="col">予約No. </td>
                    <th scope="col">状態</td>
                    <th scope="col">セミナー名</td>
                </tr>
            </thead>
            @if(isset($back_order))
            {{-- <?php dump($back_order);  ?> --}}
                @foreach($back_order as $order)
                    <tr>
                        <td class="p-1 text-center">
                            {{ Form::open(['route'=>['shipping.order']]) }}
                            {{ Form::submit('選択', ['name' => 'backorder', 'class' => 'btn btn-primary p-1']) }}
                            {{ Form::hidden('id', $order->order_id) }}
                            {{ Form::close() }}
                        </td>
                        <td class="kizai-left">{{$order->seminar_day}}（{{ Common::businessdaycheck($order->seminar_day) }}）</td>
                        <td class="kizai-left">{{$order->shipping_return_day}}（{{ Common::businessdaycheck($order->shipping_return_day) }}）</td>
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
        
    @endsection