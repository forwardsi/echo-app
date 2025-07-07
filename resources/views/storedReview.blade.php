@extends('layouts.layout')

@section('content')

<div class="main-container">
    <div class="review-container">
        
        
        <!-- Check if the data exists -->
        @if(isset($hashtag) && isset($reviewText))
        <div id="steps-div">
            <h1>You have already generated a review</h1>
            <div class="review-details">
                <p style="margin-top:10px!important;"><strong>Review Code:</strong>{{ $hashtag }}</p>
                <textarea id="review-text" readonly>{{ $reviewText }}</textarea>
                <button class="copy-button" onclick="copyReview()">Copy Review</button>
                <p id="copy-feedback" style="color: green; display: none;">Review copied to clipboard!</p>
            </div>

            <!-- Option 1: Already Posted Review -->
            <div class="option">
                <p><strong>Have you already posted the review?</strong></p>
                <button class="action-button" onclick="claimReward()">Click here to claim your reward</button>
            </div>

            <!-- Option 2: Review Generated but Not Posted -->
            <div class="option">
                <p><strong>Did you generate, but not post the review?</strong></p>
                <button class="action-button" onclick="postReview()">Post the review</button>
            </div>

            <!-- Option 3: Not Satisfied with the Review -->
            <div class="option" id="newReview">
                <p><strong>Not satisfied with the review?</strong></p>
                <button class="action-button" onclick="generateNewReview()">Generate a new review</button>
            </div>
        @else
            <p>No review found. Please generate a review first.</p>
        @endif
        </div>
    </div>

     <!-- Loader Animation -->
     <div class="loader-div" id="loadingDiv" style="display:none">
        <div class="inner-loader-div">
            <img src="{{ asset('uploads/Forward-only/coffee-shop-animation.gif') }}" alt="Coffee Animation" style="width: 200px; height: auto; margin: 0 auto;">
            <h1>Your coffee is being prepared</h1>
            <p>We are checking your review. Please wait...</p>
            <h2 id="countdown">60s</h2>
        </div>
    </div>
</div>

<script>
    // Function to copy the review text to the clipboard
    function copyReview() {
        var reviewText = document.getElementById('review-text');
        reviewText.select();
        document.execCommand('copy');
        
        // Show confirmation message
        document.getElementById('copy-feedback').style.display = 'inline';

        // Optionally, hide the confirmation message after a few seconds
        setTimeout(function() {
            document.getElementById('copy-feedback').style.display = 'none';
        }, 2000);
    }

    // Placeholder functions for actions - replace with actual logic

    // Function to claim reward (you can redirect or show a message)
    

    // Function to post the review (you can add logic for posting the review)
    function postReview() {
            // Redirect the user to the appropriate page
            window.open("{{$gmbLink}}", "_blank");
            document.getElementById('newReview').style.display = 'none';
        }

    // Function to generate a new review (you can redirect to a review generation page)
    function generateNewReview() {
        window.location.href = "{{ url('/questionViewAgain') }}"; // Replace with actual generate review route
    }
</script>

<script>
    function claimReward() {
        // Display the loading animation
        document.getElementById('loadingDiv').style.display = 'block';
        document.getElementById('steps-div').style.display = 'none';
        document.querySelector('.main-container').style.backgroundColor = 'white';
        // Start the countdown for display purposes
        let countdownTime = 60; // Countdown time in seconds
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