<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TV Network Hub</title>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Montserrat', sans-serif; background: #07122B; }

    .slide { position: absolute; inset: 0; opacity: 0; transition: opacity 1s ease; pointer-events: none; }
    .slide.active { opacity: 1; pointer-events: all; }
    .slide.active .kb { animation: kb 9s ease-out forwards; }
    @keyframes kb { from { transform: scale(1.06); } to { transform: scale(1); } }
    .slide.active .entrance { animation: fadeUp 0.7s 0.35s both ease-out; }
    @keyframes fadeUp { from { opacity:0; transform: translateY(22px); } to { opacity:1; transform: translateY(0); } }

    .watch-btn { position: relative; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .watch-btn::after { content:''; position:absolute; inset:0; background:rgba(255,255,255,0.12); transform:translateX(-100%); transition:transform 0.35s ease; }
    .watch-btn:hover::after { transform:translateX(0); }
    .watch-btn:hover { transform:scale(1.04); box-shadow: 0 10px 40px rgba(0,0,0,0.6) !important; }

    @keyframes livePulse { 0%,100%{ opacity:1;transform:scale(1); } 50%{ opacity:0.3;transform:scale(1.7); } }
    .live-dot { animation: livePulse 1.3s ease-in-out infinite; }

    .pbar { transition: width linear; }

    .nav-link { position:relative; }
    .nav-link::after { content:''; position:absolute; bottom:-2px; left:0; right:0; height:1px; background:currentColor; transform:scaleX(0); transform-origin:left; transition:transform 0.25s ease; }
    .nav-link:hover::after { transform:scaleX(1); }

    .thumb { transition: opacity 0.3s ease; border-bottom: 2px solid transparent; cursor: pointer; }
    .thumb:hover { opacity: 0.85 !important; }
    .thumb.active { border-bottom-color: var(--thumb-accent, #E8B84B); }

    html { scroll-behavior: smooth; }
    .nav-link.active::after { transform: scaleX(1); }
    .stream-form { display:inline-block; margin:0; padding:0; }
    .stream-form button { background:none; border:none; padding:0; cursor:pointer; font-family:inherit; width:100%; }
  </style>
</head>
<body>

<div
  x-data="{
    current: 0,
    timer: null,
    progress: 0,
    duration: 7000,
    slides: [
      {
        img: 'img/1.jpeg',
        name: 'LoveWorld UK',
        live: true,
        href: 'https://loveworlduk.org/watch-live/',
        accent: '#1E88E5',
        accentDark: '#0D47A1'
      },
      {
        img: 'img/2.jpeg',
        name: 'Alpha TV',
        live: true,
        href: 'https://www.myalphatv.com',
        accent: '#E8B84B',
        accentDark: '#8A6010'
      },
      {
        img: 'img/3.jpeg',
        name: 'LN24 International',
        live: true,
        href: 'https://ln24international.com/',
        accent: '#E53935',
        accentDark: '#7B1111'
      },
      {
        img: 'img/4.jpeg',
        name: 'LoveWorld USA',
        live: true,
        href: 'https://loveworldusa.org/',
        accent: '#2ECC71',
        accentDark: '#186A3B'
      },
      {
        img: 'img/5.jpeg',
        name: 'LoveWorld CAN',
        live: true,
        href: 'https://loveworldcan.ca/live/',
        accent: '#E8B84B',
        accentDark: '#8A6010'
      },
      {
        img: 'img/6.jpeg',
        name: 'LoveWorld India',
        live: true,
        href: 'https://lbntv.org/',
        accent: '#FF7043',
        accentDark: '#BF360C'
      },
      {
        img: 'img/7.jpeg',
        name: 'LoveWorld SAT',
        live: true,
        href: 'https://loveworldsat.org/live-tv/',
        accent: '#AB47BC',
        accentDark: '#6A1B9A'
      },
      {
        img: 'img/8.jpeg',
        name: 'LoveWorld XP',
        live: true,
        href: 'https://lxp.tv/watch-live/',
        accent: '#26C6DA',
        accentDark: '#00838F'
      },
      {
        img: 'img/9.jpeg',
        name: 'New Media TV',
        live: true,
        href: 'https://www.newmediatvuk.com/',
        accent: '#66BB6A',
        accentDark: '#2E7D32'
      }
    ],
    get s() { return this.slides[this.current]; },
    goTo(n) {
      this.current = (n + this.slides.length) % this.slides.length;
      this.restartProgress();
    },
    next() { this.goTo(this.current + 1); },
    prev() { this.goTo(this.current - 1); },
    restartProgress() {
      this.progress = 0;
      clearInterval(this.timer);
      this.$nextTick(() => { this.progress = 100; });
      this.timer = setInterval(() => this.next(), this.duration);
    },
    init() { this.restartProgress(); }
  }"
>

  <!-- NAV -->
<nav class="flex items-center justify-between px-8 md:px-12 py-4"
    style="background:#ffffff; border-bottom:1px solid rgba(0,0,0,0.08); position:relative; z-index:50;">
    <div class="flex items-center gap-2.5">
        <img src="img/logo2.png" style="height:40px; width:auto;">
    </div>
    <div class="hidden md:flex items-center gap-7">
        <a href="#networks" class="nav-link font-medium" style="color:rgba(0,0,0,0.5); font-size:0.67rem; letter-spacing:0.16em; text-transform:uppercase; text-decoration:none;">Networks</a>
        <a href="/join/authenticate" class="nav-link font-medium" style="color:rgba(0,0,0,0.5); font-size:0.67rem; letter-spacing:0.16em; text-transform:uppercase; text-decoration:none;">Watch Stream</a>
        <a href="#contact" class="nav-link font-medium" style="color:rgba(0,0,0,0.5); font-size:0.67rem; letter-spacing:0.16em; text-transform:uppercase; text-decoration:none;">Contact</a>
    </div>
    <div class="flex items-center gap-2 px-3 py-1.5 rounded-sm"
        style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.28);">
        <span class="live-dot w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
        <span class="font-bold" style="color:#EF4444; font-size:0.6rem; letter-spacing:0.18em;">LIVE</span>
    </div>
</nav>

  <!-- CAROUSEL -->
  <div class="relative w-full overflow-hidden" style="height: 520px;">

    <template x-for="(slide, i) in slides" :key="i">
      <div class="slide" :class="{ active: current === i }">

        <!-- Full-bleed image -->
        <div class="kb absolute inset-0 origin-center">
          <img :src="slide.img" class="w-full h-full object-cover object-center" alt=""/>
        </div>

        {{-- <!-- Gradient overlays: image lives on right, CTA on left -->
        <div class="absolute inset-0" style="background: linear-gradient(90deg, rgba(4,18,65,0.92) 0%, rgba(4,18,65,0.6) 38%, rgba(4,18,65,0.08) 65%, transparent 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(4,18,65,0.75) 0%, transparent 42%);"></div> --}}

        <!-- Subtle vertical accent bar -->
        <div class="absolute top-0 bottom-0 w-px" :style="`left:42%; background: linear-gradient(to bottom, transparent, ${slide.accent}30, transparent);`"></div>

        <!-- BOTTOM-LEFT: network label + CTA -->
        <div class="entrance absolute bottom-0 left-0 px-10 md:px-14 pb-16">

          <!-- Subtle network name — small chyron style -->
          <div class="flex items-center gap-2.5 mb-3">
            <div class="h-px w-5" :style="`background:${slide.accent};`"></div>
            <span class="font-semibold tracking-widest uppercase"
              :style="`color:${slide.accent}; font-size:0.6rem; letter-spacing:0.22em; opacity:0.85;`"
              x-text="slide.name">
            </span>
          </div>

          <!-- Live badge -->
          <template x-if="slide.live">
            <div class="flex items-center gap-2 mb-5">
              <span class="live-dot w-2 h-2 rounded-full bg-red-500 inline-block"></span>
              <span class="font-bold tracking-widest uppercase" style="color:#EF4444; font-size:0.62rem; letter-spacing:0.2em;">Streaming Live Now</span>
            </div>
          </template>

          <!-- Watch Network — POST to Laravel /join/authenticate -->
          <form class="stream-form" method="POST" action="/join/authenticate"
            @submit.prevent="
              const f = $el;
              f.querySelector('[name=stream_url]').value = slide.href;
              f.querySelector('[name=network_name]').value = slide.name;
              f.submit();
            "
          >
            @csrf
            <input type="hidden" name="stream_url" value=""/>
            <input type="hidden" name="network_name" value=""/>
            <button
              type="submit"
              class="watch-btn inline-flex items-center gap-3 rounded-sm text-white font-bold"
              :style="`
                padding: 0.85rem 2.2rem;
                background: linear-gradient(135deg, ${slide.accentDark} 0%, ${slide.accent} 100%);
                font-size: 0.76rem;
                letter-spacing: 0.12em;
                text-transform: uppercase;
                box-shadow: 0 6px 28px rgba(0,0,0,0.5);
              `"
            >
              <svg width="18" height="18" viewBox="0 0 18 18" fill="none">
                <circle cx="9" cy="9" r="9" fill="rgba(255,255,255,0.18)"/>
                <path d="M7 6l5 3-5 3V6z" fill="white"/>
              </svg>
              Watch Network
            </button>
          </form>
        </div>

        <!-- Slide counter: top right -->
        <div class="absolute top-6 right-10 flex items-baseline gap-1" style="z-index:10;">
          <span class="font-bold text-white" style="font-size:1.15rem; font-variant-numeric:tabular-nums;" x-text="String(i+1).padStart(2,'0')"></span>
          <span style="color:rgba(255,255,255,0.18); margin:0 2px;">/</span>
          <span style="color:rgba(255,255,255,0.28); font-size:0.8rem;" x-text="String(slides.length).padStart(2,'0')"></span>
        </div>

      </div>
    </template>

    <!-- Arrow controls -->
    <div class="absolute right-7 top-1/2 -translate-y-1/2 flex flex-col gap-2.5" style="z-index:20;">
      <button @click="prev()"
        class="w-9 h-9 flex items-center justify-center rounded-sm transition-all duration-200 hover:scale-110 active:scale-95"
        style="background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px);">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
          <path d="M18 15l-6-6-6 6" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
      <button @click="next()"
        class="w-9 h-9 flex items-center justify-center rounded-sm transition-all duration-200 hover:scale-110 active:scale-95"
        style="background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.14); backdrop-filter:blur(6px);">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none">
          <path d="M6 9l6 6 6-6" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <!-- Progress bar -->
    <div class="absolute bottom-0 left-0 right-0" style="height:2px; background:rgba(255,255,255,0.07); z-index:20;">
      <div class="pbar h-full" :style="`width:${progress}%; background:${s.accent}; transition-duration:${duration}ms;`"></div>
    </div>

  </div>

  <!-- THUMBNAIL STRIP -->
  <div class="flex w-full" style="background:#07122B; border-top:1px solid rgba(255,255,255,0.05);">
    <template x-for="(slide, i) in slides" :key="i">
      <button
        @click="goTo(i)"
        class="thumb flex-1 relative overflow-hidden"
        :class="{ active: current === i }"
        :style="`
          height: 76px;
          opacity: ${current === i ? '1' : '0.38'};
          --thumb-accent: ${slide.accent};
          border-bottom: 2px solid ${current === i ? slide.accent : 'transparent'};
        `"
      >
        <img :src="slide.img" class="w-full h-full object-cover object-center" alt=""/>
        <div class="absolute inset-0" :style="`background: rgba(4,18,65,${current===i ? '0.25' : '0.55'});`"></div>
        <!-- Network name on thumb -->
        <div class="absolute bottom-0 left-0 right-0 px-2 pb-1.5">
          <span class="block font-semibold tracking-wider uppercase truncate"
            :style="`color:rgba(255,255,255,${current===i ? '0.9' : '0.45'}); font-size:0.52rem; letter-spacing:0.14em;`"
            x-text="slide.name">
          </span>
        </div>
        <template x-if="slide.live">
          <span class="absolute top-1.5 left-2 flex items-center gap-1">
            <span class="live-dot w-1.5 h-1.5 rounded-full bg-red-500 inline-block"></span>
          </span>
        </template>
      </button>
    </template>
  </div>

  <!-- DOT INDICATORS -->
  <div class="flex items-center justify-center gap-2 py-4" style="background:#07122B;">
    <template x-for="(slide, i) in slides" :key="i">
      <button
        @click="goTo(i)"
        class="rounded-sm transition-all duration-300"
        :style="current === i
          ? `background:${slide.accent}; width:1.75rem; height:3px;`
          : 'background:rgba(255,255,255,0.18); width:6px; height:3px;'"
      ></button>
    </template>
  </div>

</div><!-- end alpine wrapper -->

<!-- ═══════════════════════════════════════════
     ALL NETWORKS GRID
════════════════════════════════════════════ -->
<section id="networks" style="background:#060F22; padding: 5rem 0 6rem; border-top: 1px solid rgba(255,255,255,0.05); min-height: 100vh;">
  <div style="max-width:1200px; margin:0 auto; padding:0 2.5rem;">

    <!-- Section header -->
    <div style="margin-bottom:3rem;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:0.6rem;">
        <div style="height:1px; width:2rem; background:#E8B84B;"></div>
        <span style="font-size:0.62rem; font-weight:600; letter-spacing:0.22em; text-transform:uppercase; color:#E8B84B;">All Networks</span>
      </div>
      <h2 style="font-size:1.9rem; font-weight:800; color:#fff; letter-spacing:-0.01em; line-height:1.2;">Watch Any Network, Anytime</h2>
      <p style="margin-top:0.6rem; font-size:0.84rem; color:rgba(255,255,255,0.38); font-weight:300; line-height:1.7; max-width:480px;">All LoveWorld networks streaming live. Click any card to go directly to that network.</p>
    </div>

    <!-- Grid -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1rem;">

      <!-- Network card macro -->
      <style>
        .net-card { display:block; position:relative; overflow:hidden; border-radius:6px; border:1px solid rgba(255,255,255,0.07); text-decoration:none; transition:transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease; background:#0A1733; }
        .net-card:hover { transform:translateY(-4px); box-shadow:0 16px 48px rgba(0,0,0,0.55); }
        .net-card .card-img { width:100%; height:130px; object-fit:cover; object-position:center; display:block; transition:transform 0.5s ease; }
        .net-card:hover .card-img { transform:scale(1.04); }
        .net-card .card-overlay { position:absolute; inset:0; height:130px; background:linear-gradient(to top, rgba(4,18,65,0.7) 0%, transparent 60%); }
        .net-card .card-body { padding:0.9rem 1rem 1rem; }
        .net-card .card-name { font-size:0.78rem; font-weight:700; color:#fff; letter-spacing:0.02em; margin-bottom:0.3rem; }
        .net-card .card-url { font-size:0.62rem; color:rgba(255,255,255,0.32); font-weight:400; letter-spacing:0.04em; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; margin-bottom:0.75rem; }
        .net-card .card-btn { display:inline-flex; align-items:center; gap:6px; font-size:0.62rem; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; padding:0.42rem 0.9rem; border-radius:3px; color:#fff; transition:opacity 0.2s; }
        .net-card .card-btn:hover { opacity:0.85; }
        .net-card .live-pip { position:absolute; top:10px; left:10px; display:flex; align-items:center; gap:5px; background:rgba(0,0,0,0.55); border:1px solid rgba(239,68,68,0.4); border-radius:3px; padding:3px 7px; }
        .net-card .live-pip span { font-size:0.52rem; font-weight:700; letter-spacing:0.16em; color:#EF4444; }
      </style>

      <!-- 1 -->
      <a href="https://loveworlduk.org/watch-live/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(30,136,229,0.25);">
        <img src="img/1.jpeg" alt="LoveWorld UK" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LoveWorld UK</div>
          <div class="card-url">loveworlduk.org</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#0D47A1,#1E88E5);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 2 -->
      <a href="https://www.myalphatv.com" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(232,184,75,0.25);">
        <img src="img/2.jpeg" alt="Alpha TV" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">Alpha TV</div>
          <div class="card-url">myalphatv.com</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#8A6010,#E8B84B);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 3 -->
      <a href="https://ln24international.com/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(229,57,53,0.25);">
        <img src="img/3.jpeg" alt="LN24 International" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LN24 International</div>
          <div class="card-url">ln24international.com</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#7B1111,#E53935);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 4 -->
      <a href="https://loveworldusa.org/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(46,204,113,0.25);">
        <img src="img/4.jpeg" alt="LoveWorld USA" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LoveWorld USA</div>
          <div class="card-url">loveworldusa.org</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#186A3B,#2ECC71);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 5 -->
      <a href="https://loveworldcan.ca/live/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(232,184,75,0.25);">
        <img src="img/5.jpeg" alt="LoveWorld CAN" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LoveWorld CAN</div>
          <div class="card-url">loveworldcan.ca</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#8A6010,#E8B84B);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 6 -->
      <a href="https://lbntv.org/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(255,112,67,0.25);">
        <img src="img/6.jpeg" alt="LoveWorld India" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LoveWorld India</div>
          <div class="card-url">lbntv.org</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#BF360C,#FF7043);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 7 -->
      <a href="https://loveworldsat.org/live-tv/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(171,71,188,0.25);">
        <img src="img/7.jpeg" alt="LoveWorld SAT" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LoveWorld SAT</div>
          <div class="card-url">loveworldsat.org</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#6A1B9A,#AB47BC);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 8 -->
      <a href="https://lxp.tv/watch-live/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(38,198,218,0.25);">
        <img src="img/8.jpeg" alt="LoveWorld XP" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">LoveWorld XP</div>
          <div class="card-url">lxp.tv</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#00838F,#26C6DA);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

      <!-- 9 -->
      <a href="https://www.newmediatvuk.com/" target="_blank" rel="noopener" class="net-card" style="border-color:rgba(102,187,106,0.25);">
        <img src="img/9.jpeg" alt="New Media TV" class="card-img"/>
        <div class="card-overlay"></div>
        <div class="live-pip"><span class="live-dot" style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span><span>LIVE</span></div>
        <div class="card-body">
          <div class="card-name">New Media TV</div>
          <div class="card-url">newmediatvuk.com</div>
          <div class="card-btn" style="background:linear-gradient(135deg,#2E7D32,#66BB6A);">
            <svg width="10" height="10" viewBox="0 0 12 12"><circle cx="6" cy="6" r="6" fill="rgba(255,255,255,0.2)"/><path d="M5 3.5l3.5 2.5L5 8.5V3.5z" fill="white"/></svg>
            Watch Live
          </div>
        </div>
      </a>

    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     CONTACT SECTION
════════════════════════════════════════════ -->
<section id="contact" style="background:#07122B; padding:5rem 0; border-top:1px solid rgba(255,255,255,0.05);">
  <div style="max-width:1200px; margin:0 auto; padding:0 2.5rem; display:grid; grid-template-columns:1fr 1fr; gap:4rem; align-items:start;">

    <!-- Left: info -->
    <div>
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:0.6rem;">
        <div style="height:1px; width:2rem; background:#E8B84B;"></div>
        <span style="font-size:0.62rem; font-weight:600; letter-spacing:0.22em; text-transform:uppercase; color:#E8B84B;">Get In Touch</span>
      </div>
      <h2 style="font-size:1.8rem; font-weight:800; color:#fff; margin-bottom:1rem; line-height:1.2;">Contact Us</h2>
      <p style="font-size:0.86rem; color:rgba(255,255,255,0.42); font-weight:300; line-height:1.8; margin-bottom:2.5rem; max-width:400px;">Have a question about our networks, programming schedules, or partnerships? We'd love to hear from you.</p>

      <!-- Contact details -->
      <div style="display:flex; flex-direction:column; gap:1.4rem;">

        <div style="display:flex; align-items:flex-start; gap:1rem;">
          <div style="width:36px; height:36px; border-radius:6px; background:rgba(232,184,75,0.1); border:1px solid rgba(232,184,75,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
              <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5z" fill="#E8B84B"/>
            </svg>
          </div>
          <div>
            <div style="font-size:0.72rem; font-weight:600; color:rgba(255,255,255,0.5); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:3px;">Address</div>
            <div style="font-size:0.84rem; color:rgba(255,255,255,0.8); line-height:1.6;">LoveWorld Networks HQ<br/>12 Kingdom Boulevard<br/>London, EC1A 1BB, United Kingdom</div>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:1rem;">
          <div style="width:36px; height:36px; border-radius:6px; background:rgba(232,184,75,0.1); border:1px solid rgba(232,184,75,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
              <path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C9.61 21 3 14.39 3 6.5a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.45.57 3.57a1 1 0 0 1-.25 1.02l-2.2 2.2z" fill="#E8B84B"/>
            </svg>
          </div>
          <div>
            <div style="font-size:0.72rem; font-weight:600; color:rgba(255,255,255,0.5); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:3px;">Phone</div>
            <div style="font-size:0.84rem; color:rgba(255,255,255,0.8);">+44 (0) 20 7946 0958</div>
          </div>
        </div>

        <div style="display:flex; align-items:flex-start; gap:1rem;">
          <div style="width:36px; height:36px; border-radius:6px; background:rgba(232,184,75,0.1); border:1px solid rgba(232,184,75,0.2); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none">
              <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z" fill="#E8B84B"/>
            </svg>
          </div>
          <div>
            <div style="font-size:0.72rem; font-weight:600; color:rgba(255,255,255,0.5); letter-spacing:0.1em; text-transform:uppercase; margin-bottom:3px;">Email</div>
            <div style="font-size:0.84rem; color:rgba(255,255,255,0.8);">info@loveworldnetworks.org</div>
          </div>
        </div>

      </div>

      <!-- Social links -->
      <div style="margin-top:2.5rem; display:flex; gap:0.75rem;">
        <a href="#" style="width:36px; height:36px; border-radius:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='#E8B84B'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M24 4.56v14.91A4.53 4.53 0 0 1 19.47 24H4.53A4.53 4.53 0 0 1 0 19.47V4.53A4.53 4.53 0 0 1 4.53 0h14.94A4.53 4.53 0 0 1 24 4.53v.03zM9 19.5V9H6v10.5h3zM7.5 7.68a1.75 1.75 0 1 0 0-3.5 1.75 1.75 0 0 0 0 3.5zM19.5 19.5v-6a4.5 4.5 0 0 0-4.5-4.5 3 3 0 0 0-2.5 1.38V9H10v10.5h2.5v-5.62a2 2 0 0 1 2-2 2 2 0 0 1 2 2v5.62h3z"/></svg>
        </a>
        <a href="#" style="width:36px; height:36px; border-radius:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='#E8B84B'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M23.953 4.57a10 10 0 0 1-2.825.775 4.958 4.958 0 0 0 2.163-2.723 10.054 10.054 0 0 1-3.127 1.184 4.92 4.92 0 0 0-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 0 0-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 0 1-2.228-.616v.06a4.923 4.923 0 0 0 3.946 4.827 4.996 4.996 0 0 1-2.212.085 4.937 4.937 0 0 0 4.604 3.417 9.868 9.868 0 0 1-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 0 0 7.557 2.209c9.054 0 13.999-7.496 13.999-13.986 0-.209 0-.42-.015-.63a9.936 9.936 0 0 0 2.46-2.548l-.047-.02z"/></svg>
        </a>
        <a href="#" style="width:36px; height:36px; border-radius:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='#E8B84B'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M23.5 6.19a3.02 3.02 0 0 0-2.12-2.14C19.54 3.5 12 3.5 12 3.5s-7.54 0-9.38.55A3.02 3.02 0 0 0 .5 6.19C0 8.04 0 12 0 12s0 3.96.5 5.81a3.02 3.02 0 0 0 2.12 2.14C4.46 20.5 12 20.5 12 20.5s7.54 0 9.38-.55a3.02 3.02 0 0 0 2.12-2.14C24 15.96 24 12 24 12s0-3.96-.5-5.81zM9.75 15.5v-7l6.5 3.5-6.5 3.5z"/></svg>
        </a>
        <a href="#" style="width:36px; height:36px; border-radius:6px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); display:flex; align-items:center; justify-content:center; text-decoration:none; transition:all 0.2s;" onmouseover="this.style.borderColor='#E8B84B'" onmouseout="this.style.borderColor='rgba(255,255,255,0.1)'">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="rgba(255,255,255,0.5)"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z"/></svg>
        </a>
      </div>
    </div>

    <!-- Right: contact form -->
    <div style="background:#0A1733; border:1px solid rgba(255,255,255,0.07); border-radius:10px; padding:2rem;">
      <h3 style="font-size:1rem; font-weight:700; color:#fff; margin-bottom:1.5rem; letter-spacing:0.02em;">Send a Message</h3>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
        <div>
          <label style="display:block; font-size:0.65rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:6px;">First Name</label>
          <input type="text" placeholder="John" style="width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:5px; padding:0.65rem 0.9rem; font-size:0.84rem; color:#fff; font-family:'Montserrat',sans-serif; outline:none; transition:border-color 0.2s;" onfocus="this.style.borderColor='#E8B84B'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"/>
        </div>
        <div>
          <label style="display:block; font-size:0.65rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:6px;">Last Name</label>
          <input type="text" placeholder="Doe" style="width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:5px; padding:0.65rem 0.9rem; font-size:0.84rem; color:#fff; font-family:'Montserrat',sans-serif; outline:none; transition:border-color 0.2s;" onfocus="this.style.borderColor='#E8B84B'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"/>
        </div>
      </div>

      <div style="margin-bottom:1rem;">
        <label style="display:block; font-size:0.65rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:6px;">Email Address</label>
        <input type="email" placeholder="john@example.com" style="width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:5px; padding:0.65rem 0.9rem; font-size:0.84rem; color:#fff; font-family:'Montserrat',sans-serif; outline:none; transition:border-color 0.2s;" onfocus="this.style.borderColor='#E8B84B'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"/>
      </div>

      <div style="margin-bottom:1rem;">
        <label style="display:block; font-size:0.65rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:6px;">Subject</label>
        <select style="width:100%; background:#0D1E3A; border:1px solid rgba(255,255,255,0.1); border-radius:5px; padding:0.65rem 0.9rem; font-size:0.84rem; color:rgba(255,255,255,0.7); font-family:'Montserrat',sans-serif; outline:none; transition:border-color 0.2s;" onfocus="this.style.borderColor='#E8B84B'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'">
          <option value="">Select a topic…</option>
          <option>General Enquiry</option>
          <option>Programming & Schedule</option>
          <option>Partnership & Advertising</option>
          <option>Technical Support</option>
          <option>Prayer Request</option>
        </select>
      </div>

      <div style="margin-bottom:1.5rem;">
        <label style="display:block; font-size:0.65rem; font-weight:600; letter-spacing:0.12em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:6px;">Message</label>
        <textarea placeholder="Write your message here…" rows="4" style="width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.1); border-radius:5px; padding:0.65rem 0.9rem; font-size:0.84rem; color:#fff; font-family:'Montserrat',sans-serif; outline:none; resize:vertical; transition:border-color 0.2s;" onfocus="this.style.borderColor='#E8B84B'" onblur="this.style.borderColor='rgba(255,255,255,0.1)'"></textarea>
      </div>

      <button style="width:100%; padding:0.85rem; background:linear-gradient(135deg,#8A6010,#E8B84B); border:none; border-radius:5px; color:#fff; font-family:'Montserrat',sans-serif; font-size:0.76rem; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; cursor:pointer; transition:opacity 0.2s;" onmouseover="this.style.opacity='0.88'" onmouseout="this.style.opacity='1'">
        Send Message
      </button>
    </div>

  </div>
</section>

<!-- ═══════════════════════════════════════════
     FOOTER
════════════════════════════════════════════ -->
<footer style="background:#04091A; border-top:1px solid rgba(255,255,255,0.06); padding:4rem 0 0;">
  <div style="max-width:1200px; margin:0 auto; padding:0 2.5rem;">

    <div style="display:grid; grid-template-columns:2fr 1fr 1fr 1fr; gap:3rem; padding-bottom:3rem; border-bottom:1px solid rgba(255,255,255,0.06);">

      <!-- Brand column -->
      <div>
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem;">
          <svg width="24" height="24" viewBox="0 0 28 28" fill="none">
            <circle cx="14" cy="14" r="3.5" fill="#E8B84B"/>
            <path d="M8 8a8.5 8.5 0 0 0 0 12" stroke="#E8B84B" stroke-width="1.8" stroke-linecap="round" fill="none"/>
            <path d="M20 8a8.5 8.5 0 0 1 0 12" stroke="#E8B84B" stroke-width="1.8" stroke-linecap="round" fill="none"/>
          </svg>
          <span style="font-size:0.9rem; font-weight:800; color:#fff; letter-spacing:0.04em;"><span style="color:#E8B84B;">LOVEWORLD</span> NETWORKS</span>
        </div>
        <p style="font-size:0.82rem; color:rgba(255,255,255,0.35); font-weight:300; line-height:1.8; max-width:280px; margin-bottom:1.5rem;">Broadcasting the Gospel of Jesus Christ to every nation, tongue and tribe — 24 hours a day, 7 days a week.</p>
        <div style="display:flex; align-items:center; gap:6px; padding:0.5rem 0.9rem; background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.2); border-radius:4px; width:fit-content;">
          <span style="width:6px;height:6px;border-radius:50%;background:#EF4444;display:inline-block;animation:livePulse 1.3s ease-in-out infinite;"></span>
          <span style="font-size:0.6rem; font-weight:700; color:#EF4444; letter-spacing:0.18em;">9 NETWORKS LIVE NOW</span>
        </div>
      </div>

      <!-- Networks -->
      <div>
        <h4 style="font-size:0.65rem; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:1.1rem;">Our Networks</h4>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:0.6rem;">
          <li><a href="https://loveworlduk.org/watch-live/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LoveWorld UK</a></li>
          <li><a href="https://www.myalphatv.com" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Alpha TV</a></li>
          <li><a href="https://ln24international.com/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LN24 International</a></li>
          <li><a href="https://loveworldusa.org/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LoveWorld USA</a></li>
          <li><a href="https://loveworldcan.ca/live/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LoveWorld CAN</a></li>
          <li><a href="https://lbntv.org/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LoveWorld India</a></li>
          <li><a href="https://loveworldsat.org/live-tv/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LoveWorld SAT</a></li>
          <li><a href="https://lxp.tv/watch-live/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">LoveWorld XP</a></li>
          <li><a href="https://www.newmediatvuk.com/" target="_blank" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; font-weight:400; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">New Media TV</a></li>
        </ul>
      </div>

      <!-- Quick links -->
      <div>
        <h4 style="font-size:0.65rem; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:1.1rem;">Quick Links</h4>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:0.6rem;">
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">About Us</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">TV Schedule</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Prayer Requests</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Partner With Us</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Advertising</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Careers</a></li>
        </ul>
      </div>

      <!-- Broadcast info -->
      <div>
        <h4 style="font-size:0.65rem; font-weight:700; letter-spacing:0.18em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:1.1rem;">Broadcast Info</h4>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:0.6rem;">
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Satellite Frequencies</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Cable Providers</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Mobile Apps</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Smart TV Guide</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Technical FAQ</a></li>
          <li><a href="#" style="font-size:0.8rem; color:rgba(255,255,255,0.55); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#E8B84B'" onmouseout="this.style.color='rgba(255,255,255,0.55)'">Privacy Policy</a></li>
        </ul>
      </div>

    </div>

    <!-- Footer bottom bar -->
    <div style="display:flex; align-items:center; justify-content:space-between; padding:1.5rem 0; flex-wrap:wrap; gap:1rem;">
      <p style="font-size:0.72rem; color:rgba(255,255,255,0.22); font-weight:300;">
        &copy; 2025 LoveWorld Networks. All rights reserved. Broadcasting the love of God worldwide.
      </p>
      <div style="display:flex; gap:1.5rem;">
        <a href="#" style="font-size:0.72rem; color:rgba(255,255,255,0.22); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.6)'" onmouseout="this.style.color='rgba(255,255,255,0.22)'">Terms</a>
        <a href="#" style="font-size:0.72rem; color:rgba(255,255,255,0.22); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.6)'" onmouseout="this.style.color='rgba(255,255,255,0.22)'">Privacy</a>
        <a href="#" style="font-size:0.72rem; color:rgba(255,255,255,0.22); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='rgba(255,255,255,0.6)'" onmouseout="this.style.color='rgba(255,255,255,0.22)'">Cookies</a>
      </div>
    </div>

  </div>
</footer>

</body>
</html>