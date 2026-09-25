<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f7f8fc;
            color: #263238
        }
        a {
            text-decoration: none;
            color: inherit
        }
        button,
        input,
        select {
            font-family: inherit
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #f0f2ff;
            padding: 24px 16px
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 45px
        }
        .logo-box {
            width: 54px;
            height: 54px;
            background: #007e6e;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold
        }
        .logo-title {
            color: #007e6e;
            font-size: 19px;
            font-weight: bold
        }
        .logo-subtitle {
            color: #3f4748;
            font-size: 14px;
            margin-top: 4px;
            font-weight: bold
        }
        .menu-title {
            color: #6c7778;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 20px
        }
        .menu a {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 17px 18px;
            border-radius: 12px;
            margin-bottom: 8px;
            font-size: 17px;
            font-weight: 600;
            color: #40494b
        }
        .menu a:hover {
            background: #dfeeea
        }
        .menu a.active {
            background: #008c78;
            color: white
        }
        .admin-box {
            position: absolute;
            bottom: 25px;
            left: 16px;
            right: 16px;
            background: #dfe5ff;
            padding: 15px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 12px
        }
        .admin-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #007e6e;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center
        }
        .admin-name {
            font-weight: bold
        }
        .admin-role {
            font-size: 13px;
            color: #555;
            margin-top: 3px
        }
        .content {
            margin-left: 250px;
            min-height: 100vh
        }
        .topbar {
            height: 82px;
            background: white;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px
        }
        .location {
            background: #edf1ff;
            padding: 11px 20px;
            border-radius: 13px;
            font-weight: bold
        }
        .top-right {
            display: flex;
            align-items: center;
            gap: 20px;
            font-weight: 600
        }
        .profile-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #007e6e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center
        }
        .main {
            padding: 32px 40px
        }
        .breadcrumb {
            color: #6a7475;
            font-size: 14px;
            margin-bottom: 12px
        }
        .breadcrumb span {
            color: #007e6e
        }
        .heading-row {
            display: flex;
            justify-content: space-between;
            align-items: center
        }
        h1 {
            font-size: 42px;
            margin-bottom: 10px;
            color: #151d36
        }
        .subtitle {
            color: #485456;
            font-size: 18px
        }
        .btn-add {
            background: #007e6e;
            color: white;
            border: none;
            padding: 15px 24px;
            border-radius: 13px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer
        }
        .btn-add:hover {
            background: #006b5e
        }
        .card {
            margin-top: 40px;
            background: white;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .03)
        }
        .tabs {
            display: flex;
            gap: 30px;
            padding: 0 25px;
            border-bottom: 1px solid #eee
        }
        .tab {
            padding: 24px 18px;
            font-size: 18px;
            font-weight: bold
        }
        .tab.active {
            color: #007e6e;
            border-bottom: 4px solid #007e6e
        }
        .badge {
            background: #65ead4;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 13px
        }
        .filter-area {
            padding: 22px 25px;
            background: #fafbff
        }
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: center
        }
        .search {
            flex: 1;
            position: relative
        }
        .search input {
            width: 100%;
            border: none;
            background: #f0f2ff;
            padding: 17px 20px;
            border-radius: 12px;
            font-size: 16px;
            outline: none
        }
        .filter-select {
            border: none;
            background: #f0f2ff;
            padding: 17px;
            border-radius: 12px;
            font-size: 15px;
            min-width: 220px;
            outline: none
        }
        .btn-filter {
            border: none;
            background: #007e6e;
            color: white;
            padding: 16px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold
        }
        .btn-filter:hover {
            background: #006b5e
        }
        .btn-reset {
            border: none;
            background: #e8ebf5;
            color: #333;
            padding: 16px 20px;
            border-radius: 12px;
            cursor: pointer;
            font-weight: bold
        }
        .btn-reset:hover {
            background: #dfe3ef
        }
        .filter-info {
            margin-top: 15px;
            color: #566263
        }
        .filter-tag {
            display: inline-block;
            background: #e5e9ff;
            color: #28385b;
            padding: 7px 12px;
            border-radius: 7px;
            margin-left: 8px
        }
        .table-wrapper {
            overflow-x: auto
        }
        table {
            width: 100%;
            border-collapse: collapse
        }
        th {
            background: #f1f3ff;
            text-align: left;
            padding: 18px;
            font-size: 14px;
            color: #465053
        }
        td {
            padding: 18px;
            border-bottom: 1px solid #eee;
            font-size: 14px
        }
        tr:hover td {
            background: #fafafa
        }
        .group-badge {
            display: inline-block;
            padding: 9px 13px;
            background: #d9f3ff;
            color: #075a83;
            border-radius: 20px;
            font-weight: bold;
            white-space: nowrap
        }
        .status {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: bold
        }
        .status.active {
            background: #62ead3;
            color: #063d36
        }
        .status.inactive {
            background: #ffd9d9;
            color: #a32929
        }
        .actions {
            display: flex;
            gap: 8px;
            align-items: center
        }
        .action-btn {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 9px;
            cursor: pointer;
            background: #f1f3f8;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center
        }
        .action-btn:hover {
            background: #dfeeea
        }
        .action-btn.danger:hover {
            background: #ffdede
        }
        .action-form {
            display: inline-flex;
            margin: 0;
            padding: 0
        }
        .action-btn:disabled {
            opacity: .5;
            cursor: not-allowed
        }
        .pagination-area {
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center
        }
        .pagination {
            display: flex;
            gap: 6px
        }
        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #ddd
        }
        .pagination .active {
            background: #007e6e;
            color: white
        }
        .alert {
            margin: 20px 25px;
            padding: 15px 20px;
            border-radius: 10px;
            background: #d9f8ef;
            color: #075c4e
        }
        .errors {
            margin: 20px 25px;
            padding: 15px 20px;
            border-radius: 10px;
            background: #ffe0e0;
            color: #8a2020
        }
        .errors ul {
            padding-left: 20px
        }
        .empty-state {
            text-align: center;
            padding: 50px !important;
            color: #6a7475
        }
        @media(max-width:1200px) {
            .filter-form {
                flex-wrap: wrap
            }
            .filter-select {
                flex: 1;
                min-width: 180px
            }
            .search {
                min-width: 300px
            }
        }
        @media(max-width:900px) {
            .sidebar {
                width: 80px
            }
            .logo-title,
            .logo-subtitle,
            .menu-title,
            .menu span,
            .admin-box div {
                display: none
            }
            .content {
                margin-left: 80px
            }
            .main {
                padding: 25px
            }
            .heading-row {
                gap: 20px
            }
            .filter-form {
                flex-wrap: wrap
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <div class="logo-box">PLN</div>
            <div>
                <div class="logo-title">Icon Plus</div>
                <div class="logo-subtitle">PKL MANAGEMENT</div>
            </div>
        </div>
        <div class="menu-title">MENU UTAMA</div>
        <nav class="menu">
            <a href="{{ route('dashboard') }}">
                <span>▦</span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.daftar-user') }}" class="active">
                <span>♙</span>
                <span>User</span>
            </a>
            <a href="{{ route('absensi') }}">
                <span>♧</span>
                <span>Absensi</span>
            </a>
            <a href="#">
                <span>▣</span>
                <span>Ticket</span>
            </a>
            <a href="#">
                <span>▤</span>
                <span>Daily Activity</span>
            </a>
            <a href="#">
                <span>▤</span>
                <span>Laporan</span>
            </a>
            <a href="#">
                <span>♙</span>
                <span>Profil</span>
            </a>
        </nav>
        <div class="admin-box">
            <div class="admin-icon">♙</div>
            <div>
                <div class="admin-name">Admin PKL</div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
    </aside>
    <div class="content">
        <header class="topbar">
            <div class="location">
                🏢 KP Jember - SBU Jatim
            </div>
            <div class="top-right">
                <span>
                    📅 {{ now()->locale('id')->translatedFormat('l, d F Y') }}
                </span>
                <span>●</span>
                <div class="profile-circle">
                    ♙
                </div>
                <span>
                    Admin PKL
                </span>
            </div>
        </header>
        <main class="main">
            <div class="breadcrumb">
                ADMIN WORKSPACE &nbsp;›&nbsp; <span>USER MANAGEMENT</span>
            </div>
            <div class="heading-row">
                <div>
                    <h1>
                        Kelola User
                    </h1>
                    <div class="subtitle">
                        Kelola akun, akses, dan penempatan peserta PKL PLN Icon Plus
                    </div>
                </div>
                <a href="{{ route('admin.daftar-user.tambah') }}" class="btn-add">
                    ＋ Tambah User
                </a>
            </div>
            @if(session('success'))
                <div class="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="errors">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="tabs">
                    <div class="tab active">
                        Daftar User
                        <span class="badge">
                            {{ $users->total() }}
                        </span>
                    </div>
                    <div class="tab">
                        Kelompok / Divisi
                        <span class="badge">
                            {{ $groups->count() }}
                        </span>
                    </div>
                </div>
                <div class="filter-area">
                    <form action="{{ route('admin.daftar-user') }}" method="GET" class="filter-form">
                        <div class="search">
                            <input type="text" name="keyword" placeholder="Cari nama, NIM, atau email..." value="{{ request('keyword') }}">
                        </div>
                        <select name="periode" class="filter-select">
                            <option value="">
                                Semua Periode
                            </option>
                            <option value="batch-1-2026" {{ request('periode') == 'batch-1-2026' ? 'selected' : '' }}>
                                Batch I (Feb - Jul 2026)
                            </option>
                            <option value="batch-2-2026" {{ request('periode') == 'batch-2-2026' ? 'selected' : '' }}>
                                Batch II (Agu 2026 - Jan 2027)
                            </option>
                        </select>
                        <button type="submit" class="btn-filter">
                            Filter
                        </button>
                        <a href="{{ route('admin.daftar-user') }}" class="btn-reset">
                            Reset
                        </a>
                    </form>
                    <div class="filter-info">
                        Menampilkan
                        <strong>
                            {{ $users->count() }}
                        </strong>
                        dari
                        <strong>
                            {{ $users->total() }}
                        </strong>
                        pengguna
                        @if(request('periode'))
                            <span class="filter-tag">
                                Periode:
                                {{ request('periode') == 'batch-1-2026'
                                    ? 'Batch I (Feb - Jul 2026)'
                                    : 'Batch II (Agu 2026 - Jan 2027)' }}
                            </span>
                        @endif
                        @if(request('keyword'))
                            <span class="filter-tag">
                                Pencarian: {{ request('keyword') }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>NAMA</th>
                                <th>NIM</th>
                                <th>EMAIL KAMPUS</th>
                                <th>MENTOR</th>
                                <th>KELOMPOK / DIVISI</th>
                                <th>PERIODE PKL</th>
                                <th>STATUS AKUN</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                @php
                                    $tanggalSelesai = $user->periode_selesai;
                                    $periodeSudahSelesai =
                                        $tanggalSelesai &&
                                        $tanggalSelesai->lt(now()->startOfDay());
                                    $statusAktif =
                                        $user->status === 'active' &&
                                        !$periodeSudahSelesai;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>
                                            {{ $user->name }}
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $user->nim }}
                                    </td>
                                    <td>
                                        {{ $user->email }}
                                    </td>
                                    <td>
                                        {{ $user->mentor ?? '-' }}
                                    </td>
                                    <td>
                                        @if($user->group)
                                            <span class="group-badge">
                                                {{ $user->group->name }}
                                            </span>
                                        @else
                                            <span>
                                                -
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->periode_mulai)
                                            {{ $user->periode_mulai->locale('id')->translatedFormat('d M Y') }}
                                            @if($user->periode_selesai)
                                                <br>
                                                -
                                                {{ $user->periode_selesai->locale('id')->translatedFormat('d M Y') }}
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($statusAktif)
                                            <span class="status active">
                                                ● Aktif
                                            </span>
                                        @else
                                            <span class="status inactive">
                                                ● Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="{{ route('admin.daftar-user.edit', ['id' => $user->id]) }}" class="action-btn" title="Edit">
                                                ✎
                                            </a>
                                            @if($statusAktif)
                                                <form action="{{ route('admin.daftar-user.nonaktifkan', ['id' => $user->id]) }}" method="POST" class="action-form" onsubmit="return confirm('Nonaktifkan akun {{ $user->name }}?')">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="action-btn" title="Nonaktifkan">
                                                        ⏻
                                                    </button>
                                                </form>
                                            @else
                                                @if(!$periodeSudahSelesai)
                                                    <form action="{{ route('admin.daftar-user.aktifkan', ['id' => $user->id]) }}" method="POST" class="action-form" onsubmit="return confirm('Aktifkan kembali akun {{ $user->name }}?')">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="action-btn" title="Aktifkan">
                                                            ✓
                                                        </button>
                                                    </form>
                                                @else
                                                    <button type="button" class="action-btn" title="PKL sudah selesai" disabled>
                                                        ✓
                                                    </button>
                                                @endif
                                            @endif
                                            <form action="{{ route('admin.daftar-user.hapus', ['id' => $user->id]) }}" method="POST" class="action-form" onsubmit="return confirm('Yakin ingin menghapus {{ $user->name }}? Data tidak dapat dikembalikan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn danger" title="Hapus">
                                                    🗑
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        Tidak ada data user.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($users->hasPages())
                    <div class="pagination-area">
                        <div>
                            Menampilkan
                            {{ $users->firstItem() ?? 0 }}
                            -
                            {{ $users->lastItem() ?? 0 }}
                            dari
                            {{ $users->total() }}
                        </div>
                        <div class="pagination">
                            @if($users->onFirstPage())
                                <span>
                                    ‹
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}">
                                    ‹
                                </a>
                            @endif
                            @foreach($users->getUrlRange(
                                max(1, $users->currentPage() - 2),
                                min($users->lastPage(), $users->currentPage() + 2)
                            ) as $page => $url)
                                @if($page == $users->currentPage())
                                    <span class="active">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                            @if($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}">
                                    ›
                                </a>
                            @else
                                <span>
                                    ›
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>