@extends('adminlte::page')
@section('title', 'メンテナンス詳細')
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

    <h4 class="text-bold text-center">メンテナンス用画面</h4>
    {{-- <?php dump($records, $input->session());?> --}}
    @endsection