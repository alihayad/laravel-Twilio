@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">{{ __('Verify Your Phone Number') }}</div>

        <div class="card-body">
            <p>{{ __('A verification code has been sent to your phone number.') }}</p>

            <form method="POST" action="{{ route('verification.verify') }}">
                @csrf

                <div class="form-group">
                    <label for="verification_code">{{ __('Verification Code') }}</label>
                    <input id="verification_code" type="text" class="form-control @error('verification_code') is-invalid @enderror" name="verification_code" required autocomplete="off">
                    @error('verification_code')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <button type="submit" class="mt-1 btn btn-primary">{{ __('Verify') }}</button>

              
                </form>
                    <div class="mt-3">
                        {{ __('Didn\'t receive a code?') }}  <form method="POST" id="resend-form"><button id="resend-btn" type="submit" class="btn btn-primary">{{ __('Resend code') }}</button></form>
                    </div>
                    <div id="time-remaining"> </div>
             
          
        </div>
    </div>

    <script>
     const form = document.querySelector('#resend-form');
     const button = document.getElementById('resend-btn');
     let timerInterval = null;
form.addEventListener('submit', (e) => {
    e.preventDefault(); // prevent default form submission

    // get form data
    const formData = new FormData(form);

    // send Axios POST request
    axios.post("{{route('verification.resend')}}", formData)
        .then(response => {
            data = response.data;
             // Add the 'btn-secondary' class to change the color to gray
            button.classList.add('btn-secondary');

            // Disable the button so it can't be clicked
            button.disabled = true;

           
            // handle success

             // Set the duration of the timer in seconds

             clearInterval(timerInterval);//clear if exist          
            const durationSeconds = 120-data.secondsElapsed;

            // Calculate the target time by adding the duration to the current time
            const targetTime = Date.now() + durationSeconds * 1000;

            // Update the timer display every second
            timerInterval = setInterval(() => {
            // Calculate the remaining time in seconds
            const remainingSeconds = Math.round((targetTime - Date.now()) / 1000);

            // If the remaining time is less than zero, stop the timer
            if (remainingSeconds < 0) 
            {
                // Remove the 'btn-secondary' class to change the color back to blue
                button.classList.remove('btn-secondary');

                // Enable the button so it can be clicked again
                button.disabled = false;
            clearInterval(timerInterval);
            document.getElementById('time-remaining').innerHTML = 'You can resend Verification Code Again!';
            return;
            }

            // Format the remaining time as a string (MM:SS)
            const remainingTime = `${Math.floor(remainingSeconds / 60).toString().padStart(2, '0')}:${(remainingSeconds % 60).toString().padStart(2, '0')}`;

            // Update the timer display with the remaining time
            document.getElementById('time-remaining').innerHTML = `Time remaining: ${remainingTime}`;
            }, 1000);
            })
            .catch(error => {
            console.error(error.response.data);
                // handle error
            });
});

    </script>
@endsection
