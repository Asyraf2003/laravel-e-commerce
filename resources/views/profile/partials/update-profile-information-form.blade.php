<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{ __('Profile Information') }}</h4>
        <p class="text-muted mb-0">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </div>
    <div class="card-body">

        {{-- This form is hidden and triggered by the verification button --}}
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            {{-- Name Input --}}
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Email Input --}}
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                 @error('email')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror

                {{-- Email Verification Status --}}
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2">
                        <div class="alert alert-warning p-2" role="alert">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="btn btn-link p-0 text-decoration-underline ms-1">
                                {{ __('Click here to re-send the verification email.') }}
                            </button>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success p-2 mt-2" role="alert">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Save Button and Status --}}
            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

                @if (session('status') === 'profile-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-success m-0"
                    >{{ __('Saved.') }}</p>
                @endif
            </div>
        </form>
    </div>
</div>