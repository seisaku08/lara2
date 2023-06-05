@extends('adminlte::page')
@section('title', 'マイページ')
@section('content')
    <h1>@yield('title')</h1>
        {{-- <p>Under Constructiion!</p> --}}
        {{-- <?php dump($orders);?> --}}
        <p>こちらはマイページです。</p>
        <p>ユーザー個人の登録セミナー一覧や、各種情報（の概要）を要約したページにする予定です。</p>
        <h5>今後の予定</h5>
        <ul>
            <li>ドメイン登録</li>
            <li>メール送信機能周りの実装</li>
            <ul>
                <li>登録時メール認証</li>
                <li>オーダー登録時の返送メール</li>
                <li>リマインダメール</li>
            </ul>
            <li>オーダー一覧ページ</li>
        </ul>
        </p>
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
            {{-- @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div>
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
    @endif --}}
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