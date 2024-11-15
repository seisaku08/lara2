@extends('adminlte::page')
@section('title', 'マイページ')
@section('css')
<link href="{{asset('/css/style.css')}}" rel="stylesheet" type="text/css">
<script src="{{ asset('/js/finished_hide.js') }}"></script>

@endsection

@section('content')
<h1 class="text-center p-2">@yield('title')</h1>
        {{-- <?php dump($orders);?> --}}
    <div class="box1000">
        <p>こちらはマイページです。
        @can('sys-ad'){{-- 管理者に表示される --}}
        そしてあなたはシステム管理者です。
        @elsecan('daioh') {{-- 大應ユーザーに表示される --}}
        そしてあなたは大應の人です。
        @endcan
        </p>
        <p>ユーザー個人の登録セミナー一覧や、各種情報（の概要）を要約したページにする予定です。</p>
        {{-- ユーザー認証 --}}
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="text-red text-bold">
                <p class="text-sm mt-2 text-gray-800">
                    メールアドレス認証が未完了です。<br>
                    未認証のメールアドレスによるアクセスでは、システム利用に制限がかかります。
                </p>

                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('こちらをクリックしてEメール認証を完了してください。') }}
                    </button>
                </p>

                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 font-medium text-sm text-green-600">
                        {{ __('認証用のメールをご記入のアドレスに送信しました。') }}
                    </p>
                @endif
            </div>
        @endif
        <h5>最新の更新情報</h5>
        <ul>
            <li>マイページの登録済みセミナー一覧をステータス別に表示するようにしました。（11/12）</li>
        </ul>
            <p><a class="" data-toggle="collapse" href="#updateinfo" role="button" aria-expanded="false" aria-controls="updateinfo">過去の更新情報（クリックで開く）</a></p>
        <div class="collapse" id="updateinfo">
            <div class="card card-body">
              <ul>
            <li>ドメイン登録が完了しました。<br>当サイトのアドレスは、<a href="https://daioh-pc.com/">https://daioh-pc.com/</a>となります。（6/8）</li>
            <li>選択機材情報ページにて、前の画面に戻る際カートが空にならなくなりました。（6/9）</li>
            <li>機材検索ページにて、セミナー開催日・予約開始日・予約終了日欄に対する入力規則を説明文に準じる通りに設定しました。（6/14）</li>
            <li>メール送信機能周りを実装しました。（6/20）</li>
            <ul>
                <li>登録時メール認証</li>
                <li>予約登録時の返送メール</li>
                <li>リマインダメール</li>
            </ul>メール認証機能の実装に伴い、<span class="text-red text-bold">未認証の場合マイページとユーザー情報変更以外の機能が使用できなくなります。</span>
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <br>マイページよりメール認証を行っていただきますよう、よろしくお願いいたします。<br>
                ※既にメール認証を行っていただいている場合、認証を求める文章は表示されません。
            @endif
            <li>予約一覧ページを作成しました。（6/22）<br>閲覧は全てのユーザーが可能ですが、内容の編集は予約者本人に限り行えます。</li>
            <li>依頼フォームの項目を追加しました。（6/22）</li>
            <li>機材検索時に予約中機材の表示機能を追加しました。（6/22）</li>
            <li>セミナー開催9営業日前に送られるリマインドメールにおいて、当該セミナー情報へのリンクURLが誤って生成される不具合を修正しました。（6/26）</li>
            <li>機材使用状況一覧（旧来の管理表と同等の表）出力機能、操作マニュアル表示機能を実装しました。（7/14）</li>
            <li>予約詳細画面において、機材の追加・削除ができるようになりました。（7/14）</li>
            <li>予約登録時の日程に関するルールを変更しました。詳細は「機材予約フォーム」ページからご覧ください。（2024/4/1）</li>
        </ul>
    </div>
</div>

        <h5>今後の予定</h5>
        <ul>
        </ul>
        </p>
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    <h4 class="text-bold">登録済みセミナー</h4>
    <div class="container box1000">
        <form method="get" action="/order.index">
            {{-- <form method="post" action="/order"> --}}
        @csrf
            {{-- <div class="row border">
            <div class="col">並び方を変える
                {{ Form::radio('orderby', 'seminar_name') }}
            </div>
            <div class="col"><input type="submit"></div>
            </div> --}}
        <div class="row border">
            <div class="col-12 text-center bg-info"><label>進行中（貸出中）の予約</label></div>
        </div>
        <div class="row border bg-secondary">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($sent))
        @foreach($sent as $order)
        <div class="row border {{$order->user_id == 2? "text-danger bg-warning" : ""}}">
            <div class="col-3">{{$order->order_use_from}}～{{$order->order_use_to}}</div>
            <div class="col-2 text-bold"><a href="order/detail/{{$order->order_id}}">{{$order->order_no}}</a></div>
            <div class="col">{{$order->seminar_name}}</div>
            <div class="col-2 {{$order->user_id == 2? "text-danger text-bold" : ""}}">{{$order->order_status}}</div>
        </div>
        @endforeach
    @else
        <div class="row">
            <div class="col">データはありません。</div>
            <div class="col"></div>
            <div class="col"></div>
            <div class="col"></div>
        </div>
    @endif

    </div>
    
<br>

    <div class="container box1000">
        <form method="get" action="/order.index">
            {{-- <form method="post" action="/order"> --}}
        @csrf
            {{-- <div class="row border">
            <div class="col">並び方を変える
                {{ Form::radio('orderby', 'seminar_name') }}
            </div>
            <div class="col"><input type="submit"></div>
            </div> --}}
        <div class="row border">
            <div class="col-12 text-center bg-info"><label>受付済の予約</label></div>
        </div>
        <div class="row border bg-secondary">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($accept))
        @foreach($accept as $order)
        <div class="row border {{$order->user_id == 2? "text-danger bg-warning" : ""}}">
            <div class="col-3">{{$order->order_use_from}}～{{$order->order_use_to}}</div>
            <div class="col-2 text-bold"><a href="order/detail/{{$order->order_id}}">{{$order->order_no}}</a></div>
            <div class="col">{{$order->seminar_name}}</div>
            <div class="col-2 {{$order->user_id == 2? "text-danger text-bold" : ""}}">{{$order->order_status}}</div>
        </div>
        @endforeach
    @else
        <div class="row">
            <div class="col">データはありません。</div>
            <div class="col"></div>
            <div class="col"></div>
            <div class="col"></div>
        </div>
    @endif

    </div>
    
<br>

    <div class="container box1000">
        <form method="get" action="/order.index">
            {{-- <form method="post" action="/order"> --}}
        @csrf
            {{-- <div class="row border">
            <div class="col">並び方を変える
                {{ Form::radio('orderby', 'seminar_name') }}
            </div>
            <div class="col"><input type="submit"></div>
            </div> --}}
        <div class="row border">
            <div class="col-12 text-center bg-info">
                <label>終了済の予約
                    <div class="custom-control custom-switch"><input type="checkbox" class="custom-control-input" id="show_finished" >
                        <label class="custom-control-label" for="show_finished">表示する</label>
                    </div>
                </label>
            </div>
        </div>
    <div id="finishedlist" class="hidden">
        <div class="row border bg-secondary">
            <div class="col-3"><label>期間</label></div>
            <div class="col-2"><label>予約No.</label></div>
            <div class="col"><label>セミナー名</label></div>
            <div class="col-2"><label>現在の状態</label></div>
        </div>
        {{ Form::close() }}
    @if(isset($end))
        @foreach($end as $order)
        <div class="row border {{$order->user_id == 2? "text-danger bg-warning" : ""}}">
            <div class="col-3">{{$order->order_use_from}}～{{$order->order_use_to}}</div>
            <div class="col-2 text-bold"><a href="order/detail/{{$order->order_id}}">{{$order->order_no}}</a></div>
            <div class="col">{{$order->seminar_name}}</div>
            <div class="col-2 {{$order->user_id == 2? "text-danger text-bold" : ""}}">{{$order->order_status}}</div>
        </div>
        @endforeach
    @else
        <div class="row">
            <div class="col">データはありません。</div>
            <div class="col"></div>
            <div class="col"></div>
            <div class="col"></div>
        </div>
    @endif
    </div>
    </div>
    </div>
    　
@endsection