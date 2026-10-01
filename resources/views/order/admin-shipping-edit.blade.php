@extends('adminlte::page')
@section('title', '配送先情報変更 | 予約No. '.$orders->order_no)
@section('content')
<link href="{{ asset('/css/sendstyle.css') }}" rel="stylesheet" type="text/css">
<script src="/js/number.js"></script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js"></script>
<h1 class="p-2">@yield('title')</h1>
<article id="list">
    <form action="{{ route('admin.orders.shipping.update', $orders->order_id) }}" method="post">
        @csrf
        @method('put')
        <table id="kizai2">
            <tr class="midashi">
                <th colspan="5">配送先情報</th>
            </tr>
            <tr>
                <td class="w30"><label for="venue-zip">郵便番号</label><span class="red small">＊必須</span></td>
                <td class="w40">
                    <input type="text" name="venue_zip" id="venue-zip" maxlength="8" placeholder="例）1010047" oninput="value = NUM(value)" value="{{ old('venue_zip', $orders->venue_zip) }}">
                    <button type="button" onclick="AjaxZip3.zip2addr(venue_zip,'','venue_addr1','venue_addr1');">住所を自動入力</button>
                    @error('venue_zip')<div class="text-danger">{{ $message }}</div>@enderror
                </td>
            </tr>
            <tr>
                <td class="w30"><label for="venue-addr1">住所</label><span class="red small">＊必須</span></td>
                <td class="w50">
                    <input type="text" name="venue_addr1" id="venue-addr1" placeholder="例）東京都千代田区内神田1-7-5" value="{{ old('venue_addr1', $orders->venue_addr1) }}">
                    @error('venue_addr1')<div class="text-danger">{{ $message }}</div>@enderror
                </td>
            </tr>
            <tr>
                <td class="w30"><label for="venue-addr2">施設・ビル名</label></td>
                <td class="w50"><input type="text" name="venue_addr2" id="venue-addr2" placeholder="例）旭栄ビル 2階" value="{{ old('venue_addr2', $orders->venue_addr2) }}">@error('venue_addr2')<div class="text-danger">{{ $message }}</div>@enderror</td>
            </tr>
            <tr>
                <td class="w30"><label for="venue-addr3">会社・部門名１</label></td>
                <td class="w50"><input type="text" name="venue_addr3" id="venue-addr3" placeholder="例）株式会社 大應" value="{{ old('venue_addr3', $orders->venue_addr3) }}">@error('venue_addr3')<div class="text-danger">{{ $message }}</div>@enderror</td>
            </tr>
            <tr>
                <td class="w30"><label for="venue-addr4">会社・部門名２</label></td>
                <td class="w50"><input type="text" name="venue_addr4" id="venue-addr4" placeholder="例）●●部" value="{{ old('venue_addr4', $orders->venue_addr4) }}">@error('venue_addr4')<div class="text-danger">{{ $message }}</div>@enderror</td>
            </tr>
            <tr>
                <td class="w30"><label for="venue-name">配送先担当者</label><span class="red small">＊必須</span></td>
                <td class="w40"><input type="text" name="venue_name" id="venue-name" value="{{ old('venue_name', $orders->venue_name) }}">@error('venue_name')<div class="text-danger">{{ $message }}</div>@enderror</td>
            </tr>
            <tr>
                <td class="w30"><label for="venue-tel">配送先電話番号</label><span class="red small">＊必須</span></td>
                <td class="w40">
                    <input type="tel" name="venue_tel" id="venue-tel" placeholder="例）0332921488 / 03-3292-1488" oninput="value = NUM(value)" value="{{ old('venue_tel', $orders->venue_tel) }}">
                    @error('venue_tel')<div class="text-danger">{{ $message }}</div>@enderror
                </td>
            </tr>
            <tr>
                <td class="w30"><label for="shipping-arrive-day">到着希望日時</label><span class="red small">＊必須</span></td>
                <td class="w40">
                    <input type="date" name="shipping_arrive_day" id="shipping-arrive-day" value="{{ old('shipping_arrive_day', $orders->shipping_arrive_day) }}">
                    <select name="shipping_arrive_time">
                        @foreach(['指定なし', '午前中', '14時～16時', '16時～18時', '18時～20時', '20時～21時'] as $arrivalTime)
                            <option value="{{ $arrivalTime }}" {{ old('shipping_arrive_time', $orders->shipping_arrive_time) == $arrivalTime ? 'selected' : '' }}>{{ $arrivalTime }}</option>
                        @endforeach
                    </select>
                    @error('shipping_arrive_day')<div class="text-danger">{{ $message }}</div>@enderror
                    @error('shipping_arrive_time')<div class="text-danger">{{ $message }}</div>@enderror
                </td>
            </tr>
            <tr>
                <td class="w30"><label for="shipping-return-day">返送機材発送予定日</label><span class="red small">＊必須</span></td>
                <td class="right-half">
                    <input type="date" name="shipping_return_day" id="shipping-return-day" value="{{ old('shipping_return_day', $orders->shipping_return_day) }}">
                    @error('shipping_return_day')<div class="text-danger">{{ $message }}</div>@enderror
                </td>
            </tr>
            <tr>
                <td class="w100">
                    <label for="shipping-special">事前搬入申請等、荷扱いに特記すべき事項がある場合はチェックを入れてください。→</label>
                    <input type="checkbox" class="dekai" name="shipping_special" id="shipping-special" value="1" {{ old('shipping_special', $orders->shipping_special) ? 'checked' : '' }}>
                </td>
            </tr>
            <tr>
                <td class="w30"><label for="shipping-note">備考</label></td>
                <td class="w70">
                    <textarea class="fullsize" name="shipping_note" id="shipping-note" rows="4" placeholder="特記事項やメモ等、申し送る必要のある事柄をお書きください。（200文字まで）">{{ old('shipping_note', $orders->shipping_note) }}</textarea>
                    @error('shipping_note')<div class="text-danger">{{ $message }}</div>@enderror
                </td>
            </tr>
        </table>
        <div class="text-center">
            <button type="submit" name="back" value="前の画面に戻る" class="btn btn-primary m-2 p-1">前の画面に戻る</button>
            <button type="submit" class="btn btn-primary m-2 p-1">変更を送信する</button>
        </div>
    </form>
</article>
@endsection

