<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login - ImamKu</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: { colors: { 
                "primary": "#10B981",
                "primary-container": "#0d9467",
                "on-primary-container": "#d1fae5",
                "secondary": "#1F2937",
                "accent": "#F59E0B",
                "background": "#0F172A",
                "surface": "#111827",
                "surface-container": "#1F2937",
                "surface-container-low": "#111827",
                "surface-container-high": "#374151",
                "surface-container-highest": "#4B5563",
                "surface-container-lowest": "#060e20",
                "on-surface": "#E5E7EB",
                "on-surface-variant": "#9CA3AF",
                "outline-variant": "#4B5563",
                "error": "#EF4444",
                "tertiary": "#F59E0B"
            }, fontFamily: { "headline": ["Inter"], "body": ["Inter"], "label": ["Inter"] } } }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .glow-primary { box-shadow: 0px 0px 15px rgba(16, 185, 129, 0.3); }
        .glow-primary:hover { box-shadow: 0px 0px 25px rgba(16, 185, 129, 0.5); }
        .glass-overlay { background: rgba(17, 24, 39, 0.8); backdrop-filter: blur(20px); }
        /* Task 6 constraints locally forced just in case */
        input { background-color: #111827 !important; color: #E5E7EB !important; border: 1px solid #374151 !important; }
        input:focus { border-color: #10B981 !important; box-shadow: 0 0 0 1px #10B981 !important; }
    </style>
</head>
<body class="bg-background text-on-surface font-body min-h-screen flex flex-col justify-center items-center relative overflow-hidden">
    
    <!-- Ambient Decorative Elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-accent/5 rounded-full blur-[120px] pointer-events-none"></div>
    
    <main class="w-full max-w-md px-6 py-12 z-10 hidden-on-submit">
        <!-- Brand Identity -->
        <div class="flex flex-col items-center mb-10">
            <div class="w-16 h-16 rounded-2xl bg-surface-container flex items-center justify-center mb-6 shadow-2xl border border-outline-variant/30">
                <img src="{{ asset('storage/logo/Logo.svg') }}" alt="Logo" class="h-10 w-auto" />
            </div>
            <h1 class="text-3xl font-extrabold tracking-tighter text-primary">ImamKu</h1>
            <p class="text-on-surface-variant text-sm mt-2 font-medium uppercase tracking-[0.05em]">The Digital Sanctuary</p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-surface glass-overlay p-8 md:p-10 rounded-2xl shadow-[0px_12px_32px_rgba(0,0,0,0.5)] relative overflow-hidden border border-outline-variant/50">
            <header class="relative z-10 mb-8">
                <h2 class="text-xl font-bold text-on-surface tracking-tight">Welcome Back</h2>
                <p class="text-on-surface-variant text-sm mt-1">Please enter your credentials to continue</p>
            </header>
            
            @if(session('status'))
                <div class="mb-4 p-3 bg-primary/10 border border-primary/30 rounded-xl text-primary text-sm font-medium">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-error/10 border border-error/30 rounded-xl text-error text-sm font-bold">
                    @foreach($errors->all() as $error)
                        <div class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px]">error</span>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10" id="loginForm">
                @csrf
                
                <!-- Email Input -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest block" for="email">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline-variant text-lg">mail</span>
                        </div>
                        <input class="w-full rounded-xl py-4 pl-12 pr-4 transition-all duration-300" id="email" name="email" value="{{ old('email') }}" type="email" required autofocus autocomplete="username" placeholder="nama@email.com"/>
                    </div>
                </div>
                
                <!-- Password Input -->
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-widest block" for="password">Password</label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-outline-variant text-lg">lock</span>
                        </div>
                        <input class="w-full rounded-xl py-4 pl-12 pr-12 transition-all duration-300" id="password" name="password" type="password" required autocomplete="current-password" placeholder="••••••••"/>
                    </div>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center gap-3">
                    <input class="w-5 h-5 rounded border-none bg-surface-container-highest text-primary focus:ring-primary focus:ring-offset-0" name="remember" id="remember" type="checkbox"/>
                    <label class="text-sm text-on-surface-variant cursor-pointer select-none" for="remember">Remember this device</label>
                </div>
                
                <!-- Login Action -->
                <button class="w-full bg-gradient-to-br from-primary-container to-primary py-4 rounded-xl text-on-primary-container font-bold tracking-tight glow-primary transition-all active:scale-95 border border-primary/50" type="submit">
                    Login to Dashboard
                </button>
            </form>
        </div>

        <div class="mt-12 flex justify-center items-center">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Server Secure</span>
            </div>
        </div>
    </main>

    <!-- LOADING OVERLAY (Hidden by default) -->
    <div id="loadingOverlay" style="display:none;" class="flex-col justify-center items-center fixed inset-0 bg-background/90 backdrop-blur-sm z-50 transition-opacity">
        <div class="relative flex items-center justify-center mb-6">
            <div class="absolute w-24 h-24 border-4 border-primary/20 rounded-full"></div>
            <div class="absolute w-24 h-24 border-4 border-primary rounded-full border-t-transparent animate-spin"></div>
            <span class="material-symbols-outlined text-primary text-3xl">verified_user</span>
        </div>
        <h2 class="text-xl font-bold text-on-surface mb-2">Authenticating...</h2>
        <p class="text-primary font-medium tracking-wider text-sm animate-pulse">Mengalihkan ke dashboard...</p>
    </div>

    <!-- Toggle logic for the loader -->
    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            document.querySelectorAll('.hidden-on-submit').forEach(el => el.style.opacity = '0.3');
            document.getElementById('loadingOverlay').style.display = 'flex';
        });
    </script>
</body>
</html>
