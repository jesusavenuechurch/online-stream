@php
    $liveEvents = \App\Models\StreamEvent::where('status', 'live')->get();
    $upcomingEvents = \App\Models\StreamEvent::where('status', 'scheduled')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    // Pick the first live event for the hero
    $liveEvent = $liveEvents->first();
    
    // If no live event, pick the next scheduled one for the "Register Now" button
    $targetEvent = $liveEvent ?? $upcomingEvents->first() ?? \App\Models\StreamEvent::orderBy('created_at', 'desc')->first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Christ Embassy Southern Africa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/jpeg" href="{{ asset('img/logo.jpeg') }}">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .live-glow { box-shadow: 0 0 40px rgba(37, 99, 235, 0.15); }
        .register-glow { box-shadow: 0 0 30px rgba(79, 70, 229, 0.2); }
    </style>
</head>
<body class="text-slate-200 min-h-screen flex flex-col">

    <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto w-full">
        <div class="flex items-center">
            <img src="{{ asset('img/logo.jpeg') }}" alt="Christ Embassy Southern Africa" class="h-12 md:h-14 w-auto object-contain">
        </div>
        
        <div class="flex items-center space-x-6">
            @if(!Session::has('attendee_id'))
                @if($liveEvent)
                    <a href="{{ route('stream.show', $liveEvent) }}" class="text-sm font-semibold text-white bg-white/5 hover:bg-white/10 px-6 py-2.5 rounded-xl border border-white/10 transition">Sign In</a>
                @elseif($targetEvent)
                    <a href="{{ route('stream.show', $targetEvent) }}?tab=register" class="text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 px-6 py-2.5 rounded-xl transition shadow-lg shadow-indigo-500/20">Register</a>
                @endif
            @else
                <div class="flex items-center space-x-4">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Connected</span>
                    <a href="/admin" class="text-sm font-medium text-blue-400 hover:text-white transition">Admin Dashboard →</a>
                </div>
            @endif
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 flex-grow flex flex-col justify-center w-full">

        @if($liveEvent)
            <div class="group relative bg-slate-800/40 rounded-[2.5rem] overflow-hidden border border-white/5 transition-all duration-500 live-glow mt-8">
                <div class="md:flex items-stretch">
                    <div class="md:w-3/5 bg-slate-900 aspect-video relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/10 to-transparent"></div>
                        <div class="absolute top-6 left-6 z-10">
                            <span class="flex items-center px-4 py-1.5 bg-red-600 text-white rounded-full text-[10px] font-black tracking-widest uppercase animate-pulse">
                                <span class="w-1.5 h-1.5 bg-white rounded-full mr-2"></span> LIVE NOW
                            </span>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <a href="{{ route('stream.show', $liveEvent) }}" class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white fill-current translate-x-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-10 md:w-2/5 flex flex-col justify-center bg-slate-800/20 backdrop-blur-sm">
                        <h2 class="text-sm font-bold text-blue-500 uppercase tracking-widest mb-4">Current Broadcast</h2>
                        <h3 class="text-4xl font-extrabold text-white mb-4 leading-tight tracking-tighter">{{ $liveEvent->title }}</h3>
                        <p class="text-slate-400 mb-8 leading-relaxed font-medium line-clamp-3">{{ $liveEvent->description }}</p>
                        
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('stream.show', $liveEvent) }}" class="flex-grow text-center px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl transition-all shadow-xl shadow-blue-600/30">
                                Join Experience
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-20 px-6 max-w-3xl mx-auto">
                <div class="w-24 h-24 bg-slate-800/50 rounded-3xl flex items-center justify-center mx-auto mb-10 border border-white/5">
                    <svg class="w-12 h-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                </div>
                
                <h1 class="text-5xl font-black text-white tracking-tighter mb-6">No Active Broadcast</h1>
                <p class="text-slate-400 mb-12 text-xl leading-relaxed font-medium">
                    We aren't live at the moment, but you can create your account ahead of time to be ready for the next glorious experience.
                </p>
                
                @if($targetEvent && !Session::has('attendee_id'))
                <div class="inline-block p-1 rounded-[2rem] bg-gradient-to-r from-indigo-500/20 to-blue-500/20">
                    <a href="{{ route('stream.show', $targetEvent) }}?tab=register" 
                       class="flex items-center space-x-4 px-10 py-5 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-[1.8rem] transition-all register-glow text-lg uppercase tracking-wider">
                        <span>Register for Next Service</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
                @elseif(Session::has('attendee_id'))
                    <div class="py-4 px-8 bg-slate-800/50 rounded-2xl border border-white/5 inline-block">
                        <p class="text-green-400 font-bold">✓ Your account is ready. Check back soon!</p>
                    </div>
                @endif
            </div>
        @endif

    </main>

    <footer class="py-12 text-center text-slate-600 text-[10px] font-bold uppercase tracking-[0.25em] mt-auto">
        &copy; {{ date('Y') }} Christ Embassy Southern Africa
    </footer>

</body>
</html>