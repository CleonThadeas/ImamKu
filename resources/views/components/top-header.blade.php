<header class="sticky top-0 z-[50] flex justify-between items-center px-4 lg:px-8 h-16 bg-[#0b1326]/80 backdrop-blur-md border-b border-outline-variant/10 shadow-sm w-full">
    <div class="flex items-center gap-4 flex-1">
        <!-- Search bar removed per Phase 3 instructions -->
    </div>
    
    <div class="flex items-center gap-4 lg:gap-6">
        <div class="flex items-center gap-2 lg:gap-4">
            <div class="relative p-2 text-on-surface-variant hover:text-primary transition-colors cursor-pointer group">
                <a href="{{ auth()->user()->isAdmin() ? route('admin.notification-logs.index') : route('imam.notifications.index') }}" class="flex items-center">
                    <span class="material-symbols-outlined">notifications</span>
                    @if(auth()->user()->unreadNotifications && auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full ring-2 ring-surface animate-pulse"></span>
                    @endif
                </a>
            </div>
        </div>
        
        <div class="h-8 w-[1px] bg-outline-variant/20 hidden md:block"></div>
        
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 cursor-pointer group px-2 rounded-xl hover:bg-surface-container-low transition-colors">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface">{{ auth()->user()->name }}</p>
                <p class="text-[9px] text-primary uppercase font-bold tracking-tighter">{{ auth()->user()->isAdmin() ? 'System Admin' : 'Imam' }}</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center font-bold text-on-surface ring-2 ring-transparent group-hover:ring-primary/40 transition-all text-xs">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </a>
    </div>
</header>
