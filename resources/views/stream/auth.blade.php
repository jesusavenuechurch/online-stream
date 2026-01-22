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
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .input-dark { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); color: white; }
        .tab-active { background-color: #2563eb; color: white; }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-xl">
        <div class="glass-card rounded-[2.5rem] p-8 shadow-2xl">
            
            <div id="registration-success" class="hidden text-center py-10">
                <div class="w-20 h-20 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-3xl font-bold text-white mb-4">You're All Set!</h2>
                <p class="text-slate-400 mb-8">Thank you for registering. Your account is ready. We will see you as soon as the broadcast begins!</p>
                <a href="/" class="inline-block bg-slate-800 hover:bg-slate-700 text-white px-8 py-3 rounded-xl font-bold transition">Back to Home</a>
            </div>

            <div id="auth-content">
                <div class="flex bg-slate-900/50 p-1.5 rounded-2xl mb-8 border border-white/5">
                    <button onclick="switchTab('login')" id="tab-login" class="flex-1 py-3 rounded-xl text-sm font-bold tab-active">Sign In</button>
                    <button onclick="switchTab('register')" id="tab-register" class="flex-1 py-3 rounded-xl text-sm font-bold text-slate-400">Register</button>
                </div>

                <div id="form-login" class="space-y-6">
                    <input type="text" id="login-username" class="input-dark w-full px-5 py-4 rounded-xl text-center text-lg" placeholder="Username">
                    <div id="login-error" class="hidden text-red-400 text-sm text-center"></div>
                    <button onclick="login()" id="login-btn" class="w-full bg-blue-600 py-4 rounded-xl font-bold text-lg">Join Stream</button>
                    <p class="text-center text-sm text-slate-400">No account? <button onclick="switchTab('register')" class="text-blue-400 underline">Register here</button></p>
                </div>

                <div id="form-register" class="hidden space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <select id="reg-title" class="input-dark w-full px-4 py-3 rounded-xl">
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Pastor">Pastor</option>
                        </select>
                        <input type="text" id="reg-username" class="input-dark w-full px-4 py-3 rounded-xl" placeholder="Username">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" id="reg-first" class="input-dark w-full px-4 py-3 rounded-xl" placeholder="First Name">
                        <input type="text" id="reg-last" class="input-dark w-full px-4 py-3 rounded-xl" placeholder="Last Name">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <select id="reg-zone" onchange="updateGroups()" class="input-dark w-full px-4 py-3 rounded-xl">
                            <option value="">Select Zone</option>
                            @foreach(\App\Models\Zone::all() as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                        <select id="reg-group" class="input-dark w-full px-4 py-3 rounded-xl">
                            <option value="">Select Group</option>
                        </select>
                    </div>
                    <div id="reg-error" class="hidden text-red-400 text-sm text-center"></div>
                    <button onclick="register()" id="reg-btn" class="w-full bg-indigo-600 py-4 rounded-xl font-bold">Register Now</button>
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
                opt.value = g.id; opt.textContent = g.name; groupSelect.appendChild(opt);
            });
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

            btn.disabled = true;
            btn.textContent = "Processing...";

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
                        // NO STREAM: Show Success UI
                        document.getElementById('auth-content').classList.add('hidden');
                        document.getElementById('registration-success').classList.remove('hidden');
                    }
                } else {
                    document.getElementById('reg-error').textContent = data.message;
                    document.getElementById('reg-error').classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = "Register Now";
                }
            } catch (e) {
                btn.disabled = false;
                btn.textContent = "Register Now";
            }
        }

        // Auto-switch to register if coming from home page link
        window.addEventListener('DOMContentLoaded', () => {
            if (new URLSearchParams(window.location.search).get('tab') === 'register') switchTab('register');
        });
    </script>
</body>
</html>