@php
    $admin = Auth::guard('admin')->user();
    $isCabang = $admin->role !== 'admin';
    $current = Route::currentRouteName();

    // Standalone menu (always visible)
    $standaloneMenu = [
        ['route' => 'dashboard', 'icon' => 'icon/ic_dashboard.svg', 'label' => 'Dashboard'],
        [
            'route' => 'dashboard.transaksi',
            'icon' => 'icon/ic_sampah.svg',
            'label' => 'Setoran',
            'subroutes' => ['dashboard.transaksi', 'dashboard.transaksi.show'],
        ],
    ];

    // Hierarchical menus with parent groups
    $hierarchicalMenu = [
        [
            'label' => 'Master Data',
            'icon' => 'icon/ic_category.svg',
            'children' => array_filter([
                !$isCabang
                    ? [
                        'route' => 'dashboard.category',
                        'label' => 'Kategori',
                        'subroutes' => [
                            'dashboard.category',
                            'dashboard.category.create',
                            'dashboard.category.edit',
                            'dashboard.category.show',
                        ],
                    ]
                    : null,
                [
                    'route' => 'dashboard.sampah',
                    'label' => 'Sampah',
                    'subroutes' => [
                        'dashboard.sampah',
                        'dashboard.sampah.show',
                        'dashboard.sampah.edit',
                        'dashboard.sampah.create',
                    ],
                ],
                !$isCabang
                    ? [
                        'route' => 'dashboard.redeem-item.index',
                        'label' => 'Item Redeem',
                        'subroutes' => [
                            'dashboard.redeem-item.index',
                            'dashboard.redeem-item.create',
                            'dashboard.redeem-item.edit',
                        ],
                    ]
                    : null,
                !$isCabang
                    ? [
                        'route' => 'offtakers.index',
                        'label' => 'Offtaker',
                        'subroutes' => ['offtakers.index', 'offtakers.create', 'offtakers.edit', 'offtakers.show'],
                    ]
                    : null,
            ]),
        ],
    ];

    // Admin-only standalone menus
    $adminOnlyMenu = [
        [
            'label' => 'Transaksi',
            'icon' => 'icon/ic_laporan.svg',
            'children' => [
                [
                    'route' => 'waste-transactions.sales.index',
                    'label' => 'Penjualan & Pengolahan',
                    'subroutes' => [
                        'waste-transactions.sales.index',
                        'waste-transactions.show',
                        'waste-transactions.sales.create',
                        'waste-transactions.processing.index',
                        'waste-transactions.processing.create',
                    ],
                ],
                [
                    'route' => 'waste-inventory.index',
                    'label' => 'Inventori Sampah',
                    'subroutes' => ['waste-inventory.index', 'waste-inventory.show'],
                ],
            ],
        ],
        [
            'label' => 'Laporan',
            'icon' => 'icon/ic_poin.svg',
            'children' => [
                ['route' => 'reports.laba-rugi', 'label' => 'Laba Rugi', 'subroutes' => ['reports.laba-rugi']],
                ['route' => 'reports.penjualan', 'label' => 'Penjualan & Pengolahan', 'subroutes' => ['reports.penjualan']],
                // Pengolahan merged into Penjualan
            ],
        ],
        [
            'route' => 'dashboard.user',
            'icon' => 'icon/ic_pelanggan.svg',
            'label' => 'User',
            'subroutes' => ['dashboard.user', 'dashboard.user.edit', 'dashboard.user.show'],
        ],
        [
            'route' => 'dashboard.bank',
            'icon' => 'icon/ic_banksampah.svg',
            'label' => 'Bank Sampah',
            'subroutes' => ['dashboard.bank', 'dashboard.bank.show', 'dashboard.bank.edit', 'dashboard.bank.create'],
        ],
        [
            'route' => 'dashboard.event',
            'icon' => 'icon/ic_program.svg',
            'label' => 'Event',
            'subroutes' => [
                'dashboard.event',
                'dashboard.event.show',
                'dashboard.event.create',
                'dashboard.event.edit',
            ],
        ],
        // [
        //     'route' => 'dashboard.poin',
        //     'icon' => 'icon/ic_poin.svg',
        //     'label' => 'Poin',
        //     'subroutes' => ['dashboard.poin', 'dashboard.poin.create'],
        // ],
        [
            'route' => 'dashboard.user-redeem.index',
            'icon' => 'icon/ic_poin.svg',
            'label' => 'User Redeem',
            'subroutes' => ['dashboard.user-redeem.index', 'dashboard.user-redeem.edit'],
        ],
        [
            'route' => 'dashboard.artikel',
            'icon' => 'icon/ic_artikel.svg',
            'label' => 'Artikel',
            'subroutes' => [
                'dashboard.artikel',
                'dashboard.artikel.create',
                'dashboard.artikel.edit',
                'dashboard.artikel.show',
            ],
        ],
    ];

    // Conditional menu for Blast Notification (admin OR cabang with bank_sampah_id = 13)
    $blastNotificationMenu = [];
    if (!$isCabang || ($admin->role === 'cabang' && $admin->id_bank_sampah == 13)) {
        $blastNotificationMenu = [
            [
                'route' => 'dashboard.blast-notification.index',
                'icon' => 'icon/ic_notification.svg',
                'label' => 'Blast Notification',
                'subroutes' => [
                    'dashboard.blast-notification.index',
                    'dashboard.blast-notification.create',
                ],
            ],
        ];
    }

    // Helper function to check if any child is active (closure to avoid redeclare error)
    $isParentActive = function ($children, $current) {
        foreach ($children as $child) {
            if ($child === null) {
                continue;
            }
            if (isset($child['subroutes']) && in_array($current, $child['subroutes'])) {
                return true;
            }
            if (isset($child['route']) && $current === $child['route']) {
                return true;
            }
        }
        return false;
    };
@endphp

<button class="mobile-toggle" onclick="toggleSidebar()">☰</button>
<div class="sidebar-overlay" onclick="closeSidebar()"></div>
<nav class="sidebar" id="sidebar">
    <div class="logo-section">
        <img src="{{ asset('company/bengkelsampah.png') }}" alt="Logo" class="logo">
    </div>
    <ul class="nav-menu">
        {{-- Standalone menu items (Dashboard) --}}
        @foreach ($standaloneMenu as $item)
            <li class="nav-item">
                <a href="{{ route($item['route']) }}"
                    class="nav-link{{ $current === $item['route'] ? ' active' : '' }}">
                    <span class="nav-icon"><img src="/{{ $item['icon'] }}" alt="{{ $item['label'] }}"></span>
                    <span class="nav-text">{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach

        {{-- Hierarchical menus with parent groups --}}
        @foreach ($hierarchicalMenu as $group)
            @php
                $parentActive = $isParentActive($group['children'], $current);
            @endphp
            <li class="nav-item nav-item-parent{{ $parentActive ? ' open' : '' }}">
                <div class="nav-link-parent" onclick="toggleSubmenu(this)">
                    <span class="nav-icon"><img src="/{{ $group['icon'] }}" alt="{{ $group['label'] }}"></span>
                    <span class="nav-text">{{ $group['label'] }}</span>
                    <span class="nav-arrow">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                </div>
                <ul class="nav-submenu{{ $parentActive ? ' show' : '' }}">
                    @foreach ($group['children'] as $child)
                        @if ($child !== null)
                            <li class="nav-subitem">
                                <a href="{{ route($child['route']) }}"
                                    class="nav-sublink{{ (isset($child['subroutes']) && in_array($current, $child['subroutes'])) || $current === $child['route'] ? ' active' : '' }}">
                                    {{ $child['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </li>
        @endforeach

        {{-- Admin-only menus (both hierarchical and standalone) --}}
        @if (!$isCabang)
            @foreach ($adminOnlyMenu as $item)
                @if (isset($item['children']))
                    {{-- Hierarchical parent menu --}}
                    @php
                        $parentActive = $isParentActive($item['children'], $current);
                    @endphp
                    <li class="nav-item nav-item-parent{{ $parentActive ? ' open' : '' }}">
                        <div class="nav-link-parent" onclick="toggleSubmenu(this)">
                            <span class="nav-icon"><img src="/{{ $item['icon'] }}" alt="{{ $item['label'] }}"></span>
                            <span class="nav-text">{{ $item['label'] }}</span>
                            <span class="nav-arrow">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M2.5 4.5L6 8L9.5 4.5" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </div>
                        <ul class="nav-submenu{{ $parentActive ? ' show' : '' }}">
                            @foreach ($item['children'] as $child)
                                <li class="nav-subitem">
                                    <a href="{{ route($child['route']) }}"
                                        class="nav-sublink{{ (isset($child['subroutes']) && in_array($current, $child['subroutes'])) || $current === $child['route'] ? ' active' : '' }}">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else
                    {{-- Standalone menu item --}}
                    <li class="nav-item">
                        <a href="{{ route($item['route']) }}"
                            class="nav-link{{ (isset($item['subroutes']) && in_array($current, $item['subroutes'])) || $current === $item['route'] ? ' active' : '' }}">
                            <span class="nav-icon"><img src="/{{ $item['icon'] }}" alt="{{ $item['label'] }}"></span>
                            <span class="nav-text">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach
        @endif

        {{-- Conditional Blast Notification menu (admin or cabang with bank_sampah_id = 13) --}}
        @foreach ($blastNotificationMenu as $item)
            <li class="nav-item">
                <a href="{{ route($item['route']) }}"
                    class="nav-link{{ (isset($item['subroutes']) && in_array($current, $item['subroutes'])) || $current === $item['route'] ? ' active' : '' }}">
                    <span class="nav-icon"><img src="/{{ $item['icon'] }}" alt="{{ $item['label'] }}"></span>
                    <span class="nav-text">{{ $item['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
    }

    function toggleCollapse() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('collapsed');
    }

    function toggleSubmenu(element) {
        const parentItem = element.closest('.nav-item-parent');
        parentItem.classList.toggle('open');
        const submenu = parentItem.querySelector('.nav-submenu');
        submenu.classList.toggle('show');
    }

    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        // Close dropdown when clicking outside
        if (dropdown.style.display === 'block') {
            setTimeout(() => {
                document.addEventListener('click', closeDropdownOnClickOutside);
            }, 0);
        }
    }

    function closeDropdownOnClickOutside(e) {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown && !dropdown.contains(e.target) && !e.target.closest('[onclick="toggleUserDropdown()"]')) {
            dropdown.style.display = 'none';
            document.removeEventListener('click', closeDropdownOnClickOutside);
        }
    }
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        }
    });
</script>
