<section wire:poll.30s class="hudpanel overflow-hidden">
    @include('partials.quest-styles')

    <div class="hud-head">
        <span class="hud-head__bar"></span>
        <span class="hud-head__title">Ежедневные задания</span>
        <span class="hud-head__code">{{ $completed }}/{{ $total }} ВЫПОЛНЕНО</span>
        <button onclick="Livewire.dispatch('open-quests')"
            class="ml-3 hud-mono text-[11px] font-bold uppercase tracking-widest t-acc hover:opacity-80 transition">
            Подробнее →
        </button>
    </div>

    {{-- Completion meter + refresh countdown --}}
    <div class="px-5 lg:px-6 pt-4 flex items-center gap-3"
         x-data="{ left: {{ $resetIn }}, fmt() { const h=Math.floor(this.left/3600), m=Math.floor(this.left%3600/60), s=Math.floor(this.left%60); return String(h).padStart(2,'0')+':'+String(m).padStart(2,'0')+':'+String(s).padStart(2,'0'); } }"
         x-init="setInterval(() => { if (left > 0) left--; }, 1000)">
        <div class="hud-meter flex-1"><span style="width:{{ $total > 0 ? round($completed / $total * 100) : 0 }}%"></span></div>
        <span class="qst-refresh whitespace-nowrap">обновление через <span x-text="fmt()" class="t-dim font-bold"></span></span>
    </div>

    <div class="p-5 lg:p-6 flex flex-col gap-3">
        @forelse($quests as $q)
            <div wire:key="quest-{{ $q->id }}">
                @include('partials.quest-card', ['q' => $q])
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <p class="text-sm font-bold t-dim uppercase tracking-widest">Заданий пока нет</p>
                <p class="text-xs t-dim2 mt-1.5">Загляните завтра за новой тройкой</p>
            </div>
        @endforelse
    </div>
</section>
