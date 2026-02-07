@extends('dashboard')
@section('content')
@include('partials.dashboard-header-bar', ['title' => 'History Reset XP'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Reset XP - Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            font-family: 'Urbanist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #fff;
            color: #1e293b;
        }

        .container {
            max-width: 1400px;
            background: #fff;
            border: 1px solid #E5E6E6;
            border-radius: 16px;
            padding: 16px 16px 16px 24px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 16px;
        }

        .search-filter {
            display: flex;
            gap: 1rem;
            flex: 1;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
            border-radius: 18px;
            border: 1px solid #EFF0F0;
            background: #EFF0F0;
            display: flex;
            align-items: center;
        }

        .search-input {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-family: 'Urbanist', sans-serif;
            font-size: 15px;
            font-weight: 400;
            color: #6B7271;
            padding: 6px 36px 6px 14px;
            border-radius: 18px;
        }

        .search-input::placeholder {
            color: #6B7271;
        }

        .search-box:focus-within {
            background: #fff;
        }

        .search-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            pointer-events: none;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: #39746E;
            color: #DFF0EE;
        }

        .btn-primary:hover {
            background: #2d5a55;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #E5E6E6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        thead {
            background: #F8FAFC;
        }

        th {
            padding: 12px 16px;
            text-align: left;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: #242E2C;
            border-bottom: 1px solid #E5E6E6;
            white-space: nowrap;
        }

        td {
            padding: 12px 16px;
            font-family: 'Urbanist', sans-serif;
            font-size: 14px;
            color: #6B7271;
            border-bottom: 1px solid #E5E6E6;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #F8FAFC;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #6B7271;
            font-style: italic;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .user-name {
            font-weight: 600;
            color: #242E2C;
        }

        .user-xp {
            font-size: 12px;
            color: #0FB7A6;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 16px;
            padding: 0 8px;
        }

        .pagination {
            display: flex;
            gap: 4px;
        }

        .pagination a,
        .pagination span {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
            color: #6B7271;
            border: 1px solid #E5E6E6;
        }

        .pagination a:hover {
            background: #F8FAFC;
        }

        .pagination .active {
            background: #39746E;
            color: white;
            border-color: #39746E;
        }

        .pagination .disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-gold {
            background: #FFF3CD;
            color: #856404;
        }

        .badge-silver {
            background: #E5E6E6;
            color: #242E2C;
        }

        .badge-bronze {
            background: #FFE4CC;
            color: #8B4513;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="controls">
            <div class="search-filter">
                <form method="GET" action="{{ route('admin.xp-reset.history') }}" class="search-box">
                    <input type="text" name="search" class="search-input" placeholder="Cari admin atau user..."
                        value="{{ request('search') }}">
                    <img src="{{ asset('icon/ic_search.svg') }}" alt="Search" class="search-icon">
                </form>
            </div>
            <a href="{{ route('admin.xp-reset.index') }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6 12L2 8L6 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M2 8H14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Kembali ke Reset XP
            </a>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Reset</th>
                        <th>Reset Oleh (Admin)</th>
                        <th>Top 1 User</th>
                        <th>Top 2 User</th>
                        <th>Top 3 User</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $index => $item)
                        <tr>
                            <td>{{ $history->firstItem() + $index }}</td>
                            <td>{{ $item->reset_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="user-info">
                                    <span class="user-name">{{ $item->admin_name }}</span>
                                    <span style="font-size: 12px; color: #6B7271;">ID: {{ $item->admin_id }}</span>
                                </div>
                            </td>
                            <td>
                                @if($item->top1_name)
                                    <div class="user-info">
                                        <span class="badge badge-gold">🥇 Top 1</span>
                                        <span class="user-name">{{ $item->top1_name }}</span>
                                        <span class="user-xp">{{ number_format($item->top1_xp, 0, ',', '.') }} XP</span>
                                    </div>
                                @else
                                    <span style="color: #ccc;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->top2_name)
                                    <div class="user-info">
                                        <span class="badge badge-silver">🥈 Top 2</span>
                                        <span class="user-name">{{ $item->top2_name }}</span>
                                        <span class="user-xp">{{ number_format($item->top2_xp, 0, ',', '.') }} XP</span>
                                    </div>
                                @else
                                    <span style="color: #ccc;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($item->top3_name)
                                    <div class="user-info">
                                        <span class="badge badge-bronze">🥉 Top 3</span>
                                        <span class="user-name">{{ $item->top3_name }}</span>
                                        <span class="user-xp">{{ number_format($item->top3_xp, 0, ',', '.') }} XP</span>
                                    </div>
                                @else
                                    <span style="color: #ccc;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="no-data">
                                Belum ada history reset XP
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($history->hasPages())
            <div class="pagination-container">
                <div>
                    Menampilkan {{ $history->firstItem() }} - {{ $history->lastItem() }} dari {{ $history->total() }} data
                </div>
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($history->onFirstPage())
                        <span class="disabled">&laquo;</span>
                    @else
                        <a href="{{ $history->previousPageUrl() }}">&laquo;</a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($history->links()->elements[0] as $page => $url)
                        @if ($page == $history->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($history->hasMorePages())
                        <a href="{{ $history->nextPageUrl() }}">&raquo;</a>
                    @else
                        <span class="disabled">&raquo;</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</body>
</html>
@endsection
