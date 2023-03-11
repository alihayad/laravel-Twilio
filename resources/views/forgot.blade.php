@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">{{ __('Forgot Password') }}</div>

        <div class="card-body">
            <p>{{ __('Enter Your Phone number') }}</p>

            <form method="POST" action="{{ route('password.forgot.check-phone') }}">
                @csrf

                <div class="form-group">
                    <label for="verification_code">{{ __('PhoneNumber') }}</label>
                    <input id="verification_code" type="text" class="form-control @error('user_doesnt_exists') is-invalid @enderror" name="phone_number" required autocomplete="off">
                    @error('user_doesnt_exists')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="mt-1 btn btn-primary">{{ __('Next') }}</button>

              
                </form>
                    
             
          
        </div>
    </div>


@endsection
