<div 
    x-data="{
        state: $wire.$entangle('data'),
        isDragging: false,
        dragStartX: 0,
        dragStartY: 0,
        frameStartX: 0,
        frameStartY: 0,
        
        getImageUrl() {
            let rawPath = this.state.poster_path;
            if (!rawPath) return null;
            if (typeof rawPath === 'object' && !Array.isArray(rawPath)) {
                rawPath = Object.values(rawPath)[0];
            }
            if (typeof rawPath !== 'string' || !rawPath) return null;
            if (rawPath.startsWith('http')) return rawPath;
            if (rawPath.includes('livewire-file')) return '/livewire/preview-file/' + rawPath;
            return window.location.origin + '/storage/' + rawPath.replace(/^\/+/, '');
        },
        
        startDrag(e) {
            this.isDragging = true;
            this.dragStartX = e.clientX;
            this.dragStartY = e.clientY;
            this.frameStartX = Number(this.state.frame_x) || 0;
            this.frameStartY = Number(this.state.frame_y) || 0;
            e.preventDefault();
            e.stopPropagation();
        },
        
        onDrag(e) {
            if (!this.isDragging) return;
            this.state.frame_x = Math.round(this.frameStartX + (e.clientX - this.dragStartX));
            this.state.frame_y = Math.round(this.frameStartY + (e.clientY - this.dragStartY));
        },
        
        stopDrag() { this.isDragging = false; },
        
        setPosition(e) {
            if (this.isDragging) return;
            const rect = e.currentTarget.getBoundingClientRect();
            const size = Number(this.state.frame_size) || 100;
            this.state.frame_x = Math.round(e.clientX - rect.left - (size / 2));
            this.state.frame_y = Math.round(e.clientY - rect.top - (size / 2));
        }
    }" 
    x-on:mousemove.window="onDrag($event)"
    x-on:mouseup.window="stopDrag()"
    class="relative w-full border rounded-xl overflow-hidden bg-gray-200 p-8 min-h-[600px] select-none flex items-start justify-center"
>
    {{-- The Stage: Using Grid to stack elements --}}
    <div x-show="getImageUrl()" class="grid" style="grid-template-columns: 1fr; grid-template-rows: 1fr;">
        
        {{-- Layer 1: The Image --}}
        <img 
            :src="getImageUrl()" 
            x-on:click="setPosition($event)"
            class="shadow-2xl border border-white rounded-lg max-w-full h-auto"
            style="grid-area: 1 / 1 / 2 / 2; z-index: 10;"
            draggable="false"
        />
        
        {{-- Layer 2: The Interactive Overlay --}}
        {{-- We wrap it in a container that matches the image size exactly --}}
        <div class="relative w-full h-full pointer-events-none" style="grid-area: 1 / 1 / 2 / 2; z-index: 20;">
            <div 
                x-on:mousedown="startDrag($event)"
                class="absolute border-4 border-blue-500 bg-blue-500/10 pointer-events-auto shadow-inner"
                :class="isDragging ? 'cursor-grabbing border-blue-300' : 'cursor-grab hover:border-blue-400'"
                :style="`
                    left: ${state.frame_x || 0}px;
                    top: ${state.frame_y || 0}px;
                    width: ${state.frame_size || 100}px;
                    height: ${state.frame_size || 100}px;
                    border-radius: ${state.frame_shape === 'circle' ? '50%' : '0px'};
                    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6);
                `"
            >
                {{-- Center Indicator --}}
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-10 h-10 border border-white/30 rounded-full flex items-center justify-center bg-black/20">
                        <span class="text-white text-xs font-bold">DRAG</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- HUD --}}
    <div x-show="getImageUrl()" class="absolute bottom-4 right-4 z-50 bg-black/80 text-white p-3 rounded-lg text-[10px] font-mono border border-white/10">
        X: <span x-text="state.frame_x"></span> | Y: <span x-text="state.frame_y"></span>
    </div>
</div>