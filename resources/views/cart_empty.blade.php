@extends('adminlte::page')
@section('title', '選択機材情報（カート）')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

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
                <td class="text-center" colspan="2"><label>予約開始日:</label></td>
                <td class="text-center" colspan="2"><label>予約終了日:</label></td>
            </tr>
            <tr>
                <th>#</th>
                <th>機材番号</th>
                <th>機種</th>
                <th>削除</th>
            </tr>
            <tr>
                <th colspan="4" class="text-center">カートは空です。</td>
                </td>
            </tr>
        </table>

        {{ Form::open(['route'=>'sendto']) }}
        <p class="text-center">
                <button type="submit" name="back" value="back">前の画面（機材検索）に戻る</button>
            </p>
        {{ Form::close() }}

        </form>
    </div>
@endsection