<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $frame->name }} — Avatar Generator</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #0a0a0f;
            --surface: #13131a;
            --border: #1e1e2e;
            --accent: #c8f135;
            --text: #f0f0f0;
            --muted: #6b6b80;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .header { text-align: center; margin-bottom: 2rem; }
        .header h1 { font-family: 'Syne', sans-serif; font-weight: 800; font-size: 2.2rem; }
        .header h1 span { color: var(--accent); }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 1.5rem;
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* The Stage: Proportional to the Frame Image */
        .stage-container {
            position: relative;
            width: 100%;
            border-radius: 16px;
            overflow: hidden;
            background: #1a1a26;
            cursor: grab;
            touch-action: none;
        }
        .stage-container:active { cursor: grabbing; }

        /* The "Sandwich" Layers */
        .user-photo {
            position: absolute;
            pointer-events: none; /* Let clicks pass through to the container */
            will-change: transform;
            z-index: 5; /* Between the background and the frame */
            /* Ensure the image doesn't have a max-width conflict from elsewhere */
            max-width: none !important; 
            min-width: 50px;
        }

        .frame-overlay {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10; /* Always on top */
        }

        .upload-trigger {
            position: absolute;
            inset: 0;
            opacity: 0;
            z-index: 20;
            cursor: pointer;
        }

        .controls { display: flex; flex-direction: column; gap: 1rem; }
        .control-row { display: flex; align-items: center; gap: 1rem; }
        
        input[type="range"] {
            flex: 1; height: 6px; border-radius: 3px; background: var(--border);
            -webkit-appearance: none; outline: none;
        }
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none; width: 20px; height: 20px;
            border-radius: 50%; background: var(--accent); cursor: pointer;
        }

        .btn {
            background: var(--accent); color: #000; border: none;
            padding: 1rem; border-radius: 12px; font-weight: 700;
            font-family: 'Syne', sans-serif; cursor: pointer; transition: 0.2s;
        }
        .btn:disabled { opacity: 0.3; cursor: not-allowed; }
        
        .placeholder-ui {
            position: absolute; inset: 0; display: flex; flex-direction: column;
            align-items: center; justify-content: center; color: var(--muted);
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Create <span>Avatar</span></h1>
    <p>{{ $frame->name }}</p>
</div>

<div x-data="avatarMaker('{{ asset('storage/' . $frame->frame_path) }}')" class="card">
    
    <div 
        class="stage-container" 
        x-ref="stage"
        :style="`aspect-ratio: ${aspectRatio};`"
        @mousedown="photoReady && startDrag($event)"
        @mousemove="photoReady && onDrag($event)"
        @mouseup="stopDrag"
        @mouseleave="stopDrag"
        @touchstart.prevent="photoReady && startDrag($event.touches[0])"
        @touchmove.prevent="photoReady && onDrag($event.touches[0])"
        @touchend="stopDrag"
    >
        <template x-if="photoReady">
            <img 
                :src="photoSrc" 
                class="user-photo"
                :style="`left: ${x}px; top: ${y}px; transform: scale(${scale}); transform-origin: 0 0;`"
            >
        </template>

        <img :src="frameUrl" class="frame-overlay" x-ref="frameEl">

        <input 
            x-show="!photoReady" 
            type="file" 
            accept="image/*" 
            class="upload-trigger" 
            @change="onFileSelect"
        >

        <div class="placeholder-ui" x-show="!photoReady">
            <p>Tap to upload photo</p>
        </div>
    </div>

    <div class="controls">
        <div class="control-row">
            <label style="font-size: 0.8rem; font-weight: bold; width: 50px;">SCALE</label>
            <input type="range" min="0.1" max="3" step="0.01" x-model="scale" :disabled="!photoReady">
        </div>
        
        <button class="btn" :disabled="!photoReady" @click="download()">
            Download Avatar
        </button>

        <p x-show="photoReady" style="text-align:center; font-size: 0.8rem; color: var(--muted);">
            Drag photo to position · <span @click="location.reload()" style="color: var(--accent); cursor:pointer;">Reset</span>
        </p>
    </div>
</div>

<script>
function avatarMaker(initialFrameUrl) {
    return {
        frameUrl: initialFrameUrl, // This is the 'storage/frames/xxx.png' link
        photoReady: false,
        photoSrc: null,
        photoImg: null,
        aspectRatio: '1/1',
        
        x: 0, y: 0, scale: 1,
        isDragging: false,
        lastX: 0, lastY: 0,

        init() {
            const img = new Image();
            img.crossOrigin = "anonymous"; // Essential for the download button to work
            img.onload = () => {
                this.aspectRatio = `${img.naturalWidth} / ${img.naturalHeight}`;
            };
            img.src = this.frameUrl;
        },

        onFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;

            this.photoSrc = URL.createObjectURL(file);
            this.photoImg = new Image();
            this.photoImg.onload = () => {
                this.photoReady = true;
                this.x = 0;
                this.y = 0;
                this.scale = 0.5;
            };
            this.photoImg.src = this.photoSrc;
        },

        startDrag(e) {
            this.isDragging = true;
            this.lastX = e.clientX;
            this.lastY = e.clientY;
        },

        onDrag(e) {
            if (!this.isDragging) return;
            const dx = e.clientX - this.lastX;
            const dy = e.clientY - this.lastY;
            this.x += dx;
            this.y += dy;
            this.lastX = e.clientX;
            this.lastY = e.clientY;
        },

        stopDrag() { this.isDragging = false; },

        async download() {
            const frame = this.$refs.frameEl;
            const canvas = document.createElement('canvas');
            canvas.width = frame.naturalWidth;
            canvas.height = frame.naturalHeight;
            const ctx = canvas.getContext('2d');

            const displayWidth = this.$refs.stage.clientWidth;
            const multiplier = frame.naturalWidth / displayWidth;

            // 1. Draw User Photo
            const drawW = this.photoImg.naturalWidth * this.scale * multiplier;
            const drawH = this.photoImg.naturalHeight * this.scale * multiplier;
            ctx.drawImage(this.photoImg, this.x * multiplier, this.y * multiplier, drawW, drawH);

            // 2. Draw Frame on top
            const frameClone = new Image();
            frameClone.crossOrigin = "anonymous";
            frameClone.src = this.frameUrl;
            
            frameClone.onload = () => {
                ctx.drawImage(frameClone, 0, 0, canvas.width, canvas.height);
                const link = document.createElement('a');
                link.download = `avatar-${Date.now()}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
            };
        }
    }
}
</script>
</body>
</html>