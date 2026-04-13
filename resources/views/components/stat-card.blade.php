@props(['title', 'value', 'icon', 'color' => 'primary', 'desc' => null])
<div class="col-span-1 p-6 rounded-3xl bg-surface-container border-none shadow-sm relative overflow-hidden group hover:bg-surface-container-high transition-all duration-300">
    <div class="absolute -right-4 -top-4 w-24 h-24 bg-[var(--color-opacity)] rounded-full blur-2xl transition-colors" style="--color-opacity: rgba(var(--color-{{ $color }}), 0.05)"></div>
    <div class="flex justify-between items-start relative">
        <div class="p-3 rounded-xl" style="background: rgba(16,185,129,0.1)">
            <span class="material-symbols-outlined text-{{ $color }}" style="font-variation-settings: 'FILL' 1;">{{ $icon }}</span>
        </div>
        @if(isset($topRight))
            {{ $topRight }}
        @endif
    </div>
    <div class="mt-6 relative">
        <p class="text-[10px] font-bold uppercase tracking-widest text-on-surface-variant mb-1">{{ $title }}</p>
        <h3 class="text-4xl font-black text-on-surface tracking-tighter">{{ $value }}</h3>
        @if($desc)
        <div class="mt-4 flex items-center gap-1 text-[11px] font-semibold text-on-surface-variant">
            {!! $desc !!}
        </div>
        @endif
    </div>
</div>
