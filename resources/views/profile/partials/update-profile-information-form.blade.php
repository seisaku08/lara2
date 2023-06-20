<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('ユーザー情報確認・変更') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("ユーザー情報を変更する場合は、以下の欄に記入してください。") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>
    <form id="pageback" method="post" action="{{ route('profile.pageback') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label class="block font-medium text-sm text-gray-700">お名前<span style="color: red; font-size: smaller;">＊必須</span></label>
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <label class="block font-medium text-sm text-gray-700">Email<span style="color: red; font-size: smaller;">＊必須</span></label>
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Eメール認証が未完了です。') }}

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
        </div>

        <div>
            <label class="block font-medium text-sm text-gray-700" for="user_group">所属<span style="color: red; font-size: smaller;">＊必須</span></label>
            <x-text-input id="user_group" name="user_group" type="text" class="mt-1 block w-full" :value="old('user_group', $user->user_group)" required autofocus autocomplete="user_group" />
            <x-input-error class="mt-2" :messages="$errors->get('user_group')" />
        </div>

        <div>
            <label class="block font-medium text-sm text-gray-700" for="user_tel">'連絡先電話番号<span style="color: red; font-size: smaller;">＊必須</span></label>
            <x-text-input id="user_tel" name="user_tel" type="text" class="mt-1 block w-full" :value="old('user_tel', $user->user_tel)" required autofocus autocomplete="user_tel" />
            <x-input-error class="mt-2" :messages="$errors->get('user_tel')" />
        </div>

        <div class="flex items-center gap-4">
            <button form="pageback" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">{{ __('前の画面に戻る') }}</button>
            <x-primary-button>{{ __('保存') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
