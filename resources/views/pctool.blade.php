@extends('adminlte::page')
@section('title', '機材発送依頼フォーム')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1>@yield('title')</h1>

<div class="container">
  <form method="post" action="/pctool">
    @csrf
    <input type="date" name="from" value="{{$input->from}}">～<input type="date" name="to" value="{{$input->to}}">
    <input type="submit" value="検索">
  </form>
  @if(count($errors)>0)
  <div>
      <ul>
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
  {{ Form::open(['route' => 'addCart']) }}
    {{ Form::hidden('user_id', $user->id) }}
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
        <tr class="{{ in_array($record->machine_id, $usage)? 'hidden' : '' }} ">
          <td class="p-1 text-center"><input type="checkbox" name="id[]" value="{{$record->machine_id}}"{{ in_array($record->machine_id, $usage)? ' disabled' : '' }}
            @if ($input->id <> null)
              {{ in_array($record->machine_id, $input->id)? ' checked' : '' }}
            @endif></td>
          <td class="p-1">{{$record->machine_id}}</td>
          <td class="p-1"><a href="pctool/detail/{{$record->machine_id}}" target="_self">{{$record->machine_name}}</a></td>
          {{-- <td class="p-1">{{$record->machine_status}}</td> --}}
          <td class="p-1">{{$record->machine_spec}}</td>
          {{-- <td>{{$record->machine_os}}</td>
          <td>{{$record->machine_since}}</td>
          <td>{{$record->machine_memo}}</td> --}}
        </tr>
      @endforeach
    </table>
</div>
    <input type="submit">
  {{ Form::Close() }}
</div>
@endsection

@section('footer')
(c)2023
@endsection