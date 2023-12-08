@extends('adminlte::page')
@section('title', '選択機材情報（カート）')
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1 class="p-2">@yield('title')</h1>

        @csrf
{{-- <?php dump($input,);?> --}}
        <table id="kizai" class="container table table-sm table-striped col-8">
            {{-- <thead class="text-center">
                <th scope="col" colspan="4">選択機材情報</th>
            </thead> --}}
            <tr>
                <td class="text-center" colspan="2"><label>予約開始日:</label>{{ $from }}</td>
                <td class="text-center" colspan="2"><label>予約終了日:</label>{{ $to }}</td>
            </tr>
            <tr>
                <th>#</th>
                <th>機材番号</th>
                <th>機種</th>
                <th>削除</th>
            </tr>
        @foreach($CartData as $key => $data)
            <tr>
                <th scope="row">{{$key +=1}}</th>
                <td>{{$data->machine_id}}</td>
                <td>{{$data->machine_name}}</td>
                <td>
                    {{ Form::open(['route'=>['delCart']]) }}
                    {{ Form::submit('削除', ['name' => 'delete_machine_id', 'class' => 'btn btn-danger p-1']) }}
                    {{ Form::hidden('machine_id', $data->machine_id) }}
                    {{ Form::close() }}
                </td>
            </tr>
        @endforeach
        </table>

        {{ Form::open(['route'=>'sendto']) }}
        {{ Form::hidden('seminar_day', $seminar_day) }}
        {{ Form::hidden('order_use_from', $from) }}
        {{ Form::hidden('order_use_to', $to) }}
        {{ Form::hidden('id', null) }}
        <p class="text-center">
                <button type="submit" name="back" value="back">前の画面（機材検索）に戻る</button>
                <button type="submit" name="submit" value="submit">イベント情報登録へ</button>
            </p>
        {{ Form::close() }}

        </form>
    </div>
@endsection