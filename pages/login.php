<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2.5rem] shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-500">
        <div class="text-center">
            <div class="w-16 h-16 bg-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-200">
                <i class="fas fa-link text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900">Welcome Back</h2>
            <p class="mt-2 text-sm font-bold text-slate-400 uppercase tracking-widest">Access your power panel</p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Username</label>
                    <input name="username" type="text" required class="appearance-none relative block w-full px-4 py-4 border border-slate-100 bg-slate-50 placeholder-slate-400 text-slate-900 rounded-2xl font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10 text-sm transition-all" placeholder="Enter your username">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Password</label>
                    <input name="password" type="password" required class="appearance-none relative block w-full px-4 py-4 border border-slate-100 bg-slate-50 placeholder-slate-400 text-slate-900 rounded-2xl font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10 text-sm transition-all" placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded-lg">
                    <label for="remember-me" class="ml-2 block text-sm font-bold text-slate-500">Remember me</label>
                </div>
                <div class="text-sm">
                    <a href="#" class="font-bold text-indigo-600 hover:text-indigo-500">Forgot password?</a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-lg font-extrabold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-xl shadow-indigo-100 transition-all hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-lock text-indigo-500 group-hover:text-indigo-400"></i>
                    </span>
                    Sign In
                </button>
            </div>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm font-bold text-slate-500">
                New to ProLink? 
                <a href="?page=register" class="text-indigo-600 hover:text-indigo-500">Create an account</a>
            </p>
        </div>
    </div>
</div>
