@extends('adminlte::page')
@section('title', 'マイページ')
@section('css')
<link href="/css/style.css" rel="stylesheet" type="text/css">

@endsection

@section('content')
<h1 class="text-center p-2">@yield('title')</h1>
        {{-- <?php dump($orders);?> --}}
    <div class="box1000">
        <p>こちらはマイページです。</p>
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
        <h5><a class="" data-toggle="collapse" href="#updateinfo" role="button" aria-expanded="false" aria-controls="updateinfo">更新情報（クリックで開く）</a></h5>
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
    <table id="kizai2" class="table table-striped table-sm caption-top">
        <thead class="thead-light">
            <tr>
                <th scope="col">期間</td>
                <th scope="col">予約No. </td>
                <th scope="col">セミナー名</td>
            </tr>
        </thead>
        @if(isset($orders))
        {{-- <?php dump($orders);?> --}}
            @foreach($orders as $order)
                <tr>
                    <td class="kizai-left">{{$order->order_use_from}}～{{$order->order_use_to}}</td>
                    <td class="kizai-right"><a href="order/detail/{{$order->order_id}}" target="_blank">{{$order->order_no}}</a></td>
                    <td class="kizai-right">{{$order->seminar_name}}</td>
                </tr>
            @endforeach
        @else
            <td class="kizai-left">データはありません。</td>
            <td class="kizai-right"></td>
            <td class="kizai-right"></td>
        @endif

    </table>
    　
@endsection