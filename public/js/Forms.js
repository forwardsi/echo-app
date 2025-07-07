//Showing as many NFC Tag sections as clients say they need
function generateNfcFields(num) {
    // Clear existing dynamic NFC fields
    document.getElementById('dynamicNfcFields').innerHTML = '';

    // Generate new NFC fields based on the user's input
    for (let i = 1; i <= num; i++) {
        // Create NFC tag label
        const nfcLabel = document.createElement('p');
        nfcLabel.textContent = 'NFC Tag ' + i;
        document.getElementById('dynamicNfcFields').appendChild(nfcLabel);

        // Create Location input
        const locationInput = document.createElement('input');
        locationInput.type = 'text';
        locationInput.id = 'location' + i;
        locationInput.name = 'location' + i;
        locationInput.placeholder = 'Enter Location for NFC Tag ' + i;
        locationInput.required = true;

        // Create Action dropdown
        const actionSelect = document.createElement('select');
        actionSelect.id = 'action' + i;
        actionSelect.name = 'action' + i;
        actionSelect.innerHTML = `
            <option value="demo_action1">get review</option>
            <!-- Add more options as needed -->
        `;

        // Create Reward dropdown
        const rewardSelect = document.createElement('select');
        rewardSelect.id = 'reward' + i;
        rewardSelect.name = 'reward' + i;
        rewardSelect.innerHTML = `
            <option value="demo_reward1">Free coffie</option>
            <!-- Add more options as needed -->
        `;

        // Append the new NFC fields to the container
        document.getElementById('dynamicNfcFields').appendChild(locationInput);
        document.getElementById('dynamicNfcFields').appendChild(actionSelect);
        document.getElementById('dynamicNfcFields').appendChild(rewardSelect);
    }
}

