@extends('adminlte::page')
@section('title', '機材発送依頼フォーム')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection
@section('content')
<h1>@yield('title')</h1>

          <p>
            使用期間を入力すると、期間内に使用可能な機材が一覧表示されます。
          </p>
          <p>
            「使用開始日」は<b>セミナー開催日の3営業日前以前</b>を、「使用終了日」は<b>セミナー開催日の3営業日後以降</b>を入力してください。<br>
            例）セミナー開催日が「2023年6月13日（火）」の場合<br>
            「使用開始日」は2023年6月8日（木）以前<br>
            「使用終了日」は2023年6月16日（金）以降
          </p>
<div class="container">
  <form method="post" action="/pctool">
    @csrf
    <table align="center">
      </tr>
      <tr>
        <th>使用開始日</th>
        <th>　</th>
        <th>使用終了日</th>
      </tr>
      <tr>
        <td>
          <input type="date" name="from" value="{{$input->from}}">
        </td>
        <td>
          ～
        </td>
        <td>
          <input type="date" name="to" value="{{$input->to}}">
        </td>
      </tr>
      <tr>
        <td colspan="3" align="center">
          <input type="submit" value="検索">
        </td>
      </tr>
    </table>
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