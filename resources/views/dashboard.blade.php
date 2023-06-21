@extends('adminlte::page')
@section('title', 'マイページ')
@section('content')
    <h1>@yield('title')</h1>
        {{-- <p>Under Constructiion!</p> --}}
        {{-- <?php dump($orders);?> --}}
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
        <h5>更新情報</h5>
        <ul>
            <li>ドメイン登録が完了しました。<br>当サイトのアドレスは、<a href="https://daioh-pc.com/">https://daioh-pc.com/</a>となります。（6/8）</li>
            <li>選択機材情報ページにて、前の画面に戻る際カートが空にならなくなりました。（6/9）</li>
            <li>機材検索ページにて、セミナー開催日・予約開始日・予約終了日欄に対する入力規則を説明文に準じる通りに設定しました。（6/14）</li>
            <li>メール送信機能周りを実装しました。（6/20）</li>
            <ul>
                <li>登録時メール認証</li>
                <li>オーダー登録時の返送メール</li>
                <li>リマインダメール</li>
            </ul>メール認証機能の実装に伴い、<span class="text-red text-bold">未認証の場合マイページとユーザー情報変更以外の機能が使用できなくなります。</span>
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <br>マイページよりメール認証を行っていただきますよう、よろしくお願いいたします。<br>
                ※既にメール認証を行っていただいている場合、認証を求める文章は表示されません。
            @endif
        </ul>
        <h5>今後の予定</h5>
        <ul>
            <li>オーダー一覧ページ</li>
            <li>依頼フォーム項目追加</li>
            <li>機材検索画面の機能改修</li>
        </ul>
        </p>
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
<table id="kizai2">
            <tr class="midashi">
                <th colspan="5">登録済セミナー</th>
            </tr>
            <tr>
                <td class="kizai-left">期間</td>
                <td class="kizai-right">セミナーNo.</td>
                <td class="kizai-right">セミナー名</td>
            </tr>
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