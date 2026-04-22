<header class="sticky top-0 z-[50] flex justify-between items-center px-4 lg:px-8 h-16 bg-[#0b1326]/80 backdrop-blur-md border-b border-outline-variant/10 shadow-sm w-full">
    <div class="flex items-center gap-4 flex-1">
        <!-- Search bar removed per Phase 3 instructions -->
    </div>
    
    <div class="flex items-center gap-4 lg:gap-6">
        <div class="flex items-center gap-3 px-2 rounded-xl">
            <div class="text-right hidden sm:block">
                <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface">{{ auth()->user()->name }}</p>
                <p class="text-[9px] text-primary uppercase font-bold tracking-tighter">{{ auth()->user()->isAdmin() ? 'System Admin' : 'Imam' }}</p>
            </div>
            <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center font-bold text-on-surface text-xs">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
        </div>
    </div>
</header>
