<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Your Avatar</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hidden { display: none; }
        #canvas-container { position: relative; max-width: 100%; margin: 0 auto; }
        #photo-placeholder {
            position: absolute;
            border: 3px dashed #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            cursor: pointer;
            transition: all 0.3s;
        }
        #photo-placeholder:hover { background: rgba(59, 130, 246, 0.2); border-color: #2563eb; }
        #photo-placeholder.circle { border-radius: 50%; }
        #final-canvas { max-width: 100%; height: auto; border-radius: 1rem; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-900 to-slate-800 min-h-screen text-white">

    <div class="container mx-auto px-4 py-12 max-w-4xl">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold mb-2">Create Your Avatar</h1>
            <p class="text-slate-400">Upload or capture your photo to create a personalized poster</p>
        </div>

        <!-- Step 1: Photo Upload/Capture -->
        <div id="step-upload" class="bg-slate-800/50 backdrop-blur rounded-2xl p-8 border border-slate-700">
            <div id="canvas-container">
                <canvas id="poster-canvas"></canvas>
                <div id="photo-placeholder">
                    <div class="flex flex-col items-center justify-center h-full text-blue-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <p class="text-sm font-medium">Add Photo</p>
                    </div>
                </div>
            </div>

            <!-- Upload Options (Hidden by default) -->
            <div id="upload-options" class="hidden mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="bg-blue-600 hover:bg-blue-500 px-6 py-4 rounded-xl cursor-pointer text-center font-medium transition">
                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Upload from Device
                    <input type="file" id="file-input" accept="image/*" class="hidden">
                </label>

                <button onclick="openCamera()" class="bg-purple-600 hover:bg-purple-500 px-6 py-4 rounded-xl font-medium transition">
                    <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Take Photo
                </button>
            </div>

            <!-- Generate Button (Hidden until photo added) -->
            <div id="generate-section" class="hidden mt-6 text-center">
                <button onclick="generateFinal()" class="bg-green-600 hover:bg-green-500 px-8 py-4 rounded-xl font-bold text-lg transition">
                    🎨 Generate Avatar
                </button>
            </div>
        </div>

        <!-- Camera Modal -->
        <div id="camera-modal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
            <div class="bg-slate-800 rounded-2xl p-6 max-w-2xl w-full">
                <video id="camera-video" autoplay class="w-full rounded-lg mb-4"></video>
                <div class="flex gap-4">
                    <button onclick="capturePhoto()" class="flex-1 bg-blue-600 hover:bg-blue-500 px-6 py-3 rounded-xl font-medium">
                        📸 Capture
                    </button>
                    <button onclick="closeCamera()" class="flex-1 bg-slate-600 hover:bg-slate-500 px-6 py-3 rounded-xl font-medium">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Final Result -->
        <div id="step-result" class="hidden bg-slate-800/50 backdrop-blur rounded-2xl p-8 border border-slate-700 mt-8">
            <h2 class="text-2xl font-bold text-center mb-6">🎉 Your Avatar is Ready!</h2>
            <canvas id="final-canvas" class="mx-auto mb-6"></canvas>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <a id="download-link" download="my-avatar.png" class="bg-green-600 hover:bg-green-500 px-8 py-4 rounded-xl font-bold text-center transition">
                    ⬇️ Download Avatar
                </a>
                <button onclick="reset()" class="bg-slate-600 hover:bg-slate-500 px-8 py-4 rounded-xl font-bold transition">
                    🔄 Create Another
                </button>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('poster-canvas');
        const ctx = canvas.getContext('2d');
        const placeholder = document.getElementById('photo-placeholder');
        const fileInput = document.getElementById('file-input');
        const uploadOptions = document.getElementById('upload-options');
        const generateSection = document.getElementById('generate-section');
        const finalCanvas = document.getElementById('final-canvas');
        const finalCtx = finalCanvas.getContext('2d');

        let templateData = null;
        let userPhoto = null;
        let posterImage = null;
        let cameraStream = null;

        // Fetch template data
        async function loadTemplate() {
            const response = await fetch('/avatar/template');
            templateData = await response.json();

            // Load poster image
            posterImage = new Image();
            posterImage.crossOrigin = 'anonymous';
            posterImage.onload = () => {
                canvas.width = posterImage.width;
                canvas.height = posterImage.height;
                ctx.drawImage(posterImage, 0, 0);

                // Position placeholder
                placeholder.style.left = templateData.frame_x + 'px';
                placeholder.style.top = templateData.frame_y + 'px';
                placeholder.style.width = templateData.frame_size + 'px';
                placeholder.style.height = templateData.frame_size + 'px';
                
                if (templateData.frame_shape === 'circle') {
                    placeholder.classList.add('circle');
                }
            };
            posterImage.src = templateData.poster_url;
        }

        // Show upload options when placeholder clicked
        placeholder.addEventListener('click', () => {
            uploadOptions.classList.remove('hidden');
        });

        // Handle file upload
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    addPhotoToCanvas(event.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Add photo to canvas
        function addPhotoToCanvas(imageSrc) {
            userPhoto = new Image();
            userPhoto.onload = () => {
                // Redraw poster
                ctx.drawImage(posterImage, 0, 0);

                // Draw user photo in frame
                ctx.save();
                
                if (templateData.frame_shape === 'circle') {
                    // Circular clipping path
                    ctx.beginPath();
                    ctx.arc(
                        templateData.frame_x + templateData.frame_size / 2,
                        templateData.frame_y + templateData.frame_size / 2,
                        templateData.frame_size / 2,
                        0,
                        Math.PI * 2
                    );
                    ctx.clip();
                } else {
                    // Square clipping path
                    ctx.rect(
                        templateData.frame_x,
                        templateData.frame_y,
                        templateData.frame_size,
                        templateData.frame_size
                    );
                    ctx.clip();
                }

                // Draw photo (cover the frame area)
                const aspectRatio = userPhoto.width / userPhoto.height;
                let drawWidth, drawHeight, offsetX, offsetY;

                if (aspectRatio > 1) {
                    drawHeight = templateData.frame_size;
                    drawWidth = drawHeight * aspectRatio;
                    offsetX = templateData.frame_x - (drawWidth - templateData.frame_size) / 2;
                    offsetY = templateData.frame_y;
                } else {
                    drawWidth = templateData.frame_size;
                    drawHeight = drawWidth / aspectRatio;
                    offsetX = templateData.frame_x;
                    offsetY = templateData.frame_y - (drawHeight - templateData.frame_size) / 2;
                }

                ctx.drawImage(userPhoto, offsetX, offsetY, drawWidth, drawHeight);
                ctx.restore();

                // Hide placeholder, show generate button
                placeholder.style.display = 'none';
                uploadOptions.classList.add('hidden');
                generateSection.classList.remove('hidden');
            };
            userPhoto.src = imageSrc;
        }

        // Camera functions
        async function openCamera() {
            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
                const video = document.getElementById('camera-video');
                video.srcObject = cameraStream;
                document.getElementById('camera-modal').classList.remove('hidden');
            } catch (err) {
                alert('Camera access denied or unavailable');
            }
        }

        function capturePhoto() {
            const video = document.getElementById('camera-video');
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = video.videoWidth;
            tempCanvas.height = video.videoHeight;
            tempCanvas.getContext('2d').drawImage(video, 0, 0);
            
            const imageSrc = tempCanvas.toDataURL('image/png');
            addPhotoToCanvas(imageSrc);
            closeCamera();
        }

        function closeCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
            }
            document.getElementById('camera-modal').classList.add('hidden');
        }

        // Generate final image
        function generateFinal() {
            finalCanvas.width = canvas.width;
            finalCanvas.height = canvas.height;
            finalCtx.drawImage(canvas, 0, 0);

            const dataURL = finalCanvas.toDataURL('image/png');
            document.getElementById('download-link').href = dataURL;

            document.getElementById('step-upload').classList.add('hidden');
            document.getElementById('step-result').classList.remove('hidden');
        }

        // Reset
        function reset() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(posterImage, 0, 0);
            
            placeholder.style.display = 'flex';
            uploadOptions.classList.add('hidden');
            generateSection.classList.add('hidden');
            
            document.getElementById('step-upload').classList.remove('hidden');
            document.getElementById('step-result').classList.add('hidden');
            
            fileInput.value = '';
        }

        // Initialize
        loadTemplate();
    </script>
</body>
</html>