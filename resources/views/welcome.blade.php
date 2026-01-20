<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Online | Experience Grace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .live-glow { box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); }
    </style>
</head>
<body class="bg-[#0f172a] text-slate-200 min-h-screen">

    <nav class="flex justify-between items-center px-8 py-6 max-w-7xl mx-auto">
        <div class="flex items-center space-x-2">
            <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
            </div>
            <span class="text-xl font-extrabold tracking-tight text-white italic">CE<span class="text-blue-500">LIVE</span></span>
        </div>
        <a href="/admin" class="text-sm font-medium text-slate-400 hover:text-white transition">Admin →</a>
    </nav>

    <main class="max-w-7xl mx-auto px-6 pb-20">
        
        <header class="py-12 md:py-20 text-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-white mb-6 tracking-tighter">
                Experience the <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-500">Service Online.</span>
            </h1>
            <p class="text-lg text-slate-400 max-w-2xl mx-auto">
                Join our global community in worship and word. Real-time streaming for every believer, everywhere.
            </p>
        </header>

        @php
            $liveEvents = \App\Models\StreamEvent::where('status', 'live')->get();
            $upcomingEvents = \App\Models\StreamEvent::where('status', 'scheduled')->orderBy('created_at', 'desc')->limit(3)->get();
        @endphp

        @if($liveEvents->count() > 0)
            <section class="mb-16">
                <div class="flex items-center space-x-3 mb-8">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                    <h2 class="text-sm font-bold uppercase tracking-widest text-red-500">Live Now</h2>
                </div>

                <div class="grid gap-8">
                    @foreach($liveEvents as $event)
                        <div class="group relative bg-slate-800/50 rounded-3xl overflow-hidden border border-slate-700 hover:border-blue-500/50 transition-all duration-500 live-glow">
                            <div class="md:flex">
                                <div class="md:w-1/2 bg-slate-900 aspect-video relative overflow-hidden">
                                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center backdrop-blur-md group-hover:scale-110 transition-transform">
                                            <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-8 md:w-1/2 flex flex-col justify-center">
                                    <div class="flex items-center space-x-2 mb-4">
                                        @if($event->isPastorsOnly())
                                            <span class="px-3 py-1 bg-amber-500/10 text-amber-500 text-[10px] font-bold uppercase rounded-full border border-amber-500/20">🔒 Pastors Only</span>
                                        @else
                                            <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase rounded-full border border-emerald-500/20">🌐 Open to All</span>
                                        @endif
                                    </div>
                                    <h3 class="text-3xl font-bold text-white mb-4 leading-tight">{{ $event->title }}</h3>
                                    <p class="text-slate-400 mb-8 line-clamp-2">{{ $event->description }}</p>
                                    
                                    <div class="flex items-center justify-between mt-auto">
                                        <div class="text-sm text-slate-500">
                                            <strong class="text-white">{{ $event->getCurrentViewersCount() }}</strong> People Watching
                                        </div>
                                        <a href="{{ route('stream.show', $event) }}" class="px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20">
                                            Join Meeting
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <h2 class="text-xl font-bold text-white mb-8">Upcoming Gatherings</h2>
            <div class="grid md:grid-cols-3 gap-6">
                @forelse($upcomingEvents as $event)
                    <div class="glass p-6 rounded-2xl hover:bg-white/5 transition group">
                        <div class="text-blue-500 text-xs font-bold uppercase mb-3">Next Week</div>
                        <h4 class="text-lg font-bold text-white mb-2 group-hover:text-blue-400 transition">{{ $event->title }}</h4>
                        <p class="text-sm text-slate-500 mb-6 line-clamp-2">{{ $event->description }}</p>
                        <div class="flex items-center justify-between text-[11px] font-bold tracking-widest uppercase text-slate-600">
                            <span>Scheduled</span>
                            <span>{{ $event->created_at->format('M d') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-20 text-center glass rounded-3xl">
                        <p class="text-slate-500 italic font-light">No other events scheduled at this time.</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>

</body>
</html>