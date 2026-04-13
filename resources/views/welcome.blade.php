<!DOCTYPE html>
<html class="dark" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>ImamKu | Smart Mosque Management System</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
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
        body { font-family: 'Inter', sans-serif; background-color: #0F172A; color: #E5E7EB; }
        .glass-effect { backdrop-filter: blur(20px); background: rgba(31, 41, 55, 0.6); }
        .emerald-glow { box-shadow: 0px 0px 15px rgba(16, 185, 129, 0.3); }
        .emerald-glow:hover { box-shadow: 0px 0px 25px rgba(16, 185, 129, 0.5); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-background text-on-surface selection:bg-primary/20 selection:text-primary">
    <!-- TopNavBar -->
    <header class="w-full top-0 sticky z-50 bg-background/80 backdrop-blur-md border-b border-surface-container-high/50">
        <nav class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <img src="{{ asset('storage/logo/Logo.svg') }}" alt="Logo" class="h-8 w-auto">
                <div class="text-2xl font-bold tracking-tighter text-primary">ImamKu</div>
            </div>
            <div class="hidden md:flex gap-8 items-center font-medium tracking-tight">
                <a class="text-primary border-b-2 border-primary pb-1 transition-colors duration-300" href="#">Home</a>
                <a class="text-on-surface-variant hover:text-primary transition-colors duration-300" href="#">About</a>
                <a class="text-on-surface-variant hover:text-primary transition-colors duration-300" href="#">Donation</a>
            </div>
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('imam.dashboard') }}" class="px-6 py-2 rounded-xl bg-surface-container-high text-primary font-bold active:scale-95 transition-transform border border-outline-variant/30 hover:bg-surface-container-highest">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-2 rounded-xl bg-surface-container-high text-primary font-bold active:scale-95 transition-transform border border-outline-variant/30 hover:bg-surface-container-highest">Login</a>
                @endauth
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="relative min-h-[870px] flex items-center overflow-hidden pt-12 md:pt-0">
        <!-- Abstract Background Glows -->
        <div class="absolute top-1/4 -left-20 w-96 h-96 bg-primary/10 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-1/4 -right-20 w-96 h-96 bg-accent/5 rounded-full blur-[120px]"></div>
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center relative z-10">
            <div class="flex flex-col space-y-8">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-primary/10 border border-primary/20 w-fit">
                    <span class="text-xs font-bold uppercase tracking-widest text-primary">Modern Islamic Infrastructure</span>
                </div>
                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight leading-[1.1] text-on-surface">
                    Smart Mosque <br/>
                    <span class="bg-gradient-to-r from-primary to-emerald-300 bg-clip-text text-transparent">Management System</span>
                </h1>
                <p class="text-lg text-on-surface-variant max-w-lg leading-relaxed font-light">
                    Efficiently manage prayer schedules, imam assignments, and mosque operations with our comprehensive digital platform. Built for the modern sanctuary.
                </p>
                <div class="flex flex-wrap gap-4 pt-4">
                    @auth
                        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('imam.dashboard') }}" class="px-8 py-4 rounded-3xl bg-gradient-to-br from-primary-container to-primary text-on-primary-container font-bold text-lg emerald-glow active:scale-95 transition-transform inline-block">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-8 py-4 rounded-3xl bg-gradient-to-br from-primary-container to-primary text-on-primary-container font-bold text-lg emerald-glow active:scale-95 transition-transform inline-block">
                            Get Started (Login)
                        </a>
                    @endauth
                </div>
                
                <div class="flex items-center gap-6 pt-6">
                    <div class="text-sm text-on-surface-variant flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">verified_user</span>
                        <span class="text-primary font-bold">Trusted</span> by local communities
                    </div>
                </div>
            </div>

            <!-- Dashboard Visualization -->
            <div class="relative lg:h-[600px] flex items-center justify-center">
                <div class="w-full aspect-square md:aspect-video lg:aspect-square bg-surface-container rounded-3xl relative overflow-hidden emerald-glow border border-outline-variant/30 group p-4 flex flex-col gap-4">
                    <!-- Faux App UI -->
                    <div class="flex gap-4">
                        <div class="w-1/3 bg-background rounded-xl p-4 flex flex-col gap-3 opacity-80">
                            <div class="w-8 h-8 rounded-full bg-primary/20 mb-4"></div>
                            <div class="h-2 w-full bg-surface-container-high rounded-full"></div>
                            <div class="h-2 w-3/4 bg-surface-container-high rounded-full"></div>
                            <div class="h-2 w-5/6 bg-surface-container-high rounded-full"></div>
                            <div class="mt-auto h-8 bg-primary/10 rounded-lg border border-primary/20"></div>
                        </div>
                        <div class="w-2/3 flex flex-col gap-4">
                            <div class="h-32 bg-background rounded-xl border border-outline-variant/20 p-4 flex flex-col justify-end">
                                <div class="text-3xl font-black text-primary">Jadwal Imam</div>
                            </div>
                            <div class="flex-1 bg-background rounded-xl border border-outline-variant/20 p-4 grid grid-cols-2 gap-2">
                                <div class="bg-surface-container-high rounded-lg p-2"><div class="h-2 w-1/2 bg-primary mb-2 rounded-full"></div><div class="h-6 bg-surface-container-lowest rounded"></div></div>
                                <div class="bg-surface-container-high rounded-lg p-2"><div class="h-2 w-1/2 bg-error mb-2 rounded-full"></div><div class="h-6 bg-surface-container-lowest rounded"></div></div>
                                <div class="bg-surface-container-high rounded-lg p-2"><div class="h-2 w-1/2 bg-primary mb-2 rounded-full"></div><div class="h-6 bg-surface-container-lowest rounded"></div></div>
                                <div class="bg-surface-container-high rounded-lg p-2"><div class="h-2 w-1/2 bg-primary mb-2 rounded-full"></div><div class="h-6 bg-surface-container-lowest rounded"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bento Grid Features Section -->
    <section class="py-24 px-6 max-w-7xl mx-auto">
        <div class="flex flex-col items-center mb-16 text-center">
            <h2 class="text-3xl font-bold tracking-tight mb-4 text-on-surface">Everything needed for a <span class="text-primary">Digital Sanctuary</span></h2>
            <p class="text-on-surface-variant max-w-xl">Move beyond spreadsheets and manual tracking with modules designed specifically for mosque administrators.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feature Card 1 -->
            <div class="md:col-span-2 bg-surface-container p-8 rounded-3xl flex flex-col justify-between border-b border-r border-outline-variant/30 hover:bg-surface-container-high transition-colors">
                <div>
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-6 shadow-sm border border-primary/20">
                        <span class="material-symbols-outlined text-3xl">groups</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-on-surface">Imam & Congregation Management</h3>
                    <p class="text-on-surface-variant leading-relaxed max-w-md">Coordinate assignments, keep track of congregational data, and facilitate communication within your community effortlessly.</p>
                </div>
            </div>
            <!-- Feature Card 2 -->
            <div class="bg-surface-container p-8 rounded-3xl border border-outline-variant/30 hover:bg-surface-container-high transition-colors">
                <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent mb-6 shadow-sm border border-accent/20">
                    <span class="material-symbols-outlined text-3xl">payments</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-on-surface">Financial Clarity</h3>
                <p class="text-on-surface-variant leading-relaxed text-sm">Complete transparency for donations, expenses, and facility maintenance funding.</p>
            </div>
            <!-- Feature Card 3 -->
            <div class="bg-surface-container p-8 rounded-3xl border border-outline-variant/30 hover:bg-surface-container-high transition-colors">
                <div class="w-12 h-12 rounded-xl bg-emerald-700/20 flex items-center justify-center text-primary mb-6 shadow-sm border border-primary/20">
                    <span class="material-symbols-outlined text-3xl">event</span>
                </div>
                <h3 class="text-2xl font-bold mb-3 text-on-surface">Automated Schedules</h3>
                <p class="text-on-surface-variant leading-relaxed text-sm">Organize schedules and shift swaps with integrated penalty and point monitoring effortlessly.</p>
            </div>
            <!-- Feature Card 4 -->
            <div class="md:col-span-2 bg-surface-container p-8 rounded-3xl flex flex-col md:flex-row gap-8 items-center border border-outline-variant/30 hover:bg-surface-container-high transition-colors">
                <div class="flex-1">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary mb-6 shadow-sm border border-primary/20">
                        <span class="material-symbols-outlined text-3xl">notifications_active</span>
                    </div>
                    <h3 class="text-2xl font-bold mb-3 text-on-surface">Smart Announcements</h3>
                    <p class="text-on-surface-variant leading-relaxed">Broadcast important updates, prayer time changes, and emergency notices to your community instantly.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full bg-background border-t border-outline-variant/30">
        <div class="flex flex-col md:flex-row justify-between items-center px-8 py-10 gap-4 max-w-7xl mx-auto">
            <div class="flex items-center gap-2">
                <img src="{{ asset('storage/logo/Logo.svg') }}" alt="Logo" class="h-6 w-auto grayscale opacity-70">
                <p class="text-xs font-['Inter'] text-on-surface-variant uppercase tracking-widest">© 2024 ImamKu Directive.</p>
            </div>
            <div class="flex gap-8">
                <a class="text-xs font-['Inter'] text-on-surface-variant uppercase tracking-widest hover:text-primary transition-opacity duration-300" href="#">Privacy Policy</a>
                <a class="text-xs font-['Inter'] text-on-surface-variant uppercase tracking-widest hover:text-primary transition-opacity duration-300" href="#">Terms of Service</a>
                <a class="text-xs font-['Inter'] text-on-surface-variant uppercase tracking-widest hover:text-primary transition-opacity duration-300" href="#">Support</a>
            </div>
        </div>
    </footer>
</body>
</html>
