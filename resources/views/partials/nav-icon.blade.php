<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
    @switch($icon)
        @case('dashboard')
        @case('overview')
            <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
            <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
            <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
            <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
            @break
        @case('profile')
        @case('users')
            <circle cx="9" cy="8" r="3.2" stroke="currentColor" stroke-width="1.7"/>
            <path d="M3.8 19c.4-3 2.3-4.7 5.2-4.7s4.8 1.7 5.2 4.7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="M15.2 5.2a3.2 3.2 0 0 1 0 6.1M16 14.5c2.2.5 3.5 2 3.9 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('risk')
            <path d="M12 3.5 21 19a1.2 1.2 0 0 1-1 1.8H4A1.2 1.2 0 0 1 3 19l9-15.5a1.2 1.2 0 0 1 2 0Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M12 9v4.5M12 17h.01" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
            @break
        @case('simulator')
            <path d="M4 17a9 9 0 1 1 16 0" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            <path d="m12 13 4-4M6.5 17h11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="12" cy="13" r="1.3" fill="currentColor"/>
            @break
        @case('comparison')
            <path d="M4 8h15m0 0-3.5-3.5M19 8l-3.5 3.5M20 16H5m0 0 3.5-3.5M5 16l3.5 3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('categories')
        @case('products')
            <rect x="4" y="4" width="16" height="16" rx="2" stroke="currentColor" stroke-width="1.7"/>
            <path d="M4 10h16M10 4v16" stroke="currentColor" stroke-width="1.7"/>
            @break
        @case('policies')
        @case('documents')
            <path d="M6 3.8h8l4 4V20H6z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M14 3.8v4h4M9 12h6M9 15.5h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            @break
        @case('chat')
            <path d="M20 11.5a7.5 7.5 0 0 1-7.5 7.5H6l-2.5 1v-5A7.5 7.5 0 1 1 20 11.5Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M8 11h8M8 14h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('quality')
            <path d="m12 3 1.9 5.8L20 11l-6.1 2.2L12 19l-1.9-5.8L4 11l6.1-2.2L12 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="m18.5 15 .8 2.2 2.2.8-2.2.8-.8 2.2-.8-2.2-2.2-.8 2.2-.8.8-2.2Z" fill="currentColor"/>
            @break
        @case('audit')
            <path d="M8 4.5h8M9 3h6v3H9zM6 4.5H5v16h14v-16h-1" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
            <path d="M8.5 11h7M8.5 15h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
        @case('user-dashboard')
            <rect x="3.5" y="4" width="17" height="16" rx="2" stroke="currentColor" stroke-width="1.7"/>
            <path d="M3.5 9h17M8 9v11M6 6.5h.01M8.5 6.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
            @break
    @endswitch
</svg>
