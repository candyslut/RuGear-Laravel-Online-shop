@php
    $typeMeta = [
        'achievement' => [
            // Через var: тема «Космос» подменяет тёплый акцент на фиолетовый.
            'color' => 'var(--warm-acc, #fb923c)',
            'bg'    => 'var(--warm-acc-bg, rgba(249,115,22,.15))',
            'svg'   => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
        ],
        'level_up' => [
            'color' => '#22d3ee',
            'bg'    => 'rgba(6,182,212,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>',
        ],
        'ticket_status' => [
            'color' => '#c084fc',
            'bg'    => 'rgba(168,85,247,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>',
        ],
        'order_status' => [
            'color' => '#4ade80',
            'bg'    => 'rgba(34,197,94,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path stroke-linecap="round" stroke-linejoin="round" d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>',
        ],
        'comment_reply' => [
            'color' => '#f472b6',
            'bg'    => 'rgba(236,72,153,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17l-5-5 5-5M4 12h11a4 4 0 0 1 4 4v2"/></svg>',
        ],
        'comment_like' => [
            'color' => '#f43f5e',
            'bg'    => 'rgba(244,63,94,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.23l-8.84-8.84a5.5 5.5 0 117.78-7.78L12 5.67l1.06-1.06a5.5 5.5 0 117.78 7.78z"/></svg>',
        ],
        'streak' => [
            'color' => 'var(--warm-acc, #fb923c)',
            'bg'    => 'var(--warm-acc-bg, rgba(249,115,22,.15))',
            'svg'   => '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.5 2c.4 2.8 2.3 4.4 3.6 6 1.2 1.5 1.9 3 1.9 4.8a6 6 0 11-12 0c0-1.6.6-3 1.6-4.2.2.9.9 1.5 1.8 1.5 1.2 0 1.8-1 1.3-2.5C11.8 5.4 12 3.6 12.5 2z"/></svg>',
        ],
        'rank_up' => [
            'color' => '#fbbf24',
            'bg'    => 'rgba(251,191,36,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M12 2 4 5v6c0 5 3.4 7.7 8 9 4.6-1.3 8-4 8-9V5l-8-3z"/><polyline points="8.5 11.5 11 14 15.5 9.5"/></svg>',
        ],
        'quest' => [
            'color' => '#34d399',
            'bg'    => 'rgba(16,185,129,.15)',
            'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg>',
        ],
    ];
    $defaultMeta = [
        'color' => 'var(--text-secondary)',
        'bg'    => 'var(--bg-tertiary)',
        'svg'   => '<svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1"/></svg>',
    ];
@endphp

<div
    class="relative"
    x-data="{ open: false }"
    @keydown.escape.window="open = false"
    wire:poll.60s="refresh"
>
    {{-- Bell button --}}
    <button
        type="button"
        @click="open = !open; if (open) $wire.markAllRead()"
        class="relative w-9 h-9 rounded-lg flex items-center justify-center border border-[var(--border-color)] bg-[var(--bg-tertiary)] text-[var(--text-secondary)] hover:text-orange-500 hover:border-orange-500/40 transition-colors"
        aria-label="Уведомления"
        title="Уведомления"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1"/>
        </svg>
        @if($unreadCount > 0)
            <span data-bell-badge class="absolute -top-1.5 -right-1.5 flex items-center justify-center">
                <span class="absolute inline-flex h-full w-full rounded-full bg-orange-500 opacity-60 animate-ping"></span>
                <span class="relative min-w-[18px] h-[18px] px-1 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center leading-none ring-2 ring-[var(--bg-secondary)]">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-cloak
        x-transition.opacity
        @click.outside="open = false"
        class="fixed sm:absolute top-20 sm:top-auto left-3 right-3 sm:left-auto sm:right-0 sm:mt-3 w-auto sm:w-96 max-w-[calc(100vw-1.5rem)] bg-[var(--bg-primary)] border border-[var(--border-color)] rounded-xl shadow-2xl overflow-hidden z-50"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-[var(--border-color)] bg-[var(--bg-tertiary)]">
            <p class="text-sm font-black uppercase tracking-wider text-[var(--text-primary)]">Уведомления</p>
            @if(count($notifications))
                <button
                    type="button"
                    wire:click="markAllRead"
                    class="text-[11px] font-mono uppercase tracking-wider text-[var(--text-tertiary)] hover:text-orange-500 transition-colors"
                >Прочитать все</button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-96 overflow-y-auto divide-y divide-[var(--border-color)]">
            @forelse($notifications as $n)
                @php $m = $typeMeta[$n['type']] ?? $defaultMeta; @endphp
                <button
                    type="button"
                    wire:key="notif-{{ $n['id'] }}"
                    wire:click="openNotification({{ $n['id'] }})"
                    @click="open = false"
                    class="w-full text-left flex gap-3 items-start px-4 py-3 hover:bg-[var(--bg-tertiary)] transition-colors {{ is_null($n['read_at']) ? 'bg-orange-500/[0.04]' : '' }}"
                >
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                          style="background: {{ $m['bg'] }}; color: {{ $m['color'] }};">
                        {!! $m['svg'] !!}
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wide truncate" style="color: {{ $m['color'] }};">{{ $n['title'] }}</span>
                            @if(is_null($n['read_at']))
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 flex-shrink-0"></span>
                            @endif
                        </span>
                        @if(!empty($n['message']))
                            <span class="block text-sm text-[var(--text-secondary)] mt-0.5 leading-snug">{{ $n['message'] }}</span>
                        @endif
                        <span class="block text-[10px] text-[var(--text-tertiary)] font-mono mt-1">
                            {{ \Carbon\Carbon::parse($n['created_at'])->diffForHumans() }}
                        </span>
                    </span>
                </button>
            @empty
                <div class="px-4 py-10 text-center text-[var(--text-tertiary)]">
                    <svg class="w-8 h-8 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0v1a3 3 0 0 1-6 0v-1"/>
                    </svg>
                    <p class="text-xs font-mono uppercase tracking-wider">Пока нет уведомлений</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
