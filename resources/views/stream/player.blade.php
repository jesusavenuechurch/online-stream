<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} - Live Stream</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpeg') }}">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: #f8fafc; }
        .glass-nav { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .glass-card { background: rgba(30, 41, 59, 0.4); backdrop-filter: blur(8px); border: 1px solid rgba(255, 255, 255, 0.1); }
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
                
                <span id="session-timer" class="text-slate-400 text-sm px-3 py-1 bg-slate-800 rounded-lg">
                    Session: <span id="time-remaining">2:00:00</span>
                </span>
                
                <button onclick="leaveStream()" 
                        class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-xl text-sm font-medium transition-all border border-white/5">
                    Leave
                </button>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 mt-8">
        <div class="relative group">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-[2rem] blur opacity-20 group-hover:opacity-30 transition duration-1000"></div>
            
            <div class="relative bg-black rounded-[1.5rem] overflow-hidden shadow-2xl aspect-video border border-white/5">
                <video id="stream-player" class="video-js vjs-big-play-centered w-full h-full" controls preload="auto"></video>
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
        const SESSION_DURATION = 2 * 60 * 60 * 1000; // 2 hours in milliseconds
        const sessionStartTime = Date.now();
        
        // Initialize player with external stream
        const player = videojs('stream-player', {
            fluid: true,
            liveui: true,
            responsive: true,
            html5: {
                vhs: {
                    overrideNative: true,
                    enableLowInitialPlaylist: true,
                }
            }
        });

        player.src({
            src: 'https://cdnstack.internetmultimediaonline.org/lwsat/saRegionStr/playlist.m3u8',
            type: 'application/x-mpegURL'
        });

        // Auto-play
        player.ready(function() {
            this.muted(true);
            this.play().then(() => {
                setTimeout(() => this.muted(false), 500);
            }).catch(error => {
                console.log('Auto-play prevented:', error);
            });
        });

        // Session timer display
        function updateSessionTimer() {
            const elapsed = Date.now() - sessionStartTime;
            const remaining = SESSION_DURATION - elapsed;
            
            if (remaining <= 0) {
                autoLogout();
                return;
            }
            
            const hours = Math.floor(remaining / 3600000);
            const minutes = Math.floor((remaining % 3600000) / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            
            document.getElementById('time-remaining').textContent = 
                `${hours}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        // Update timer every second
        const timerInterval = setInterval(updateSessionTimer, 1000);

        // Auto-logout after 2 hours
        const autoLogoutTimeout = setTimeout(() => {
            autoLogout();
        }, SESSION_DURATION);

        async function autoLogout() {
            clearInterval(timerInterval);
            clearInterval(heartbeatInterval);
            clearTimeout(autoLogoutTimeout);
            
            if (player) {
                player.pause();
                player.dispose();
            }
            
            try {
                await fetch(`/watch/${eventId}/leave`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
            } catch (e) {
                console.error('Logout failed:', e);
            }
            
            alert('Your 2-hour session has ended. Please log in again to continue watching.');
            window.location.href = '/watch/{{ $event->id }}';
        }

        // Heartbeat every 30 seconds
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

        sendHeartbeat();
        const heartbeatInterval = setInterval(sendHeartbeat, 30000);

        // Leave function
        async function leaveStream() {
            if (confirm('You are about to disconnect from the service. Continue?')) {
                clearInterval(heartbeatInterval);
                clearInterval(timerInterval);
                clearTimeout(autoLogoutTimeout);
                
                if (player) {
                    player.pause();
                    player.dispose();
                }
                
                try {
                    await fetch(`/watch/${eventId}/leave`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                } finally {
                    window.location.href = 'https://www.cesouthernafrica.co.za';
                }
            }
        }

        // Clean up on page unload
        window.addEventListener('beforeunload', function() {
            if (player) {
                player.dispose();
            }
            clearInterval(heartbeatInterval);
            clearInterval(timerInterval);
            clearTimeout(autoLogoutTimeout);
        });
    </script>
</body>
</html>