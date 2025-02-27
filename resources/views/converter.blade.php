<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>kudiBase®</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/7.2.3/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" integrity="sha384-XjKyoo2wGj6H9m7Pe1GqYhjpY7i1n1t1o1t2" crossorigin="anonymous">
    

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.1/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.1/TextPlugin.min.js"></script>
    {{-- <link rel="stylesheet" href="{{ asset('css/style.css') }}"> --}}
    @vite('resources/css/style.css')
    @vite('resources/js/app.js')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('select.form-select').select2({
                templateResult: formatCurrency,
                templateSelection: formatCurrency
            });

            function formatCurrency(currency) {
                if (!currency.id) return currency.text;
                const flag = $(currency.element).data('icon');
                return $(`<span><span class="fi fi-${flag}"></span> ${currency.text}</span>`);
            }
        });
    </script>
    

</head>
<body class="container mt-3">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">kudiBase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://www.exchangerate-api.com/docs/overview" target="_blank">Exchange Rates API</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Rates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/AdeniranZion"><i class="fa-brands fa-github"></i></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="text-center hero-section">
        <h1 class="hero-heading">
            Convert <span class="from-currency highlight">$USD</span> to <span class="to-currency highlight">₦NGN</span>
        </h1>
    </div>

    <!-- GSAP Animation -->
    <script>
        // Register the TextPlugin
        gsap.registerPlugin(TextPlugin);

        // Arrays of currencies to rotate through
        const fromCurrencies = ["$USD", "€EUR", "£GBP", "¥JPY"];
        const toCurrencies = ["₦NGN", "$USD", "€EUR", "£GBP"];
        let fromIndex = 0;let toIndex = 0;


        // Function to clear the existing text
        function clearText(element, onComplete) {
            gsap.to(element, { duration: 0.5, text: "", onComplete });
        }

        // Function to type the new text
        function typeText(element, newText, onComplete) {
            gsap.to(element, { duration: 0.5, text: newText, ease: "none", onComplete });
        }

        // Function to handle the animation sequence
        function updateText() {
            clearText(".from-currency", () => {
                typeText(".from-currency", fromCurrencies[fromIndex], () => {
                    setTimeout(() => {
                        clearText(".to-currency", () => {
                            typeText(".to-currency", toCurrencies[toIndex], () => {
                                setTimeout(updateText, 2000);
                            });
                        });
                    }, 2000);
                });
            });

            // Update indices for the next round
            fromIndex = (fromIndex + 1) % fromCurrencies.length;
            toIndex = (toIndex + 1) % toCurrencies.length;
        }

        // Start the animation after a brief delay
        setTimeout(updateText, 2000);
    </script>

    {{-- Coverter Form --}}
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card p-4">
                <h3 class="card-title text-center">Currency Converter</h3>
                <form action="{{ route('convert') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="fromCurrency" class="form-label">From:</label>
                        <select name="from" id="fromCurrency" class="form-select" required>
                            <option value="USD" data-icon="us" {{ old('from') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="GBP" data-icon="gb" {{ old('from') === 'GBP' ? 'selected' : '' }}>GBP - British Pounds</option>
                            <option value="EUR" data-icon="eu" {{ old('from') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="CAD" data-icon="ca" {{ old('from') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="JPY" data-icon="jp" {{ old('from') === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                            <option value="NGN" data-icon="ng" {{ old('from') === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                            <option value="GHS" data-icon="gh" {{ old('from') === 'GHS' ? 'selected' : '' }}>GHS - Ghanaian Cedi</option>
                            <option value="ZAR" data-icon="za" {{ old('from') === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                        </select>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        {{-- <button type="button" id="switchCurrencies" class="btn btn-outline-light switch-btn">Switch ↔️</button> --}}
                    </div>
                    <div class="mb-3">
                        <label for="toCurrency" class="form-label">To:</label>
                        <select name="to" id="toCurrency" class="form-select" required>
                            <option value="NGN" data-icon="ng" {{ old('to') === 'NGN' ? 'selected' : '' }}>NGN - Nigerian Naira</option>
                            <option value="USD" data-icon="us" {{ old('to') === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                            <option value="EUR" data-icon="eu" {{ old('to') === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="GBP" data-icon="gb" {{ old('to') === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                            <option value="CAD" data-icon="ca" {{ old('to') === 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                            <option value="JPY" data-icon="jp" {{ old('to') === 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                            <option value="GHS" data-icon="gh" {{ old('to') === 'GHS' ? 'selected' : '' }}>GHS - Ghanaian Cedi</option>
                            <option value="ZAR" data-icon="za" {{ old('to') === 'ZAR' ? 'selected' : '' }}>ZAR - South African Rand</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" name="amount" id="amount" class="form-control" placeholder="1.00" value="{{ old('amount', 1) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="converted_amount" class="form-label">Converted Amount</label>
                        <input type="number" name="converted_amount" id="converted_amount" class="form-control converted-output" value="{{ $convertedAmount ?? '' }}" style="background-color: #525252;" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary convert-btn">Convert</button>
                </form>
                @if ($errors->any())
                    <div class="alert alert-danger mt-2" id="error-alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @if (isset($convertedAmount))
                <div class="card p-4 mt-4" id="conversion-result">
                    <h5 class="card-title">Conversion Result</h5>
                    <p>
                        <span class="fi fi-{{ strtolower($from) }}"></span> {{ number_format($amount, 2) }} {{ $from }} = 
                        <span class="fi fi-{{ strtolower($to) }}"></span> {{ number_format($convertedAmount, 2) }} {{ $to }}
                    </p>
                </div>
            @endif

            <p class="detail">
                Kudibase is a user-friendly currency converter app designed to simplify the process of converting currencies on the go. It provides real-time exchange rates, ensuring users have the most up-to-date information for accurate conversions. The app supports a wide range of currencies, making it a versatile tool for travelers, business professionals, and anyone dealing with international transactions.
            </p>
           <div class="flag-disp">
                <span class="fi fi-ng"></span>
                <span class="fi fi-us"></span>
                <span class="fi fi-gb"></span>
                <span class="fi fi-eu"></span>
                <span class="fi fi-ca"></span>
                <span class="fi fi-jp"></span>
                <span class="fi fi-gh"></span>
                <span class="fi fi-za"></span>
           </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="social-icons">
                
                <p class="zee">
                    @php echo date('Y'); @endphp
                    &copy; zionAdeniran</p>
                <div>
                    <a href="https://x.com/zionaadeniran"><i class="fab fa-x-twitter"></i></a>
                    <a href="https://github.com/AdeniranZion"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
