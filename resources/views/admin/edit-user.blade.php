<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
        .btn-back {
            background: #e8ebf5;
            color: #333;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: bold
        }
        .btn-back:hover {
            background: #dfe3ef
        }
        .card {
            margin-top: 35px;
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05)
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px
        }
        .form-group {
            display: flex;
            flex-direction: column
        }
        .form-group.full {
            grid-column: 1/-1
        }
        label {
            font-size: 15px;
            font-weight: bold;
            color: #37474f;
            margin-bottom: 9px
        }
        input,
        select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #dfe3eb;
            border-radius: 10px;
            background: #fafbff;
            font-size: 15px;
            outline: none
        }
        input:focus,
        select:focus {
            border-color: #007e6e;
            background: white
        }
        .password-wrapper {
            position: relative;
            width: 100%
        }
        .password-wrapper input {
            padding-right: 50px
        }
        .toggle-password {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 38px;
            height: 38px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 20px;
            display: flex;
            align-items: center;
            justify-content: center
        }
        .toggle-password:hover {
            background: #edf1ff;
            border-radius: 8px
        }
        .password-info {
            margin-top: 7px;
            font-size: 13px;
            color: #6c7778
        }
        .form-actions {
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 12px
        }
        .btn-cancel {
            padding: 14px 22px;
            border: none;
            border-radius: 10px;
            background: #e8ebf5;
            color: #333;
            font-weight: bold;
            cursor: pointer
        }
        .btn-save {
            padding: 14px 25px;
            border: none;
            border-radius: 10px;
            background: #007e6e;
            color: white;
            font-weight: bold;
            cursor: pointer
        }
        .btn-save:hover {
            background: #006b5e
        }
        .error-box {
            margin-bottom: 25px;
            padding: 15px 20px;
            border-radius: 10px;
            background: #ffe0e0;
            color: #8a2020
        }
        .error-box ul {
            padding-left: 20px
        }
        .info-box {
            margin-bottom: 25px;
            padding: 15px 20px;
            border-radius: 10px;
            background: #edf8f5;
            color: #075c4e
        }
        .success-box {
            margin-bottom: 25px;
            padding: 15px 20px;
            border-radius: 10px;
            background: #edf8f5;
            color: #075c4e
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
            .form-grid {
                grid-template-columns: 1fr
            }
            .form-group.full {
                grid-column: auto
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
            <a href="{{ route('dashboard') }}"><span>▦</span><span>Dashboard</span></a>
            <a href="{{ route('admin.daftar-user') }}" class="active"><span>♙</span><span>User</span></a>
            <a href="{{ route('absensi') }}"><span>♧</span><span>Absensi</span></a>
            <a href="#"><span>▣</span><span>Ticket</span></a>
            <a href="#"><span>▤</span><span>Daily Activity</span></a>
            <a href="#"><span>▤</span><span>Laporan</span></a>
            <a href="#"><span>♙</span><span>Profil</span></a>
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
            <div class="location">🏢 KP Jember - SBU Jatim</div>
            <div class="top-right">
                <span>📅 {{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                <span>●</span>
                <div class="profile-circle">♙</div>
                <span>Admin PKL</span>
            </div>
        </header>
        <main class="main">
            <div class="breadcrumb">ADMIN WORKSPACE &nbsp;›&nbsp; <span>EDIT USER</span></div>
            <div class="heading-row">
                <div>
                    <h1>Edit User</h1>
                    <div class="subtitle">Perbarui data peserta PKL PLN Icon Plus</div>
                </div>
                <a href="{{ route('admin.daftar-user') }}" class="btn-back">← Kembali</a>
            </div>
            @if($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if(session('success'))
                <div class="success-box">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="error-box">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                <div class="info-box">
                    <strong>Data peserta:</strong> {{ $user->name }}
                </div>
                <form action="{{ route('admin.daftar-user.update', ['id' => $user->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label>NIM</label>
                            <input type="text" name="nim" value="{{ old('nim', $user->nim) }}" required>
                        </div>
                        <div class="form-group">
    <label>Email Kampus</label>
    <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
</div>

<div class="form-group">
    <label>Nama Mentor</label>
    <input 
        type="text" 
        name="mentor" 
        value="{{ old('mentor', $user->mentor) }}" 
        placeholder="Masukkan nama mentor"
    >
                        </div>

                        <div class="form-group">
                            <label>Kelompok / Divisi</label>
                            <input 
                                type="text" 
                                name="group_name" 
                                value="{{ old('group_name', $user->group?->name) }}" 
                                placeholder="Masukkan kelompok / divisi"
                            >
                        </div>
                        <div class="form-group">
                            <label>Tanggal Mulai PKL</label>
                            <input type="date" name="periode_mulai" value="{{ old('periode_mulai', $user->periode_mulai ? $user->periode_mulai->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Selesai PKL</label>
                            <input type="date" name="periode_selesai" value="{{ old('periode_selesai', $user->periode_selesai ? $user->periode_selesai->format('Y-m-d') : '') }}">
                        </div>
                        <div class="form-group">
                            <label>Status Akun</label>
                            <select name="status" required>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <input type="text" value="Peserta PKL" disabled>
                        </div>
                        <div class="form-group full">
                            <label>Password Baru</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" value="{{ old('password') }}" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6" maxlength="100" autocomplete="new-password">
                                <button type="button" class="toggle-password" id="togglePassword" title="Tampilkan password">👁</button>
                            </div>
                            <div class="password-info">Kosongkan jika password lama tetap digunakan. Minimal 6 karakter jika ingin mengganti password.</div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <a href="{{ route('admin.daftar-user') }}" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-save">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    <script>
        const passwordInput=document.getElementById('password');
        const togglePassword=document.getElementById('togglePassword');
        togglePassword.addEventListener('click',function(){
            if(passwordInput.type==='password'){
                passwordInput.type='text';
                togglePassword.textContent='🙈';
                togglePassword.title='Sembunyikan password';
            }else{
                passwordInput.type='password';
                togglePassword.textContent='👁';
                togglePassword.title='Tampilkan password';
            }
        });
    </script>
</body>
</html>