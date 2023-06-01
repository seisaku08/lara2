@extends('adminlte::page')
@section('title', 'カート')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1>@yield('title')</h1>

        @csrf
{{-- <?php dump($input,);?> --}}
        <table id="kizai" class="container table table-sm table-striped col-8">
            <thead class="text-center">
                <th scope="col" colspan="4">選択機材情報</th>
            </thead>
            <tr>
                <td class="text-center" colspan="2">使用開始日:{{ $from }}</td>
                <td class="text-center" colspan="2">使用終了日:{{ $to }}</td>
            </tr>
            <tr>
                <td></td>
                <td>機材番号</td>
                <td>機種</td>
                <td>削除</td>
            </tr>
        @foreach($CartData as $key => $data)
            <tr>
                <th scope="row">{{$key +=1}}</th>
                <td>{{$data->machine_id}}</td>
                <td>{{$data->machine_name}}</td>
                <td>
                    {{Form::open(['route'=>['delCart']])}}
                        {{Form::submit('削除', ['name' => 'delete_machine_id', 'class' => 'btn btn-danger p-1'])}}
                        {{Form::hidden('machine_id', $data->machine_id)}}
                    {{Form::close()}}
                </td>
            </tr>
        @endforeach
        </table>

        {{Form::open(['route'=>'sendto'])}}
        {{ Form::hidden('order_use_from', $from) }}
        {{ Form::hidden('order_use_to', $to) }}
        {{ Form::hidden('id', null) }}
        <p class="text-center">
                <button type="submit" name="back" value="back">前の画面に戻る（カートは空になります）</button>
                <button type="submit" name="submit" value="submit">イベント情報登録へ</button>
            </p>
        {{Form::close()}}

        </form>
    </div>
@endsection