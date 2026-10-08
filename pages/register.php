<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2.5rem] shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-500">
        <div class="text-center">
            <div class="w-16 h-16 bg-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-indigo-200">
                <i class="fas fa-rocket text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-slate-900">Create Account</h2>
            <p class="mt-2 text-sm font-bold text-slate-400 uppercase tracking-widest">Join the intelligence platform</p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="space-y-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Username</label>
                    <input name="username" type="text" required class="appearance-none relative block w-full px-4 py-4 border border-slate-100 bg-slate-50 placeholder-slate-400 text-slate-900 rounded-2xl font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10 text-sm transition-all" placeholder="Choose a username">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Email Address</label>
                    <input name="email" type="email" required class="appearance-none relative block w-full px-4 py-4 border border-slate-100 bg-slate-50 placeholder-slate-400 text-slate-900 rounded-2xl font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10 text-sm transition-all" placeholder="your@email.com">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-slate-500 uppercase ml-1">Password</label>
                    <input name="password" type="password" required class="appearance-none relative block w-full px-4 py-4 border border-slate-100 bg-slate-50 placeholder-slate-400 text-slate-900 rounded-2xl font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10 text-sm transition-all" placeholder="••••••••">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-lg font-extrabold rounded-2xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-xl shadow-indigo-100 transition-all hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-user-plus text-indigo-500 group-hover:text-indigo-400"></i>
                    </span>
                    Create Account
                </button>
            </div>
        </form>
        
        <div class="text-center mt-6">
            <p class="text-sm font-bold text-slate-500">
                Already have an account? 
                <a href="?page=login" class="text-indigo-600 hover:text-indigo-500">Sign in instead</a>
            </p>
        </div>
    </div>
</div>
