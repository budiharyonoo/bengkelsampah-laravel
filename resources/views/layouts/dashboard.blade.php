<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bengkel Sampah - Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --sidebar-bg: #E3F4F1;
            --sidebar-accent: #00B6A0;
            --sidebar-dark: #008378;
            --sidebar-active: #319795;
            --sidebar-text: #2d3748;
            --sidebar-link: #6B7271;
            --sidebar-hover: rgba(0,182,160,0.08);
            --primary-color: #00B6A0;
            --secondary-color: #008378;
            --success-color: #10B981;
            --warning-color: #F59E0B;
            --danger-color: #EF4444;
            --info-color: #3B82F6;
            --light-bg: #F8FAFC;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --border-radius: 12px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html {
            height: 100%;
            overflow: hidden;
        }
        body {
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: var(--light-bg);
            display: flex;
            min-height: 100vh;
            height: 100vh;
            overflow: hidden;
            overflow-x: hidden;
        }
        .sidebar {
            width: 220px;
            background-color: var(--sidebar-bg);
            padding: 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            top: 0;
            left: 0;
        }
        .sidebar.collapsed { width: 70px; }
        .logo-section {
            width: 100%;
            padding: 0 0 0 0;
            display: flex;
            flex-direction: column;
            align-items: left;
            justify-content: left;
        }
        .logo {
            width: 200px;
            height: auto;
            display: block;
        }
        .nav-menu { padding: 20px 0; list-style: none; width: 100%; }
        .nav-item { margin-bottom: 4px; }
        .nav-link {
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--sidebar-link);
            text-decoration: none;
            font-size: 14px;
            font-family: 'Urbanist', sans-serif;
            font-weight: 600;
            letter-spacing: 0.01em;
            transition: all 0.3s ease;
            position: relative;
            gap: 16px;
            border-radius: 25px;
            margin: 0 12px;
        }
        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-dark);
        }
        .nav-link.active {
            background-color: var(--sidebar-active);
            color: #fff;
        }
        /* Parent menu styling */
        .nav-link-parent {
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 14px 24px;
            color: var(--sidebar-link);
            font-size: 14px;
            font-family: 'Urbanist', sans-serif;
            font-weight: 600;
            gap: 16px;
            border-radius: 25px;
            margin: 0 12px;
            transition: all 0.3s ease;
        }
        .nav-link-parent:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-dark);
        }
        .nav-item-parent.open .nav-link-parent {
            color: var(--sidebar-dark);
        }
        /* Arrow icon */
        .nav-arrow {
            margin-left: auto;
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
        }
        .nav-item-parent.open .nav-arrow {
            transform: rotate(180deg);
        }
        /* Submenu container */
        .nav-submenu {
            list-style: none;
            padding: 0;
            margin: 0 12px;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .nav-submenu.show {
            max-height: 500px;
        }
        /* Submenu items */
        .nav-subitem {
            margin-bottom: 2px;
        }
        .nav-sublink {
            display: flex;
            align-items: center;
            padding: 10px 24px 10px 52px;
            color: var(--sidebar-link);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            border-radius: 8px;
        }
        .nav-sublink:hover {
            background-color: var(--sidebar-hover);
            color: var(--sidebar-dark);
            text-decoration: none;
        }
        .nav-sublink.active {
            background-color: var(--sidebar-active);
            color: #fff;
        }
        /* Collapsed sidebar - hide submenus */
        .sidebar.collapsed .nav-submenu {
            display: none;
        }
        .sidebar.collapsed .nav-arrow {
            display: none;
        }
        .sidebar.collapsed .nav-link-parent {
            justify-content: center;
            padding: 14px;
        }
        .nav-icon {
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .nav-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        .nav-link.active .nav-icon img {
            filter: brightness(0) invert(1);
        }
        .nav-text {
            transition: all 0.3s ease;
            white-space: nowrap;
            color: inherit;
        }
        .sidebar.collapsed .nav-text { opacity: 0; width: 0; overflow: hidden; }
        .sidebar.collapsed .nav-link { justify-content: center; padding: 14px; }
        .sidebar.collapsed .nav-link.active { margin: 0 8px; border-radius: 12px; }
        .mobile-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: var(--sidebar-active);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
            flex: 1;
            transition: margin-left 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
            height: 100vh;
        }
        .sidebar.collapsed + .main-content { margin-left: 70px; }
        @media (max-width: 768px) {
            .mobile-toggle { display: block; }
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; padding-top: 80px; height: auto; }
            .sidebar.collapsed + .main-content { margin-left: 0; }
        }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999; }
        .sidebar-overlay.active { display: block; }
        /* Header Styles */
        .header { 
            padding: 1rem 2rem 0rem 2rem; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .header h1 { 
            font-size: 22px; 
            font-weight: 700; 
            color: #39746E; 
            margin: 0;
        }
        .user-info { 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
        }
        .notification { 
            position: relative; 
            width: 24px; 
            height: 24px; 
            cursor: pointer; 
        }
        .notification::before { 
            content: "🔔"; 
            font-size: 18px; 
        }
        .user-name { 
            font-weight: 700; 
            font-size: 16px; 
            color: #39746E; 
        }
        .user-avatar { 
            width: 40px; 
            height: 40px; 
            border-radius: 50%; 
            background: #0FB7A6; 
            color: white; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-weight: 600; 
            font-size: 14px; 
        }
    </style>
    @yield('styles')
</head>
<body>
    @include('partials.sidebar')
    
    <main class="main-content">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html> 