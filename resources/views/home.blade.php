@extends('layouts.layout')

@section('content')
    <div class="main-container" style="margin-bottom:150px">
        <img class="banner-img" src="{{ asset($bannerImgPath) }}">
        <div class="header">
            <h1> Hello! You’re just a few clicks away from your free {{ $reward }}. </h1>
        </div>
            
        <form action="{{ url('store') }}" method="POST">
            @csrf
            <h2>Please fill-in the information below.</h2>
            <div class="input-div">
                <label for="full_name">Full Name(required)</label>
                <input type="text" id="full_name" name="full_name" required>
            </div>

            <div class="input-div">
                <label for="email">Email(required)</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <h2>Please tick in the boxes below:</h2>


            <div class="input-div">
                <div class="promo-checkbox">
                    <input type="checkbox" id="checkbox2" name="checkbox2" required>
                    <label for="checkbox2" class="short">I agree with <a href="https://www.charming-bled.com/en/privacy-policy/">Terms and Conditions.</a></label>
                </div>
            </div>

            <div class="input-div">
                <div class="promo-checkbox">
                    <input type="checkbox" id="checkbox1" name="checkbox1" required>
                    <label for="checkbox1" class="short">I consent to receiving marketing communications and promotional offers from Charming Bled</label>
                </div>
            </div>
            
            
            <div style="display:none">
                <h2 style="width:100%;"> <b>How it works</b></h2>
                <p>1. Fill out the form at the top.</p>
                <p>2. Click Continue and choose the answers that match your stay.</p>
                <p>3. Wait for your review to generate.</p>
                <p>4. Copy & paste the review on our Google Business profile. <b>Important: Do not delete the generated hashtag—it helps us find your review!</b></p>
                <p>5. <b>Manually return</b> to your Echo by Forward screen.</p>
                <p>6. Check review: Wait 20-40 seconds to confirm it’s posted.</p>
                <p>7. Get QR code: You’ll receive it via email. Show it to the receptionist <b>(don’t scan it yourself)</b>.</p>
                <p>8. Claim your reward and enjoy! 😊</p>
            </div>

            <button type="submit" value="Write a review">Write a review</button>
        </form>
    </div>

    <script>

        function incrementDays() {
            var nfcTagsInput = document.getElementById('length_of_stay');
            var currentValue = parseInt(nfcTagsInput.value);
            nfcTagsInput.value = currentValue + 1;
        }

        function decrementDays() {
            var nfcTagsInput = document.getElementById('length_of_stay');
            var currentValue = parseInt(nfcTagsInput.value);
            if (currentValue > 1) {
                nfcTagsInput.value = currentValue - 1;
            }
        }
    </script>

@endsection
