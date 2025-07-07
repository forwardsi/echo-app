@extends('layouts.layout')

@section('content')

    <div class="main-container" style = "background-color:white;">
    @if(!isset($apiAnswer))
        <form action="{{ url('generateReview') }}" method="POST">
            @csrf
            <p><b>Please answer the following questions.</b></p>
            
            @foreach ($jsonData['ranking_features'] as $feature)
                    @foreach ($feature['questions'] as $question)

                    <div class="input-div">
                        <label>{{ $question['text'] }}</label>
                        <div class="promo-checkbox space-between">
                        
                            @foreach ($question['options'] as $option)
                            <div class="checkbox-together">
                                <input type="hidden" name="{{ 'question_label' . $question['id'] }}" value="{{ $question['text'] }}">
                                <input type="{{ $question['type'] }}" name="{{ 'question' . $question['id'] }}" value="{{ $option }}" required>
                                <p>{{ $option }}</p>
                            </div>
                                
                            @endforeach
                        </div>
                    </div>
                    @endforeach
            @endforeach
            

            <button type="submit" value="Generate review">Generate review</button>
        </form>
        @endif
        @if(isset($apiAnswer))
        <!-- Display the API answer -->

        
        <div class="steps-div" id="steps-div" >
            <div class="input-div">
                <p><strong>Your review:</strong><br></p>
                <div class="generated-review">
                    <p> {{ $apiAnswer }} </p> 
                </div>
                <div style="margin-top:10px;" >
                    <p style=" font-size:12px;">We've created a review based on your answers — <strong> feel free to tweak it to better reflect your experience.</strong> Just be sure to keep the hashtag at the end so we can find your review!</p>
                </div>
                
            </div>


            <div class="input-div">
                <h2 style="width:100%;">Step 1/3</h2>
                <p class="m-b-15">Copy the generated review</p>
                <button onclick="copyApiAnswer()">Copy the review</button>
            </div>


            <div class="input-div" id="redirectButton" style="display: none; flex-direction: column;">
                <h2 style="width:100%; margin-bottom:10px;">Step 2/3 - Please read carefully</h2>
                <p style="margin-bottom:5px;">1. Do not delete the generated hashtag—it helps us find your review!</p>
                <p class="m-b-15">2. Manually return to your Echo by Forward screen once you post the review.</p>

                <!-- Countdown -->
                <p id="countdownMessage">You can paste your review in <span id="countdownTimer">5</span> seconds...</p>

                <!-- Hidden button initially -->
                <button onclick="redirect()" id="goPasteBtn" style="display: none;">Go paste the review</button>
            </div>


            <div class="input-div" id="claimRewardButton" style="display: none;">
                <h2 style="width:100%;">Step 3/3</h2>
                <p class="m-b-15"> Congrats! Go and claim your reward!</p>
                <button onclick="claimReward()" id="start_animation">Claim my reward</button>
            </div>

        </div>
    
        <div class="loader-div" id="loadingDiv" style="display:none">
            <div class="inner-loader-div">
                <!-- Add the GIF -->
                <img src="{{ asset('uploads/Forward-only/coffee-shop-animation.gif') }}" alt="Coffee Animation" style="width: 200px; height: auto; margin: 0 auto;">
                <h1>Your reward is being prepared</h1>
                <p>We are checking your review. Please wait...</p>
                <h2 id="countdown">60s</h2>
            </div>
        </div>

    
    <div >

    <script>
        function copyApiAnswer() {
            var apiAnswer = `{!! $apiAnswer !!}`;

            navigator.clipboard.writeText(apiAnswer).then(function () {
                alert('API answer copied to clipboard!');
                
                // Show Step 2 section
                const redirectDiv = document.getElementById('redirectButton');
                redirectDiv.style.display = 'flex';

                // Start countdown
                let countdown = 5;
                const countdownElement = document.getElementById('countdownTimer');
                const goPasteBtn = document.getElementById('goPasteBtn');

                const countdownInterval = setInterval(() => {
                    countdown--;
                    countdownElement.textContent = countdown;

                    if (countdown <= 0) {
                        clearInterval(countdownInterval);
                        goPasteBtn.style.display = 'inline-block'; // Show the button
                        document.getElementById('countdownMessage').style.display = 'none'; // Hide the message
                    }
                }, 1000);

            }, function () {
                alert('Failed to copy API answer. Please try again.');
            });
        }

        function redirect() {
            // Redirect the user to the appropriate page
            window.open("{{$gmbLink}}", "_blank");
            // Show the claim reward button after the redirect
            document.getElementById('claimRewardButton').style.display = 'flex';
        }

        

    </script>

<script>
    function claimReward() {
        // Display the loading animation
        document.getElementById('loadingDiv').style.display = 'block';
        document.getElementById('steps-div').style.display = 'none';

        // Start the countdown for display purposes
        let countdownTime = 90;
        const countdownElement = document.getElementById('countdown');

        const interval = setInterval(() => {
            countdownTime -= 1;
            countdownElement.textContent = `${countdownTime}s`;

            if (countdownTime <= 0) {
                clearInterval(interval); // Stop the countdown when it reaches 0
            }
        }, 1000); // Update every second

        // Redirect to the check review route immediately
        window.location.href = "{{ url('/checkReview') }}";
    }
</script>
@endif

    </div>
@endsection