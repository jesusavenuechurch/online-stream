<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access | {{ $event->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpeg') }}">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at top center, #1e293b 0%, #0f172a 100%); 
        }
        .glass-card { 
            background: rgba(30, 41, 59, 0.65); 
            backdrop-filter: blur(24px); 
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08); 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }
        .input-dark { 
            background: rgba(15, 23, 42, 0.8); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            color: white; 
            transition: all 0.2s ease;
        }
        .input-dark:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .tab-active { 
            background: #2563eb; 
            color: white; 
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .fade-in { animation: fadeIn 0.4s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-lg">
        
        <div class="flex justify-center mb-10">
            <div class="relative">
                <div class="absolute -inset-6 bg-blue-600/10 rounded-full blur-2xl"></div>
                <div class="relative">
                    <img src="{{ asset('img/logo.jpeg') }}" 
                         alt="Event Logo" 
                         class="w-auto h-24 max-w-[200px] object-contain drop-shadow-2xl"
                         style="display: block;">
                </div>
            </div>
        </div>

        <div class="glass-card rounded-[2.5rem] p-8 md:p-10">
            
            <div id="registration-success" class="hidden text-center py-6 fade-in">
                <div class="w-20 h-20 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-4">Registration Complete</h2>
                <p class="text-slate-400 mb-8">Thank you for joining. The broadcast will be available shortly.</p>
                <a href="/" class="block w-full bg-slate-800 hover:bg-slate-700 text-white py-4 rounded-xl font-bold transition text-center">Return Home</a>
            </div>

            <div id="auth-content">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-white mb-1">{{ $event->title }}</h1>
                    <p class="text-slate-400 text-sm">Welcome! Please enter your details</p>
                </div>

                <div class="flex bg-slate-900/80 p-1.5 rounded-2xl mb-8 border border-white/5">
                    <button onclick="switchTab('login')" id="tab-login" class="flex-1 py-3 rounded-xl text-sm font-bold transition-all duration-200 tab-active">Sign In</button>
                    <button onclick="switchTab('register')" id="tab-register" class="flex-1 py-3 rounded-xl text-sm font-bold transition-all duration-200 text-slate-400">Register</button>
                </div>

                <div id="form-login" class="space-y-6 fade-in">
                    <div>
                        <input type="text" id="login-username" class="input-dark w-full px-5 py-4 rounded-xl text-center text-lg" placeholder="Username/Email">
                    </div>
                    <div id="login-error" class="hidden text-red-400 text-sm text-center bg-red-400/10 py-2 rounded-lg"></div>
                    <button onclick="login()" id="login-btn" class="w-full bg-blue-600 hover:bg-blue-500 py-4 rounded-xl font-bold text-lg transition-all transform active:scale-[0.98]">Join Live Stream</button>
                    <p class="text-center text-sm text-slate-400">First time? <button onclick="switchTab('register')" class="text-blue-400 hover:text-blue-300 underline font-medium">Create an account</button></p>
                </div>

                <div id="form-register" class="hidden space-y-4 fade-in">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <select id="reg-title" class="input-dark w-full px-4 py-3 rounded-xl appearance-none cursor-pointer">
                                <option value="">Title</option>
                                <option value="Brother">Brother</option>
                                <option value="Sister">Sister</option>
                                <option value="Deacon">Deacon</option>
                                <option value="Deaconess">Deaconess</option>
                                <option value="Pastor">Pastor</option>
                            </select>
                        </div>
                        <input type="text" id="reg-username" class="input-dark w-full px-4 py-3 rounded-xl" placeholder="Username/Email">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" id="reg-first" class="input-dark w-full px-4 py-3 rounded-xl" placeholder="First Name">
                        <input type="text" id="reg-last" class="input-dark w-full px-4 py-3 rounded-xl" placeholder="Last Name">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <select id="reg-zone" onchange="updateGroups()" class="input-dark w-full px-4 py-3 rounded-xl cursor-pointer">
                            <option value="">Select Zone</option>
                            @foreach(\App\Models\Zone::all() as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        <select id="reg-group" class="input-dark w-full px-4 py-3 rounded-xl cursor-pointer">
                            <option value="">Select Group</option>
                        </select>
                    </div>
                    <div id="reg-error" class="hidden text-red-400 text-sm text-center bg-red-400/10 py-2 rounded-lg"></div>
                    <button onclick="register()" id="reg-btn" class="w-full bg-indigo-600 hover:bg-indigo-500 py-4 rounded-xl font-bold transition-all transform active:scale-[0.98]">Register Now</button>
                </div>
            </div>
        </div>
    </div>

    <div id="master-groups" class="hidden">
        @foreach(\App\Models\Group::all() as $group)
            <span data-id="{{ $group->id }}" data-zone="{{ $group->zone_id }}" data-name="{{ $group->name }}"></span>
        @endforeach
    </div>

<script>
    const eventId = "{{ $event->id }}";
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const allGroups = Array.from(document.querySelectorAll('#master-groups span')).map(span => ({
        id: span.dataset.id, zone_id: span.dataset.zone, name: span.dataset.name
    }));

    function switchTab(tab) {
        const isLogin = tab === 'login';
        document.getElementById('form-login').classList.toggle('hidden', !isLogin);
        document.getElementById('form-register').classList.toggle('hidden', isLogin);
        
        document.getElementById('tab-login').className = isLogin ? 'flex-1 py-3 rounded-xl text-sm font-bold tab-active' : 'flex-1 py-3 rounded-xl text-sm font-bold text-slate-400';
        document.getElementById('tab-register').className = !isLogin ? 'flex-1 py-3 rounded-xl text-sm font-bold tab-active' : 'flex-1 py-3 rounded-xl text-sm font-bold text-slate-400';
    }

    function updateGroups() {
        const zoneId = document.getElementById('reg-zone').value;
        const groupSelect = document.getElementById('reg-group');
        groupSelect.innerHTML = '<option value="">Select Group</option>';
        allGroups.filter(g => g.zone_id == zoneId).forEach(g => {
            const opt = document.createElement('option');
            opt.value = g.id; 
            opt.textContent = g.name; 
            groupSelect.appendChild(opt);
        });
    }

    async function login() {
        const btn = document.getElementById('login-btn');
        const username = document.getElementById('login-username').value.trim();
        
        if (!username) {
            const errEl = document.getElementById('login-error');
            errEl.textContent = "Please enter your username";
            errEl.classList.remove('hidden');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = `<span class="flex items-center justify-center"><svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Signing in...</span>`;
        
        try {
            const response = await fetch(window.location.origin + `/watch/${eventId}/authenticate`, {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ username: username })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                window.location.href = data.redirect;
            } else {
                const errEl = document.getElementById('login-error');
                errEl.textContent = data.message || "Invalid username or access denied";
                errEl.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = "Join Live Stream";
            }
        } catch (e) {
            const errEl = document.getElementById('login-error');
            errEl.textContent = "Connection error. Please try again.";
            errEl.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = "Join Live Stream";
        }
    }

    async function register() {
        const btn = document.getElementById('reg-btn');
        const payload = {
            title: document.getElementById('reg-title').value,
            username: document.getElementById('reg-username').value.trim(),
            first_name: document.getElementById('reg-first').value.trim(),
            last_name: document.getElementById('reg-last').value.trim(),
            zone_id: document.getElementById('reg-zone').value,
            group_id: document.getElementById('reg-group').value,
            type: '{{ $event->isPastorsOnly() ? "pastor" : "member" }}'
        };

        if(!payload.username || !payload.first_name || !payload.zone_id) {
            const errEl = document.getElementById('reg-error');
            errEl.textContent = "Please fill in all required fields";
            errEl.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<span class="flex items-center justify-center"><svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Registering...</span>`;

        try {
            const response = await fetch(window.location.origin + `/watch/${eventId}/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();

            if (response.ok) {
                if (data.is_live) {
                    window.location.href = data.redirect;
                } else {
                    document.getElementById('auth-content').classList.add('hidden');
                    document.getElementById('registration-success').classList.remove('hidden');
                }
            } else {
                const errEl = document.getElementById('reg-error');
                errEl.textContent = data.message || "An error occurred";
                errEl.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = "Register Now";
            }
        } catch (e) {
            btn.disabled = false;
            btn.textContent = "Register Now";
        }
    }

    // Enter key support
    window.addEventListener('DOMContentLoaded', () => {
        document.getElementById('login-username')?.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') login();
        });
        
        // Check for tab parameter
        if (new URLSearchParams(window.location.search).get('tab') === 'register') {
            switchTab('register');
        }
    });
</script>
</body>
</html>