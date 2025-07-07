@extends('layouts.layout')

@section('content')
    <div class="main-container">
        <img src=".\uploads\Forward-only\Forward_Mail_logo_2024 1.png" class="f-logo">
        <div class="header">
            <h1>Welcome to Forward NFC project!</h1>
            <p>Please take a minute and carefully read the <b>instructions.</b></p>
        </div>

        <form action="{{ url('client') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <h2>Fill-in the information below.</h2>
            <div class="input-div">
                <label for="hotel_name">Hotel name:</label>
                <input type="text" id="hotel_name" name="hotel_name" required placeholder="Forwad">
            </div>

            <div class="input-div">
                <label for="colors">Hotel color:</label>
                <input type="text" id="colors" name="colors" required placeholder="#332046">
            </div>

            <div class="input-div">
                <label for="logo_img">Hotel Logo:</label>
                <input type="file" id="logo_img" name="logo_img" accept="image/*" required>
            </div>

            <div class="input-div">
                <label for="banner_img">Hotel Banner image:</label>
                <input type="file" id="banner_img" name="banner_img" accept="image/*" required>
            </div>



            <h2>Choose a ranking feature, number of NCF tags and your Google Business link.</h2>

            <div class="input-div select-style">
                <label for="ranking_features">Choose a ranking feature:</label>
                <select id="ranking_features" name="ranking_features" placeholder="Select an item">
                    <option value="Family">Family friendly</option>
                    <option value="Pet">Pet friendly</option>
                    <option value="Peaceful">Peaceful</option>
                </select>
            </div>

            <div class="input-div">
                <label for="gmb_link">Google Business Reviews link:</label>
                <input type="text" id="gmb_link" name="gmb_link" required placeholder="Paste URL here">
            </div>

            <div class="input-div">
                <label for="nfcTags">Number of NFC Tags:</label>
                <div class="custom-input">
                    <input type="number" id="nfc_tags" name="nfc_tags" min="1" value="0" required oninput="generateNfcFields(this.value)" style="width:75%;">
                    <div class="input-btns" style="width:25%; display: flex; justify-content: flex-end;">
                        <button type="button" class="quantity-button" onclick="decrementNfc()" style="padding:5px 14px; background: linear-gradient(33.81deg, #BABABA 0.02%, #D8D8D8 100.03%);
 "> <p style="padding-bottom:3px; font-size:23px" > - </p> </button>
                        <button type="button" class="quantity-button" onclick="incrementNfc()"><p style="padding:4px 0px 0px 0px; font-size:20px; height:20px"> + </p></button>
                    </div>
                </div>
                
                <!-- Container to hold dynamically generated NFC fields -->
                <div id="dynamicNfcFields"></div>
            </div>
    
            

            <button type="submit" value="Submit"> Submit </button>
        </form>
        <script>
        @if(isset($message))
            alert("{{ $message }}");
        @endif

        function incrementNfc() {
            var nfcTagsInput = document.getElementById('nfc_tags');
            var currentValue = parseInt(nfcTagsInput.value);
            nfcTagsInput.value = currentValue + 1;
            generateNfcFields(currentValue + 1); // Optionally, update dynamic fields
        }

        function decrementNfc() {
            var nfcTagsInput = document.getElementById('nfc_tags');
            var currentValue = parseInt(nfcTagsInput.value);
            if (currentValue > 1) {
                nfcTagsInput.value = currentValue - 1;
                generateNfcFields(currentValue - 1); // Optionally, update dynamic fields
            }
        }

        
    </script>
    </div>



    <br><br>

@endsection
