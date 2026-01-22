<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} - Live Experience</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass-nav { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-card { background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .vjs-custom-skin .video-js { border-radius: 1rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
        .live-indicator { animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); } 70% { box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); } }
    </style>
</head>
<body class="min-h-screen pb-12">

    <header class="glass-nav sticky top-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="hidden md:block">
                    <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
                        {{ $event->title }}
                    </h1>
                    <p class="text-[11px] text-blue-400 uppercase tracking-widest font-semibold">
                        Welcome, {{ $attendee->full_name }}
                    </p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                @if($event->status === 'live')
                    <span class="flex items-center px-4 py-1.5 bg-red-600/10 border border-red-600/20 text-red-500 rounded-full text-xs font-bold live-indicator">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                        LIVE
                    </span>
                @endif
                
                <button onclick="leaveStream()" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-medium transition-all border border-white/5">
                    Leave
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 mt-8">
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-[2rem] blur opacity-20 group-hover:opacity-30 transition duration-1000"></div>
            
            <div class="relative bg-black rounded-[1.5rem] overflow-hidden shadow-2xl aspect-video border border-white/5">
                @if($streamUrl)
                    <video id="stream-player" class="video-js vjs-big-play-centered w-full h-full" controls preload="auto">
                        <source src="{{ $streamUrl }}" type="application/x-mpegURL">
                    </video>
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-900/50">
                        <div class="p-6 bg-slate-800/50 rounded-full mb-4">
                             <svg class="w-12 h-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-white">
                            {{ $event->status === 'scheduled' ? 'Ready to Start' : 'Stream Offline' }}
                        </h2>
                        <p class="text-slate-400 mt-2">The broadcast hasn't started yet. Please stay tuned.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-8">
            <div class="md:col-span-3 glass-card rounded-2xl p-6">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest mb-3">About This Service</h3>
                <p class="text-slate-300 leading-relaxed">
                    {{ $event->description ?: 'No description provided for this event.' }}
                </p>
            </div>

            <div class="space-y-4">
                <div class="glass-card rounded-2xl p-5 flex items-center space-x-4">
                    <div class="p-3 bg-blue-500/10 rounded-xl text-blue-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">Viewers</p>
                        <p class="text-lg font-bold text-white" id="viewer-count">---</p>
                    </div>
                </div>

                <div class="glass-card rounded-2xl p-5 flex items-center space-x-4">
                    <div class="p-3 bg-purple-500/10 rounded-xl text-purple-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">My Group</p>
                        <p class="text-sm font-bold text-white truncate max-w-[120px]">{{ $attendee->group->name ?? 'None' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
    <script>
        const eventId = {{ $event->id }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // 1. Video.js Initialization
        @if($streamUrl)
            const player = videojs('stream-player', {
                fluid: true,
                liveui: true,
                responsive: true,
                playbackRates: [0.5, 1, 1.5, 2]
            });
        @endif

        // 2. Heartbeat Logic (FIXES DUPLICATE COUNTING)
        // This tells the server "I am still here" every 30 seconds
        async function sendHeartbeat() {
            try {
                await fetch(`/watch/${eventId}/heartbeat`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } catch (e) {
                console.error("Heartbeat failed", e);
            }
        }

        // Ping immediately on load, then every 30 seconds
        sendHeartbeat();
        setInterval(sendHeartbeat, 30000);

        // 3. Viewer Counter UI Update
        function updateViewers() {
            fetch(`/api/events/${eventId}/viewers`)
                .then(r => r.json())
                .then(data => {
                    // Now shows the accurate "active" count from your model
                    document.getElementById('viewer-count').textContent = data.count.toLocaleString();
                })
                .catch(() => {
                    console.log('Viewer count update failed');
                });
        }

        updateViewers();
        setInterval(updateViewers, 30000);

        // 4. Leave Function
        async function leaveStream() {
            if (confirm('You are about to disconnect from the service. Continue?')) {
                try {
                    await fetch(`/watch/${eventId}/leave`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                } finally {
                    window.location.href = '/';
                }
            }
        }
    </script>
</body>
</html>