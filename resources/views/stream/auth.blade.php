<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Access Stream | {{ $event->title }}</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; overflow-x: hidden; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .input-dark { 
            background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); color: white; transition: all 0.2s; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='rgba(148, 163, 184, 0.5)'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.25rem;
        }
        .input-dark:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); outline: none; }
        .input-dark option { background-color: #1e293b; color: white; }
        .bg-glow { position: fixed; border-radius: 50%; filter: blur(100px); z-index: -1; }
        .tab-active { background-color: #2563eb; color: white; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3); }
        .tab-inactive { color: #94a3b8; }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="bg-glow w-[500px] h-[500px] bg-blue-600/10 -top-48 -left-48"></div>

    <div class="w-full max-w-xl">
        <div class="glass-card rounded-[2.5rem] p-8 md:p-10 shadow-2xl">
            
            <div class="flex bg-slate-900/50 p-1.5 rounded-2xl mb-8 border border-white/5">
                <button onclick="switchTab('login')" id="tab-login" class="flex-1 py-3 rounded-xl text-sm font-bold transition-all tab-active">Sign In</button>
                <button onclick="switchTab('register')" id="tab-register" class="flex-1 py-3 rounded-xl text-sm font-bold transition-all tab-inactive">Register</button>
            </div>

            <div id="form-login" class="space-y-6">
                <div>
                    <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">Username</label>
                    <input type="text" id="login-username" class="input-dark w-full px-5 py-4 rounded-xl text-lg text-center font-semibold" placeholder="Enter username">
                    <div id="login-error" class="hidden mt-3 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center"></div>
                </div>

                <div class="space-y-4">
                    <button id="login-btn" onclick="login()" class="w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-xl font-bold text-lg transition-all">Join Stream</button>
                    <p class="text-center text-sm text-slate-400">No account yet? <button onclick="switchTab('register')" class="text-blue-400 underline">Register here</button></p>
                </div>
            </div>

            <div id="form-register" class="hidden space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">Title</label>
                        <select id="reg-title" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                            <option value="Brother">Brother</option>
                            <option value="Sister">Sister</option>
                            <option value="Pastor">Pastor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">Username</label>
                        <input type="text" id="reg-username" class="input-dark w-full px-4 py-3 rounded-xl text-sm" placeholder="Unique ID">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">First Name</label>
                        <input type="text" id="reg-first" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">Last Name</label>
                        <input type="text" id="reg-last" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">Zone</label>
                        <select id="reg-zone" onchange="updateGroups()" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                            <option value="">Select Zone</option>
                            @foreach(\App\Models\Zone::all() as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-slate-500 mb-2">Group</label>
                        <select id="reg-group" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                            <option value="">Select Zone First</option>
                        </select>
                    </div>
                </div>

                <div id="reg-error" class="hidden mt-3 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center"></div>
                <button onclick="register()" id="reg-btn" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-4 rounded-xl font-bold transition-all">Register & Join</button>
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
            id: span.dataset.id,
            zone_id: span.dataset.zone,
            name: span.dataset.name
        }));

        function switchTab(tab) {
            const isLogin = tab === 'login';
            document.getElementById('form-login').classList.toggle('hidden', !isLogin);
            document.getElementById('form-register').classList.toggle('hidden', isLogin);
            document.getElementById('tab-login').className = isLogin ? 'flex-1 py-3 rounded-xl text-sm font-bold tab-active' : 'flex-1 py-3 rounded-xl text-sm font-bold tab-inactive';
            document.getElementById('tab-register').className = !isLogin ? 'flex-1 py-3 rounded-xl text-sm font-bold tab-active' : 'flex-1 py-3 rounded-xl text-sm font-bold tab-inactive';
        }

        function updateGroups() {
            const zoneId = document.getElementById('reg-zone').value;
            const groupSelect = document.getElementById('reg-group');
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            if (!zoneId) return;
            allGroups.filter(g => g.zone_id == zoneId).forEach(group => {
                const opt = document.createElement('option');
                opt.value = group.id; opt.textContent = group.name; groupSelect.appendChild(opt);
            });
        }

        function showError(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        async function login() {
            const username = document.getElementById('login-username').value.trim();
            const btn = document.getElementById('login-btn');
            const errorBox = document.getElementById('login-error');
            
            errorBox.classList.add('hidden');
            if (!username) return showError('login-error', 'Username is required');

            btn.disabled = true;
            btn.textContent = "Connecting...";

            try {
                // Using URL object to ensure absolute path
                const url = window.location.origin + `/watch/${eventId}/authenticate`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ username })
                });

                const data = await response.json();

                if (response.ok) {
                    window.location.href = data.redirect;
                } else {
                    showError('login-error', data.message || 'User not found. Check spelling or register.');
                    btn.disabled = false;
                    btn.textContent = "Join Stream";
                }
            } catch (e) { 
                console.error(e);
                showError('login-error', 'Network error. Please check your internet.');
                btn.disabled = false;
                btn.textContent = "Join Stream";
            }
        }

        async function register() {
            const btn = document.getElementById('reg-btn');
            const errorBox = document.getElementById('reg-error');
            errorBox.classList.add('hidden');

            const payload = {
                title: document.getElementById('reg-title').value,
                username: document.getElementById('reg-username').value.trim(),
                first_name: document.getElementById('reg-first').value.trim(),
                last_name: document.getElementById('reg-last').value.trim(),
                zone_id: document.getElementById('reg-zone').value,
                group_id: document.getElementById('reg-group').value,
                type: '{{ $event->isPastorsOnly() ? "pastor" : "member" }}'
            };

            if(!payload.username || !payload.first_name || !payload.last_name || !payload.zone_id || !payload.group_id) {
                return showError('reg-error', 'Please fill all fields.');
            }

            btn.disabled = true;
            btn.textContent = "Processing...";

            try {
                const url = window.location.origin + `/watch/${eventId}/register`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok) window.location.href = data.redirect;
                else {
                    showError('reg-error', data.message || "Username might be taken.");
                    btn.disabled = false;
                    btn.textContent = "Register & Join";
                }
            } catch (e) { 
                showError('reg-error', 'Network error.'); 
                btn.disabled = false;
                btn.textContent = "Register & Join";
            }
        }
    </script>
</body>
</html>