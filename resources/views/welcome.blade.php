@php
    $liveEvents = \App\Models\StreamEvent::where('status', 'live')->get();
    $upcomingEvents = \App\Models\StreamEvent::where('status', 'scheduled')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();
    
    // Pick the first live event for the hero and links
    $liveEvent = $liveEvents->first();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Online | Experience Grace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .live-glow { box-shadow: 0 0 40px rgba(37, 99, 235, 0.15); }
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
                @endif
            @else
                <a href="/admin" class="text-sm font-medium text-slate-400 hover:text-white transition">Admin Dashboard →</a>
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
                                <span class="w-1.5 h-1.5 bg-white rounded-full mr-2"></span> LIVE
                            </span>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <a href="{{ route('stream.show', $liveEvent) }}" class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white fill-current translate-x-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-10 md:w-2/5 flex flex-col justify-center bg-slate-800/20 backdrop-blur-sm">
                        <h2 class="text-sm font-bold text-blue-500 uppercase tracking-widest mb-4">Happening Now</h2>
                        <h3 class="text-4xl font-extrabold text-white mb-4 leading-tight tracking-tighter">{{ $liveEvent->title }}</h3>
                        <p class="text-slate-400 mb-8 leading-relaxed font-medium line-clamp-3">{{ $liveEvent->description }}</p>
                        
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('stream.show', $liveEvent) }}" class="flex-grow text-center px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-2xl transition-all shadow-xl shadow-blue-600/30">
                                Join Experience
                            </a>
                        </div>
                        <div class="mt-6 flex items-center text-slate-500 text-sm font-medium">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>
                            {{ $liveEvent->getCurrentViewersCount() }} People Online
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-20 px-6">
                <div class="w-24 h-24 bg-slate-800/50 rounded-3xl flex items-center justify-center mx-auto mb-8 border border-white/5">
                    <svg class="w-12 h-12 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight mb-4">No Active Broadcast</h1>
                <p class="text-slate-400 max-w-md mx-auto mb-10 leading-relaxed text-lg">
                    We aren't live right now, but we'll be back soon!
                </p>
                
                @if(!Session::has('attendee_id'))
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <p class="text-slate-500 italic font-medium">Check back later for our next service.</p>
                </div>
                @endif
            </div>
        @endif

    </main>

    <footer class="py-10 text-center text-slate-600 text-[10px] font-bold uppercase tracking-[0.2em] mt-auto">
        &copy; {{ date('Y') }} Christ Embassy Southern Africa
    </footer>

</body>
</html>