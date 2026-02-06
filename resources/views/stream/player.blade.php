<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Joining Stream...</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpeg') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #f8fafc; 
        }
        .spinner { 
            border: 4px solid rgba(255,255,255,0.1); 
            border-top-color: #3b82f6; 
            border-radius: 50%; 
            width: 50px; 
            height: 50px; 
            animation: spin 1s linear infinite; 
        }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">

    <div class="text-center max-w-md">
        <div class="mb-8">
            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo" class="w-32 h-32 mx-auto rounded-2xl shadow-2xl">
        </div>
        
        <div class="spinner mx-auto mb-6"></div>
        
        <h1 class="text-3xl font-bold text-white mb-3">
            Welcome, {{ $attendee->full_name }}!
        </h1>
        
        <p class="text-slate-300 text-lg mb-2">
            Taking you to the live stream...
        </p>
        
        <p class="text-slate-500 text-sm" id="countdown">
            Redirecting in <span id="seconds">2</span> seconds
        </p>
        
        <button onclick="redirectNow()" 
                class="mt-8 bg-blue-600 hover:bg-blue-500 text-white px-8 py-3 rounded-xl font-semibold transition-all shadow-lg hover:shadow-xl">
            Go to Stream Now
        </button>
        
        <p class="text-slate-600 text-xs mt-8">
            Your attendance has been recorded
        </p>
    </div>

    <script>
        const eventId = {{ $event->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const WIX_URL = "https://www.cesouthernafrica.co.za/live-stream";
        
        let countdown = 2;
        
        // Record the attendance immediately
        fetch(`/watch/${eventId}/heartbeat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        }).then(() => {
            console.log('Attendance recorded');
        });
        
        // Countdown and redirect
        const timer = setInterval(() => {
            countdown--;
            const secondsElement = document.getElementById('seconds');
            if (secondsElement) {
                secondsElement.textContent = countdown;
            }
            
            if (countdown <= 0) {
                clearInterval(timer);
                redirectNow();
            }
        }, 1000);
        
        function redirectNow() {
            clearInterval(timer);
            window.location.href = WIX_URL;
        }
    </script>
</body>
</html>