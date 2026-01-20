<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Join Stream | Church Online</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; overflow-x: hidden; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .input-dark { background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); color: white; transition: all 0.2s; }
        .input-dark:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2); outline: none; }
        .bg-glow { position: fixed; border-radius: 50%; filter: blur(100px); z-index: -1; }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex items-center justify-center p-4">

    <div class="bg-glow w-[500px] h-[500px] bg-blue-600/20 -top-48 -left-48"></div>
    <div class="bg-glow w-[400px] h-[400px] bg-purple-600/20 -bottom-24 -right-24"></div>

    <div class="w-full max-w-xl">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-blue-500/20">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">{{ $event->title }}</h1>
            <p class="text-slate-400 mt-1">Experience the service live</p>
        </div>

        <div class="glass-card rounded-[2rem] p-8 md:p-10 shadow-2xl">
            
            <div id="step-check" class="space-y-6">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-semibold text-white">Enter your username</h2>
                    <p class="text-sm text-slate-400 mt-1">Dont have one? Enter the desired to create an account</p>
                </div>

                <div>
                    <input type="text" id="check-username" 
                           class="input-dark w-full px-5 py-4 rounded-xl text-lg text-center" 
                           placeholder="username or email"
                           autocomplete="off">
                    <div id="check-error" class="hidden mt-3 text-red-400 text-sm text-center font-medium italic"></div>
                </div>

                <button onclick="checkUsername()" 
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-xl font-bold text-lg transition-all transform hover:scale-[1.01] active:scale-[0.98]">
                    Continue
                </button>
            </div>

            <div id="step-login" class="hidden space-y-8 text-center py-4">
                <div class="space-y-2">
                    <span class="px-3 py-1 bg-blue-500/10 text-blue-400 text-[11px] font-bold uppercase tracking-widest rounded-full border border-blue-500/20">
                        Welcome Back
                    </span>
                    <h2 class="text-3xl font-bold text-white" id="login-name"></h2>
                </div>

                <div class="flex flex-col gap-3">
                    <button onclick="login()" 
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20">
                        Join Stream
                    </button>
                    <button onclick="backToCheck()" class="text-slate-500 text-sm hover:text-white transition underline underline-offset-4">
                        Switch Account
                    </button>
                </div>
            </div>

            <div id="step-register" class="hidden space-y-6">
                <div class="flex items-center justify-between border-b border-white/10 pb-4 mb-2">
                    <h2 class="text-xl font-bold text-white">New Registration</h2>
                    <button onclick="backToCheck()" class="text-slate-400 text-xs hover:text-white transition">← Back</button>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">Title</label>
                        <select id="reg-title" class="input-dark w-full px-4 py-3 rounded-xl text-sm appearance-none">
                            <option value="Mr">Brother</option>
                            <option value="Mrs">Sister</option>
                            <option value="Ms">Deacon</option>
                            <option value="Pastor">Deaconess</option>
                            <option value="Deacon">Pastor</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">Username</label>
                        <input type="text" id="reg-username" readonly class="input-dark w-full px-4 py-3 rounded-xl text-sm opacity-50 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">First Name</label>
                        <input type="text" id="reg-first" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">Last Name</label>
                        <input type="text" id="reg-last" class="input-dark w-full px-4 py-3 rounded-xl text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">Zone</label>
                        <select id="reg-zone" class="input-dark w-full px-4 py-3 rounded-xl text-sm appearance-none">
                            <option value="">Select Zone</option>
                            @foreach(\App\Models\Zone::all() as $zone)
                                <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-widest font-bold text-slate-500 mb-2 ml-1">Group</label>
                        <select id="reg-group" class="input-dark w-full px-4 py-3 rounded-xl text-sm appearance-none">
                            <option value="">Select Group</option>
                            @foreach(\App\Models\Group::all() as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="reg-error" class="hidden text-red-400 text-xs italic text-center"></div>

                <button onclick="register()" 
                        class="w-full bg-indigo-600 hover:bg-indigo-500 text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-indigo-500/20 mt-2">
                    Register & Join
                </button>
            </div>
        </div>
    </div>

    <script>
        const eventId = {{ $event->id }};
        let currentUsername = '';
        let allGroups = []; // Store all groups

        // Fetch all groups on page load
        async function loadGroups() {
            try {
                const response = await fetch('/api/groups');
                allGroups = await response.json();
            } catch (e) {
                console.error('Failed to load groups:', e);
            }
        }

        // Filter groups based on selected zone
        function updateGroupDropdown() {
            const zoneId = document.getElementById('reg-zone').value;
            const groupSelect = document.getElementById('reg-group');
            
            // Clear current options
            groupSelect.innerHTML = '<option value="">Select Group</option>';
            
            if (!zoneId) {
                groupSelect.disabled = true;
                return;
            }
            
            // Filter and add groups for selected zone
            const filteredGroups = allGroups.filter(group => group.zone_id == zoneId);
            
            filteredGroups.forEach(group => {
                const option = document.createElement('option');
                option.value = group.id;
                option.textContent = group.name;
                groupSelect.appendChild(option);
            });
            
            groupSelect.disabled = false;
        }

        // Add event listener for zone change
        document.addEventListener('DOMContentLoaded', function() {
            loadGroups();
            document.getElementById('reg-zone').addEventListener('change', updateGroupDropdown);
        });

        function showStep(step) {
            document.getElementById('step-check').classList.add('hidden');
            document.getElementById('step-login').classList.add('hidden');
            document.getElementById('step-register').classList.add('hidden');
            document.getElementById('step-' + step).classList.remove('hidden');
        }

        function backToCheck() {
            showStep('check');
            document.getElementById('check-username').value = currentUsername;
        }

        function showError(id, msg) {
            const el = document.getElementById(id);
            el.textContent = msg;
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 4000);
        }

        async function checkUsername() {
            const username = document.getElementById('check-username').value.trim();
            if (!username) {
                showError('check-error', 'Please enter a username or email');
                return;
            }

            currentUsername = username;

            try {
                const response = await fetch('/check-username', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ username })
                });

                const data = await response.json();

                if (data.exists) {
                    const fullName = data.attendee.first_name + ' ' + data.attendee.last_name;
                    document.getElementById('login-name').textContent = fullName;
                    showStep('login');
                } else {
                    document.getElementById('reg-username').value = username;
                    showStep('register');
                }
            } catch (e) {
                showError('check-error', 'Connection lost. Try again.');
            }
        }

        async function login() {
            try {
                const response = await fetch(`/watch/${eventId}/authenticate`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ username: currentUsername })
                });
                const data = await response.json();
                if (response.ok) window.location.href = data.redirect;
                else showError('login-error', data.message || 'Access denied');
            } catch (e) { 
                showError('login-error', 'Login error.'); 
            }
        }

        async function register() {
            const payload = {
                title: document.getElementById('reg-title').value,
                first_name: document.getElementById('reg-first').value.trim(),
                last_name: document.getElementById('reg-last').value.trim(),
                username: currentUsername,
                zone_id: document.getElementById('reg-zone').value || null,
                group_id: document.getElementById('reg-group').value || null,
                type: '{{ $event->isPastorsOnly() ? "pastor" : "member" }}'
            };

            if (!payload.first_name || !payload.last_name) {
                showError('reg-error', 'First and Last name are required');
                return;
            }

            try {
                const response = await fetch(`/watch/${eventId}/register`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await response.json();
                if (response.ok) window.location.href = data.redirect;
                else {
                    const errors = data.errors || {};
                    const errorMessage = Object.values(errors).flat().join(', ') || data.message;
                    showError('reg-error', errorMessage);
                }
            } catch (e) { 
                showError('reg-error', 'Registration failed.'); 
            }
        }

        document.getElementById('check-username').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') checkUsername();
        });
    </script>
</body>
</html>