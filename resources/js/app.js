import './bootstrap';


document.addEventListener("DOMContentLoaded", function() {
    const fromCurrency = document.getElementById("fromCurrency");
    const toCurrency = document.getElementById("toCurrency");
    const resultDiv = document.getElementById('conversion-result');
    const errorAlert = document.getElementById('error-alert');
    const amountInput = document.getElementById('amount');
    const form = document.querySelector('form');


    // Handle currency changes and form submission
    [fromCurrency, toCurrency].forEach(select => {
        select.addEventListener('change', function() {
            // Set amount to 1 if empty or invalid
            if (!amountInput.value || isNaN(amountInput.value) || amountInput.value <= 0) {
                amountInput.value = 1;
            }
        });
    });

    // Override form submission to ensure amount is 1 if invalid
    form.addEventListener('submit', function(e) {
        if (!amountInput.value || isNaN(amountInput.value) || amountInput.value <= 0) {
            amountInput.value = 1;
        }
    });

    
    if (resultDiv) {
        gsap.to('#conversion-result', {
            opacity: 0,          // Fade out to 0 opacity
            duration: 1,         // 1-second fade duration
            delay: 10,            // Wait 5 seconds before fading
            onComplete: () => {
                resultDiv.style.display = 'none'; // Hide completely after fade
            }
        });
    }

    if (errorAlert) {
        gsap.to('#error-alert', {
            opacity: 0,
            duration: 0.8,         // 1-second fade duration
            delay: 7,            // Wait 5 seconds before fading
            onComplete: () => {
                errorAlert.style.display = 'none'; // Hide completely after fade
            }
        });
    }

    function updateToCurrencyOptions() {
        const selectedFromValue = fromCurrency.value;
        const toOptions = toCurrency.options;

        for (let i = 0; i < toOptions.length; i++) {
            if (toOptions[i].value === selectedFromValue) {
                toOptions[i].disabled = true;
            } else {
                toOptions[i].disabled = false;
            }
        }
    }

    function updateFromCurrencyOptions() {
        const selectedToValue = toCurrency.value;
        const fromOptions = fromCurrency.options;

        for (let i = 0; i < fromOptions.length; i++) {
            if (fromOptions[i].value === selectedToValue) {
                fromOptions[i].disabled = true;
            } else {
                fromOptions[i].disabled = false;
            }
        }
    }

    fromCurrency.addEventListener("change", updateToCurrencyOptions);
    toCurrency.addEventListener("change", updateFromCurrencyOptions);

    // Initialize on page load
    updateToCurrencyOptions();
    updateFromCurrencyOptions();
});

document.querySelectorAll('select.form-select option').forEach(option => {
    const flagIcon = document.createElement('span');
    flagIcon.classList.add('flag-icon', `flag-icon-${option.getAttribute('data-icon')}`);
    option.prepend(flagIcon);
});

document.querySelectorAll('select').forEach(function(selectElement) {
    selectElement.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const iconClass = selectedOption.getAttribute('data-icon');
        this.style.backgroundImage = `url('https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.2.3/flags/4x3/${iconClass}.svg')`;
    });
});