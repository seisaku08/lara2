@extends('adminlte::page')
@section('title', 'カート')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1>@yield('title')</h1>

        @csrf
{{-- <?php dump($input,$CartData);?> --}}
        <table id="kizai">
            <tr class="midashi">
                <th colspan="5">選択機材情報</th>
            </tr>
            <tr>
                <td>From:{{ $from }}</td>
                <td>To:{{ $to }}</td>
            </tr>
            <tr>
                <td></td>
                <td>機材番号</td>
                <td>機種</td>
                <td>削除</td>
            </tr>
        @foreach($CartData as $key => $data)
            <tr>
                <td>{{$key +=1}}</td>
                <td>{{$data->machine_id}}</td>
                <td>{{$data->machine_name}}</td>
                <td>
                    {{Form::open(['route'=>['delCart']])}}
                        {{Form::submit('削除', ['name' => 'delete_machine_id', 'class' => 'btn btn-danger'])}}
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
        <p>
                <button type="submit" name="back" value="back">前の画面に戻る（カートは空になります）</button>
                <button type="submit" name="submit" value="submit">イベント情報登録へ</button>
            </p>
        {{Form::close()}}

        </form>
    </div>
@endsection