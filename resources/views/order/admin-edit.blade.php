@extends('adminlte::page')
@section('title', 'セミナー情報変更 | 予約No. '.$orders->order_no)
@section('content')
<link href="{{ asset('/css/sendstyle.css') }}" rel="stylesheet" type="text/css">
<h1 class="p-2">@yield('title')</h1>
<article id="list">
    <form action="{{ route('admin.orders.update', $orders->order_id) }}" method="post">
        @csrf
        @method('put')
        {{ Form::hidden('id', $orders->order_id) }}
        {{ Form::hidden('order_id', $orders->order_id) }}
        <table id="kizai2">
            <tr class="midashi">
                <th colspan="5">セミナー情報</th>
            </tr>
            <tr>
                <td class="w30"><label>セミナー名</label><span class="red small">＊必須</span></td>
                <td class="w50">
                    <input type="text" name="seminar_name" value="{{ old('seminar_name', $orders->seminar_name) }}">
                    @error('seminar_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </td>
            </tr>
            <tr>
                <td class="w30"><label>セミナー開催日</label><span class="red small">＊必須</span></td>
                <td class="w25">
                    <input type="date" name="seminar_day" value="{{ old('seminar_day', $orders->seminar_day) }}">
                    @error('seminar_day')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </td>
            </tr>
            <tr>
                <td class="w30"><label>予約期間</label><span class="red small">＊必須</span></td>
                <td class="w70">
                    <label for="order-use-from">予約開始日</label>
                    <input id="order-use-from" type="date" name="order_use_from" required
                        @if($availablePeriodStart) min="{{ $availablePeriodStart }}" @endif
                        @if($availablePeriodEnd) max="{{ $availablePeriodEnd }}" @endif
                        value="{{ old('order_use_from', $orders->order_use_from) }}">
                    <label for="order-use-to">予約終了日</label>
                    <input id="order-use-to" type="date" name="order_use_to" required
                        @if($availablePeriodStart) min="{{ $availablePeriodStart }}" @endif
                        @if($availablePeriodEnd) max="{{ $availablePeriodEnd }}" @endif
                        value="{{ old('order_use_to', $orders->order_use_to) }}">
                    @if($machineCount > 0)
                        <p class="text-muted mt-2 mb-1">
                            機材が空いている最大期間（現在の予約期間を含む）:
                            {{ $availablePeriodStart ?? '制限なし' }} ～ {{ $availablePeriodEnd ?? '制限なし' }}
                        </p>
                    @else
                        <p class="text-muted mt-2 mb-1">この予約に機材が登録されていないため、機材空き状況による期間制限はありません。</p>
                    @endif
                    @if($periodHasConflict)
                        <div class="alert alert-warning mt-2 mb-1">現在の予約期間内に、選択機材を使用する別の予約があります。空いている日程へ変更してください。</div>
                    @endif
                    @error('order_use_from')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('order_use_to')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </td>
            </tr>
        </table>
        <div class="text-center">
            <button type="submit" name="back" value="前の画面に戻る" class="btn btn-primary m-2 p-1">前の画面に戻る</button>
            <button type="button" class="btn btn-primary m-2 p-1" data-toggle="modal" data-target="#confirmAdminSeminarUpdate">変更を送信する</button>
        </div>

        <div class="modal fade" id="confirmAdminSeminarUpdate" tabindex="-1" role="dialog" aria-labelledby="confirmAdminSeminarUpdateTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmAdminSeminarUpdateTitle">変更内容の確認</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">セミナー情報と予約期間を変更します。よろしいですか？</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                        <button type="submit" class="btn btn-primary">変更する</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</article>
@endsection

