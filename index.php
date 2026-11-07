<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPINTAR - Kemenag Kota Palembang</title>
    <link rel="icon" type="image/png" href="logo.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- jsPDF & autoTable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <!-- SheetJS / XLSX Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ---- SCROLLBAR ---- */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #10b981; border-radius: 99px; }

        /* ---- LOGIN SCREEN ---- */
        #login-screen {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 30%, #0f172a 100%);
        }
        .login-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(16,185,129,0.08);
            animation: floatShape 8s ease-in-out infinite;
        }
        .login-shape:nth-child(1) { width:280px; height:280px; top:-80px; left:-80px; animation-delay:0s; }
        .login-shape:nth-child(2) { width:200px; height:200px; bottom:-60px; right:-40px; animation-delay:3s; }
        .login-shape:nth-child(3) { width:140px; height:140px; top:50%; left:70%; animation-delay:5s; }
        @keyframes floatShape {
            0%,100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-24px) scale(1.06); }
        }

        .login-card {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 32px 80px rgba(0,0,0,0.5);
        }
        .login-inner-card {
            background: rgba(255,255,255,0.97);
            border-radius: 16px;
        }
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-in-up { animation: fadeInUp 0.6s cubic-bezier(.16,1,.3,1) forwards; }

        /* ---- HEADER ---- */
        #main-header {
            background: linear-gradient(90deg, #064e3b 0%, #065f46 60%, #047857 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        }
        .header-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%2310b981' fill-opacity='0.07'%3E%3Cpath d='M0 0h20v20H0zM20 20h20v20H20z'/%3E%3C/g%3E%3C/svg%3E");
        }
        .avatar-badge {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 2px 8px rgba(16,185,129,0.4);
        }
        #header-clock { font-variant-numeric: tabular-nums; }

        /* ---- TABS ---- */
        #main-tabs { scrollbar-width: none; }
        #main-tabs::-webkit-scrollbar { display: none; }
        .tab-active {
            background: linear-gradient(135deg, #064e3b, #065f46);
            color: #fff !important;
            box-shadow: 0 4px 14px rgba(6,78,59,0.35);
            border-radius: 10px;
        }
        .tab-btn {
            transition: all 0.2s ease;
            border-radius: 10px;
            border: none !important;
        }
        .tab-btn:hover:not(.tab-active) {
            background: rgba(16,185,129,0.09);
            color: #065f46;
        }

        /* ---- STAT CARDS ---- */
        .stat-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-6px) scale(1.015);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }
        .card-blue   { background: linear-gradient(135deg, #1d4ed8, #2563eb); }
        .card-amber  { background: linear-gradient(135deg, #b45309, #d97706); }
        .card-green  { background: linear-gradient(135deg, #064e3b, #059669); }
        .card-rose   { background: linear-gradient(135deg, #be123c, #e11d48); }

        /* ---- COUNT-UP ---- */
        .count-up { transition: color 0.3s; }

        /* ---- PULSE BADGE ---- */
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(220,38,38,0.6); }
            70%  { box-shadow: 0 0 0 8px rgba(220,38,38,0); }
            100% { box-shadow: 0 0 0 0 rgba(220,38,38,0); }
        }
        .badge-pulse { animation: pulse-ring 1.6s infinite; }

        /* ---- TABLE ---- */
        .modern-table thead th {
            background: linear-gradient(90deg, #f0fdf4, #ecfdf5);
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #374151;
            padding: 12px 14px;
            border-bottom: 2px solid #d1fae5;
        }
        .modern-table tbody tr {
            transition: background 0.15s;
        }
        .modern-table tbody tr:nth-child(even) { background: #f9fafb; }
        .modern-table tbody tr:hover { background: #f0fdf4; }
        .modern-table tbody td { padding: 11px 14px; font-size: 13px; }

        /* ---- FORMS ---- */
        .form-input {
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
            background: #fff;
        }
        .form-input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 3px rgba(5,150,105,0.12);
        }
        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 5px;
            letter-spacing: 0.03em;
        }

        /* ---- BUTTONS ---- */
        .btn-primary {
            background: linear-gradient(135deg, #064e3b, #059669);
            color: #fff;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 22px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(6,78,59,0.3);
        }
        .btn-primary:hover {
            box-shadow: 0 8px 20px rgba(6,78,59,0.4);
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #f1f5f9;
            color: #374151;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 20px;
            border: 1.5px solid #e2e8f0;
            transition: all 0.2s;
        }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger {
            background: linear-gradient(135deg, #be123c, #e11d48);
            color: #fff;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 20px;
            box-shadow: 0 4px 12px rgba(190,18,60,0.25);
            transition: all 0.2s;
        }
        .btn-danger:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(190,18,60,0.35); }

        /* ---- MODALS ---- */
        .modal-overlay { transition: opacity 0.25s; }
        .modal-content {
            animation: modalSlideIn 0.3s cubic-bezier(.16,1,.3,1) forwards;
        }
        @keyframes modalSlideIn {
            from { opacity:0; transform:scale(0.94) translateY(20px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        .modal-header {
            background: linear-gradient(90deg, #064e3b, #047857);
            padding: 16px 20px;
        }

        /* ---- SECTION CARDS ---- */
        .section-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e9f5f1;
            box-shadow: 0 2px 16px rgba(0,0,0,0.05);
            padding: 28px;
        }
        .section-title {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
        }

        /* ---- ALERT CARDS ---- */
        .alert-critical {
            border-left: 4px solid #dc2626;
            background: linear-gradient(90deg, #fff5f5, #fff);
        }
        .alert-warning {
            border-left: 4px solid #d97706;
            background: linear-gradient(90deg, #fffbf0, #fff);
        }
        .alert-safe {
            border-left: 4px solid #059669;
            background: linear-gradient(90deg, #f0fdf4, #fff);
        }

        /* ---- SIGNATURE CANVAS ---- */
        .signature-canvas {
            border: 2px dashed #d1fae5;
            border-radius: 12px;
            background: #fafffe;
            cursor: crosshair;
            transition: border-color 0.2s;
        }
        .signature-canvas:hover { border-color: #059669; }

        /* ---- FILTER BOX ---- */
        .filter-box {
            background: linear-gradient(135deg, #f0fdf4, #f8fafc);
            border: 1.5px solid #d1fae5;
            border-radius: 14px;
            padding: 16px;
        }

        /* ---- FOOTER ---- */
        footer {
            background: linear-gradient(90deg, #064e3b, #0f172a);
            color: rgba(255,255,255,0.55);
            padding: 18px;
            text-align: center;
            font-size: 12px;
            margin-top: 40px;
        }
        footer strong { color: #6ee7b7; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

    <!-- LOGIN SCREEN -->
    <div id="login-screen" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:linear-gradient(135deg,#064e3b 0%,#065f46 30%,#0f172a 100%)">
        <!-- Floating shapes -->
        <div class="login-shape" style="width:300px;height:300px;top:-90px;left:-90px;"></div>
        <div class="login-shape" style="width:220px;height:220px;bottom:-70px;right:-50px;animation-delay:3s"></div>
        <div class="login-shape" style="width:150px;height:150px;top:55%;left:68%;animation-delay:5.5s"></div>

        <div class="login-card rounded-3xl max-w-md w-full p-2 fade-in-up relative z-10">
            <div class="login-inner-card p-8">
                <!-- Logo & title -->
                <div class="text-center mb-8">
                    <div class="w-24 h-24 bg-gradient-to-br from-emerald-700 to-emerald-900 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl overflow-hidden p-1.5">
                        <img id="app-logo-img" src="logo.png" alt="Logo Kemenag Kota Palembang" class="w-full h-full object-contain rounded-xl" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-white font-bold text-sm text-center\'>KEMENAG</div>';">
                    </div>
                    <h2 class="text-3xl font-extrabold text-emerald-900 tracking-tight">SIPINTAR</h2>
                    <p class="text-slate-500 text-sm mt-1 font-medium">Sistem Pengelolaan Inventaris</p>
                    <div class="mt-1.5 inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold px-3 py-1 rounded-full">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Kemenag Kota Palembang
                    </div>
                </div>

                <form id="form-login" onsubmit="handleLogin(event)" class="space-y-4">
                    <div>
                        <label class="form-label">Username</label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="text" id="login-username" class="form-input pl-10" placeholder="Masukkan username">
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <div class="relative">
                            <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                            <input type="password" id="login-password" class="form-input pl-10" placeholder="Masukkan password">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2 mt-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Masuk ke Sistem
                    </button>
                </form>

                <div class="relative my-5 text-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <span class="relative bg-white px-3 text-[11px] text-slate-400 font-medium">Atau Akses Pengunjung</span>
                </div>

                <a href="pengunjung.php" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 py-2.5 rounded-xl font-semibold text-sm transition border border-slate-200 flex items-center justify-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-slate-500"></i> Buka Form Pengunjung (Tanpa Login)
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN APP DASHBOARD -->
    <div id="app-dashboard" class="hidden">
        <!-- HEADER / NAVBAR -->
        <header id="main-header" class="text-white sticky top-0 z-30 header-pattern">
            <div class="max-w-7xl mx-auto px-4 py-3.5 flex justify-between items-center">
                <div class="flex items-center gap-3.5">
                    <div class="p-1 bg-white rounded-xl w-11 h-11 flex items-center justify-center overflow-hidden shadow">
                        <img src="logo.png" alt="Logo Kemenag Kota Palembang" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-emerald-800 font-bold text-[9px] text-center\'>KEMENAG</div>';">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="font-extrabold text-xl leading-tight tracking-tight">SIPINTAR</h1>
                            <span class="hidden sm:inline-flex items-center bg-emerald-600 bg-opacity-50 border border-emerald-400 border-opacity-40 text-emerald-100 text-[10px] font-bold px-2 py-0.5 rounded-full">v2.0</span>
                        </div>
                        <p class="text-xs text-emerald-200 font-medium">Sistem Pengelolaan Inventaris Kemenag Kota Palembang</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Realtime Clock -->
                    <div class="hidden md:flex flex-col items-end">
                        <span id="header-clock" class="text-sm font-bold text-white leading-tight tabular-nums">--:--:--</span>
                        <span id="header-date" class="text-[10px] text-emerald-300 font-medium"></span>
                    </div>
                    <div class="w-px h-8 bg-emerald-600 bg-opacity-50 hidden md:block"></div>
                    <!-- User badge -->
                    <div class="flex items-center gap-2.5">
                        <div class="avatar-badge w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" id="user-avatar">P</div>
                        <div class="hidden sm:block text-right">
                            <p id="user-name" class="text-xs font-bold text-emerald-100 leading-tight">Pengguna Sistem</p>
                            <p class="text-[10px] text-emerald-300">Petugas Inventaris</p>
                        </div>
                    </div>
                    <button onclick="handleLogout()" class="btn-danger flex items-center gap-1.5 !py-2 !px-3 !text-xs !rounded-lg">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i> <span class="hidden sm:inline">Logout</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="max-w-7xl mx-auto px-4 py-6 space-y-6">
            <!-- TAB NAVIGATION -->
            <div id="main-tabs" class="-mx-4 -mt-6 mb-6 flex max-w-none items-center gap-1 overflow-x-auto bg-white border-b border-slate-100 px-4 pt-3 pb-3 shadow-sm">
                <button onclick="switchTab('dashboard')" id="tab-dashboard" class="tab-btn tab-active px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                </button>
                <button onclick="switchTab('barang_masuk')" id="tab-barang-masuk" class="tab-btn px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500">
                    <i data-lucide="corner-left-down" class="w-4 h-4"></i> Barang Masuk (Restok)
                </button>
                <button onclick="switchTab('form')" id="tab-form" class="tab-btn px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500">
                    <i data-lucide="corner-right-up" class="w-4 h-4"></i> Form Penerimaan (Keluar)
                </button>
                <button onclick="switchTab('master_barang')" id="tab-master-barang" class="tab-btn px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500">
                    <i data-lucide="package" class="w-4 h-4"></i> Data Master Barang
                </button>
                <button onclick="switchTab('history')" id="tab-history" class="tab-btn px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500">
                    <i data-lucide="history" class="w-4 h-4"></i> Riwayat &amp; Laporan
                </button>
                <button onclick="switchTab('akun')" id="tab-akun" class="tab-btn px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500">
                    <i data-lucide="users" class="w-4 h-4"></i> Kelola Akun
                </button>
            </div>

            <!-- SECTION 0: DASHBOARD STATISTIK -->
            <section id="section-dashboard" class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-extrabold tracking-tight text-slate-800">Ringkasan Inventaris</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Pantau kondisi stok dan aktivitas inventaris Kemenag Kota Palembang</p>
                    </div>
                </div>

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Barang -->
                    <div class="stat-card card-blue rounded-2xl p-5 text-white relative overflow-hidden cursor-pointer shadow-lg">
                        <div class="absolute -right-5 -top-5 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="absolute -right-2 -bottom-6 w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-blue-100 text-xs font-semibold uppercase tracking-wide">Total Jenis Barang</p>
                                <div class="p-2 bg-white bg-opacity-20 rounded-xl">
                                    <i data-lucide="boxes" class="w-5 h-5 text-white"></i>
                                </div>
                            </div>
                            <h3 id="dash-total-barang" class="text-4xl font-extrabold tabular-nums">0</h3>
                            <p class="text-blue-200 text-xs mt-1.5 font-medium">jenis item terdaftar</p>
                        </div>
                    </div>

                    <!-- Stok Kritis -->
                    <div class="stat-card card-amber rounded-2xl p-5 text-white relative overflow-hidden cursor-pointer shadow-lg">
                        <div class="absolute -right-5 -top-5 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="absolute -right-2 -bottom-6 w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-amber-100 text-xs font-semibold uppercase tracking-wide">Stok Kritis (≤ 5)</p>
                                <div class="p-2 bg-white bg-opacity-20 rounded-xl">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                                </div>
                            </div>
                            <h3 id="dash-stok-kritis" class="text-4xl font-extrabold tabular-nums">0</h3>
                            <p class="text-amber-100 text-xs mt-1.5 font-medium">barang perlu restok</p>
                        </div>
                    </div>

                    <!-- Barang Masuk -->
                    <div class="stat-card card-green rounded-2xl p-5 text-white relative overflow-hidden cursor-pointer shadow-lg">
                        <div class="absolute -right-5 -top-5 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="absolute -right-2 -bottom-6 w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-emerald-100 text-xs font-semibold uppercase tracking-wide">Transaksi Masuk</p>
                                <div class="p-2 bg-white bg-opacity-20 rounded-xl">
                                    <i data-lucide="arrow-down-left" class="w-5 h-5 text-white"></i>
                                </div>
                            </div>
                            <h3 id="dash-trans-masuk" class="text-4xl font-extrabold tabular-nums">0</h3>
                            <p class="text-emerald-100 text-xs mt-1.5 font-medium">total transaksi restok</p>
                        </div>
                    </div>

                    <!-- Barang Keluar -->
                    <div class="stat-card card-rose rounded-2xl p-5 text-white relative overflow-hidden cursor-pointer shadow-lg">
                        <div class="absolute -right-5 -top-5 w-24 h-24 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="absolute -right-2 -bottom-6 w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-rose-100 text-xs font-semibold uppercase tracking-wide">Transaksi Keluar</p>
                                <div class="p-2 bg-white bg-opacity-20 rounded-xl">
                                    <i data-lucide="arrow-up-right" class="w-5 h-5 text-white"></i>
                                </div>
                            </div>
                            <h3 id="dash-trans-keluar" class="text-4xl font-extrabold tabular-nums">0</h3>
                            <p class="text-rose-100 text-xs mt-1.5 font-medium">total penerimaan barang</p>
                        </div>
                    </div>
                </div>

                <!-- Alert Stok Kritis -->
                <div class="section-card">
                    <h2 class="font-bold text-slate-800 mb-4 flex items-center gap-2 text-base">
                        <div class="p-1.5 bg-amber-100 rounded-lg">
                            <i data-lucide="bell" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        Peringatan Notifikasi Stok Kritis
                    </h2>
                    <div id="dash-alert-container" class="space-y-2.5"></div>
                </div>

                <!-- Recent Transactions -->
                <div class="section-card">
                    <div class="flex justify-between items-center mb-5">
                        <div>
                            <h2 class="font-bold text-slate-800 flex items-center gap-2 text-base">
                                <div class="p-1.5 bg-emerald-100 rounded-lg">
                                    <i data-lucide="clock" class="w-4 h-4 text-emerald-700"></i>
                                </div>
                                Daftar Transaksi Terbaru
                            </h2>
                            <p class="mt-1 text-xs text-slate-500 ml-9">Aktivitas arus masuk dan keluar barang terakhir</p>
                        </div>
                        <button onclick="switchTab('history')" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition">Lihat Semua <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></button>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-sm text-left modern-table">
                            <thead>
                                <tr>
                                    <th>Kode Transaksi</th>
                                    <th>Tipe</th>
                                    <th>Tanggal</th>
                                    <th>Pihak Terkait</th>
                                    <th>Rincian Barang</th>
                                </tr>
                            </thead>
                            <tbody id="dash-recent-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 1: BARANG MASUK -->
            <section id="section-barang-masuk" class="hidden section-card">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="p-2.5 bg-emerald-100 rounded-xl">
                        <i data-lucide="box" class="w-5 h-5 text-emerald-700"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Form Restok / Barang Masuk</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Input penerimaan barang baru ke gudang inventaris</p>
                    </div>
                </div>

                <form id="form-barang-masuk" onsubmit="handleSaveBarangMasuk(event)" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" id="bm_tanggal" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Petugas Penerima</label>
                            <input type="text" id="bm_petugas" required placeholder="Nama Petugas Inventaris" class="form-input">
                        </div>
                    </div>

                    <div>
                        <label class="form-label mb-2">Daftar Barang Masuk</label>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-sm text-left modern-table">
                                <thead>
                                    <tr>
                                        <th>Nama Barang</th>
                                        <th class="w-32">Jumlah Masuk</th>
                                        <th class="w-36">Satuan</th>
                                        <th class="w-16 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bm-table-items-body"></tbody>
                            </table>
                        </div>
                        <button type="button" onclick="addBarangMasukRow()" class="mt-3 text-xs font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-lg transition">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="max-w-xl">
                        <label class="form-label mb-2">Tanda Tangan Petugas Penerima</label>
                        <canvas id="bm-signature" width="600" height="180" class="signature-canvas w-full h-36 touch-none"></canvas>
                        <button type="button" onclick="clearSignature('bm-signature')" class="mt-2 text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1">
                            <i data-lucide="eraser" class="w-3.5 h-3.5"></i> Hapus tanda tangan
                        </button>
                    </div>

                    <div class="pt-2 flex justify-end border-t border-slate-100">
                        <button type="submit" class="btn-primary flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Barang Masuk
                        </button>
                    </div>
                </form>
            </section>

            <!-- SECTION 2: BARANG KELUAR -->
            <section id="section-form" class="hidden section-card">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="p-2.5 bg-rose-100 rounded-xl">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-rose-700"></i>
                    </div>
                    <div>
                        <h2 class="section-title">Form Penerimaan Barang (Barang Keluar)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Catat barang yang dikeluarkan dan diterima oleh guru / staf</p>
                    </div>
                </div>

                <div id="form-holder">
                    <form id="form-pengambilan" onsubmit="handleSaveTransaksi(event)" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label">Tanggal Penerimaan</label>
                                <input type="date" id="tanggal_pengambilan" required class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Nama Penerima</label>
                                <input type="text" id="nama_pengambil" required placeholder="Nama Guru / Staf Penerima" class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Jabatan</label>
                                <input type="text" id="jabatan_unit" required placeholder="Contoh: Guru IPA / Staf TU" class="form-input">
                            </div>
                        </div>

                        <div>
                            <label class="form-label mb-2">Daftar Barang yang Diterima</label>
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="w-full text-sm text-left modern-table">
                                    <thead>
                                        <tr>
                                            <th>Nama Barang</th>
                                            <th class="w-32">Jumlah</th>
                                            <th class="w-36">Satuan</th>
                                            <th class="w-16 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-items-body"></tbody>
                                </table>
                            </div>
                            <button type="button" onclick="addBarangRow()" class="mt-3 text-xs font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-lg transition">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Baris Barang
                            </button>
                        </div>

                        <div>
                            <label class="form-label">Keterangan / Keperluan (Opsional)</label>
                            <textarea id="keterangan" rows="3" placeholder="Catatan atau keperluan barang..." class="form-input resize-none"></textarea>
                        </div>

                        <div class="max-w-xl">
                            <label class="form-label mb-2">Tanda Tangan Penerima</label>
                            <canvas id="bk-signature" width="600" height="180" class="signature-canvas w-full h-36 touch-none"></canvas>
                            <button type="button" onclick="clearSignature('bk-signature')" class="mt-2 text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-1">
                                <i data-lucide="eraser" class="w-3.5 h-3.5"></i> Hapus tanda tangan
                            </button>
                        </div>

                        <div class="pt-2 flex justify-end border-t border-slate-100">
                            <button type="submit" class="btn-primary flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Penerimaan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- SECTION 3: MASTER BARANG -->
            <section id="section-master-barang" class="hidden section-card">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-blue-100 rounded-xl">
                            <i data-lucide="package" class="w-5 h-5 text-blue-700"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Master Data Barang</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Kelola jenis barang, nama barang, dan stok ketersediaan inventaris.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="openExcelModal()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow transition">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Upload Excel
                        </button>
                        <button onclick="exportMasterBarangExcel()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-sky-700 hover:bg-sky-800 text-white shadow transition">
                            <i data-lucide="download" class="w-4 h-4"></i> Export Excel
                        </button>
                        <button onclick="openBarangModal()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-800 hover:bg-emerald-900 text-white shadow transition">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Barang
                        </button>
                    </div>
                </div>

                <div class="flex flex-wrap items-end gap-3 mb-5">
                    <div>
                        <label for="barang-filter-stok" class="form-label">Status stok</label>
                        <select id="barang-filter-stok" onchange="renderTableBarang()" class="form-input !w-auto">
                            <option value="">Semua barang</option>
                            <option value="habis">Stok habis</option>
                            <option value="kurang5">Stok &lt; 5</option>
                            <option value="tersedia">Stok tersedia</option>
                        </select>
                    </div>
                    <button onclick="cetakLaporanMasterBarang()" class="flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold bg-rose-700 hover:bg-rose-800 text-white shadow transition">
                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm text-left modern-table">
                        <thead>
                            <tr>
                                <th class="w-12">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Jenis Barang</th>
                                <th>Kondisi</th>
                                <th>Stok Tersedia</th>
                                <th>Satuan</th>
                                <th>Terakhir Diubah</th>
                                <th class="w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="barang-table-body"></tbody>
                    </table>
                </div>
            </section>

            <!-- SECTION 4: RIWAYAT & LAPORAN -->
            <section id="section-history" class="hidden section-card">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 bg-indigo-100 rounded-xl">
                            <i data-lucide="history" class="w-5 h-5 text-indigo-700"></i>
                        </div>
                        <div>
                            <h2 class="section-title">Riwayat &amp; Laporan Inventaris</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi transaksi barang masuk &amp; keluar serta rekap PDF/Excel.</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="exportBuktiPenerimaan()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-indigo-700 hover:bg-indigo-800 text-white shadow transition">
                            <i data-lucide="file-down" class="w-4 h-4"></i> Rekap Bukti Penerimaan
                        </button>
                        <button onclick="exportPDF()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-700 hover:bg-rose-800 text-white shadow transition">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Cetak PDF
                        </button>
                        <button onclick="exportExcel()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow transition">
                            <i data-lucide="sheet" class="w-4 h-4"></i> Cetak Excel
                        </button>
                        <button onclick="handleHapusTerfilter()" class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white shadow transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Terfilter
                        </button>
                    </div>
                </div>

                <p class="mb-3 text-[11px] text-slate-500 flex items-center gap-1">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400"></i>
                    Filter bulan, tahun, dan tipe transaksi di bawah juga diterapkan pada Rekap Bukti Penerimaan.
                </p>
                <div class="filter-box grid grid-cols-1 sm:grid-cols-4 gap-3 mb-5">
                    <div>
                        <label class="form-label">Tipe Transaksi</label>
                        <select id="filter-tipe" onchange="renderTableHistory()" class="form-input">
                            <option value="">Semua Transaksi</option>
                            <option value="MASUK">Barang Masuk (Restok)</option>
                            <option value="KELUAR">Barang Keluar (Penerimaan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Pencarian</label>
                        <input type="text" id="filter-search" oninput="renderTableHistory()" placeholder="Cari Kode/Pihak/Barang..." class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Bulan</label>
                        <select id="filter-bulan" onchange="renderTableHistory()" class="form-input">
                            <option value="">Semua Bulan</option>
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tahun</label>
                        <input type="number" id="filter-tahun" oninput="renderTableHistory()" placeholder="Contoh: 2026" class="form-input">
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm text-left modern-table">
                        <thead>
                            <tr>
                                <th>Kode Transaksi</th>
                                <th>Tipe</th>
                                <th>Tanggal</th>
                                <th>Penerima / Petugas</th>
                                <th>Rincian Barang</th>
                                <th>Keterangan</th>
                                <th class="w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL EDIT TRANSAKSI -->
    <div id="modal-edit-transaksi" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-60 p-4 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden modal-content">
            <div class="modal-header flex justify-between items-center">
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <i data-lucide="edit-3" class="w-4 h-4"></i> Edit Data Transaksi
                </h3>
                <button onclick="closeEditTransaksiModal()" class="text-emerald-200 hover:text-white w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white hover:bg-opacity-20 transition text-xl leading-none">&times;</button>
            </div>
            <form id="form-edit-transaksi" onsubmit="handleSaveEditTransaksi(event)" class="p-6 space-y-4 max-h-[85vh] overflow-y-auto">
                <input type="hidden" id="edit_kode_transaksi">
                <input type="hidden" id="edit_tipe_transaksi">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="edit_tanggal" required class="form-input">
                    </div>
                    <div>
                        <label id="edit_label_pengambil" class="form-label">Nama Penerima</label>
                        <input type="text" id="edit_nama_pengambil" required class="form-input">
                    </div>
                    <div>
                        <label id="edit_label_jabatan" class="form-label">Jabatan</label>
                        <input type="text" id="edit_jabatan_unit" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label mb-2">Daftar Barang</label>
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs text-left modern-table">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="w-28">Jumlah</th>
                                    <th class="w-28">Satuan</th>
                                    <th class="w-12 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="edit-table-items-body"></tbody>
                        </table>
                    </div>
                    <button type="button" onclick="addEditBarangRow()" class="mt-2 text-xs font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Baris Barang
                    </button>
                </div>

                <div>
                    <label class="form-label">Keterangan</label>
                    <textarea id="edit_keterangan" rows="2" class="form-input resize-none"></textarea>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeEditTransaksiModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL UPLOAD EXCEL -->
    <div id="modal-excel" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-60 p-4 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden modal-content">
            <div class="modal-header flex justify-between items-center">
                <h3 class="font-bold text-sm text-white flex items-center gap-2">
                    <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Upload Template Excel Barang
                </h3>
                <button onclick="closeExcelModal()" class="text-emerald-200 hover:text-white w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white hover:bg-opacity-20 transition text-xl leading-none">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl text-xs text-emerald-800">
                    <p class="font-bold mb-1 flex items-center gap-1.5"><i data-lucide="info" class="w-3.5 h-3.5"></i> Format Kolom Excel yang Dibutuhkan:</p>
                    <p class="font-mono text-[11px] bg-white border border-emerald-100 rounded-lg px-3 py-2 mt-1.5">nama_barang | jenis_barang | stok | satuan</p>
                </div>

                <button onclick="downloadExcelTemplate()" class="w-full py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-xl text-xs font-bold border border-slate-200 flex items-center justify-center gap-2 transition">
                    <i data-lucide="download" class="w-4 h-4 text-emerald-600"></i> Download Format Template Excel
                </button>

                <div class="border-t border-slate-100 pt-2"></div>

                <div>
                    <label class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" id="excel-file-input" accept=".xlsx, .xls" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-200 rounded-xl p-1.5">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeExcelModal()" class="btn-secondary">Batal</button>
                    <button type="button" onclick="processExcelUpload()" class="btn-primary flex items-center gap-2">
                        <i data-lucide="upload" class="w-4 h-4"></i> Upload &amp; Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT / TAMBAH BARANG -->
    <div id="modal-barang" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-60 p-4 modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden modal-content">
            <div class="modal-header flex justify-between items-center">
                <h3 id="modal-barang-title" class="font-bold text-sm text-white flex items-center gap-2">
                    <i data-lucide="package-plus" class="w-4 h-4"></i> Tambah Data Barang
                </h3>
                <button onclick="closeBarangModal()" class="text-emerald-200 hover:text-white w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white hover:bg-opacity-20 transition text-xl leading-none">&times;</button>
            </div>
            <form id="form-barang" onsubmit="handleSaveMasterBarang(event)" class="p-6 space-y-4">
                <input type="hidden" id="barang-id">
                <div>
                    <label class="form-label">Nama Barang</label>
                    <input type="text" id="barang-nama" required class="form-input" placeholder="Contoh: Spidol Boardmarker">
                </div>
                <div>
                    <label class="form-label">Jenis / Kategori Barang</label>
                    <input type="text" id="barang-jenis" required class="form-input" placeholder="Contoh: ATK / Alat Kebersihan">
                </div>
                <div>
                    <label class="form-label">Kondisi Barang</label>
                    <select id="barang-kondisi" required class="form-input">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Sedang">Rusak Sedang</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="form-label">Jumlah Stok</label>
                        <input type="number" id="barang-stok" required min="0" value="0" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Satuan</label>
                        <input type="text" id="barang-satuan" required class="form-input" placeholder="Pcs/Box/Pack">
                    </div>
                </div>
                <div class="pt-2 flex justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeBarangModal()" class="btn-secondary">Batal</button>
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Barang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- KELOLA AKUN -->
    <section id="section-akun" class="hidden max-w-7xl mx-auto px-4 pb-6">
        <div class="section-card">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                <div class="p-2.5 bg-purple-100 rounded-xl">
                    <i data-lucide="users" class="w-5 h-5 text-purple-700"></i>
                </div>
                <div>
                    <h2 class="section-title">Kelola Akun</h2>
                    <p id="akun-description" class="text-xs text-slate-500 mt-0.5">Kelola akun pengguna SIPINTAR.</p>
                </div>
            </div>
            <div id="admin-account-tools" class="hidden space-y-6">
                <form id="form-akun-baru" onsubmit="handleCreateAccount(event)" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <div>
                        <label class="form-label">Nama lengkap</label>
                        <input id="akun-nama" required class="form-input" autocomplete="name">
                    </div>
                    <div>
                        <label class="form-label">Username</label>
                        <input id="akun-username" required class="form-input" autocomplete="username">
                    </div>
                    <div>
                        <label class="form-label">Password</label>
                        <input id="akun-password" type="password" required minlength="4" class="form-input" autocomplete="new-password">
                    </div>
                    <div>
                        <label class="form-label">Peran</label>
                        <select id="akun-role" class="form-input">
                            <option value="pegawai">Pegawai</option>
                            <option value="admin">Admin</option>
                            <option value="pemantau">Pemantau</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary flex items-center justify-center gap-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Akun
                    </button>
                </form>
                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="w-full text-sm text-left modern-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Peran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="account-table-body"></tbody>
                    </table>
                </div>
                <div id="modal-edit-akun" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-60 p-4 modal-overlay">
                    <form onsubmit="handleEditAccount(event)" class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4 modal-content">
                        <div class="flex items-center justify-between pb-3 border-b">
                            <h3 class="font-bold text-slate-800 flex items-center gap-2"><i data-lucide="edit-2" class="w-4 h-4 text-emerald-600"></i> Edit Akun</h3>
                            <button type="button" onclick="closeEditAccountModal()" class="text-slate-400 hover:text-slate-700 text-xl w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 transition leading-none">&times;</button>
                        </div>
                        <input id="edit-akun-id" type="hidden">
                        <div>
                            <label class="form-label">Nama lengkap</label>
                            <input id="edit-akun-nama" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Username</label>
                            <input id="edit-akun-username" required class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Password baru</label>
                            <input id="edit-akun-password" type="password" minlength="4" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Peran</label>
                            <select id="edit-akun-role" class="form-input">
                                <option value="pegawai">Pegawai</option>
                                <option value="admin">Admin</option>
                                <option value="pemantau">Pemantau</option>
                            </select>
                        </div>
                        <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                            <button type="button" onclick="closeEditAccountModal()" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <form id="form-ubah-password" onsubmit="handleChangeOwnPassword(event)" class="hidden max-w-md space-y-4">
                <div>
                    <label class="form-label">Password saat ini</label>
                    <input id="password-saat-ini" type="password" required autocomplete="current-password" class="form-input">
                </div>
                <div>
                    <label class="form-label">Password baru (minimal 4 karakter)</label>
                    <input id="password-baru" type="password" required minlength="4" autocomplete="new-password" class="form-input">
                </div>
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <i data-lucide="key" class="w-4 h-4"></i> Ubah Password
                </button>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <strong>SIPINTAR</strong> &mdash; Sistem Pengelolaan Inventaris &copy; 2026 <strong>Kemenag Kota Palembang</strong> &bull; SIPINTAR Dev
    </footer>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let rawTransaksiData = [];
        let masterBarangList = [];
        let showAllCriticalStock = false;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
            setupSignaturePad('bm-signature');
            setupSignaturePad('bk-signature');
            
            const today = new Date();
            document.getElementById('tanggal_pengambilan').valueAsDate = today;
            document.getElementById('bm_tanggal').valueAsDate = today;

            const isLoggedIn = sessionStorage.getItem('sipintar_logged_in') === 'true';

            if (isLoggedIn) {
                showDashboard();
            }

            // Realtime clock
            function updateClock() {
                const now = new Date();
                const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                const h = String(now.getHours()).padStart(2,'0');
                const m = String(now.getMinutes()).padStart(2,'0');
                const s = String(now.getSeconds()).padStart(2,'0');
                const clockEl = document.getElementById('header-clock');
                const dateEl = document.getElementById('header-date');
                if (clockEl) clockEl.textContent = `${h}:${m}:${s}`;
                if (dateEl) dateEl.textContent = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
            }
            updateClock();
            setInterval(updateClock, 1000);
        });

        async function handleLogin(e) {
            e.preventDefault();
            const u = document.getElementById('login-username').value;
            const p = document.getElementById('login-password').value;

            try {
                const res = await fetch('api.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username: u, password: p })
                });
                const result = await res.json();

                if (result.success) {
                    sessionStorage.setItem('sipintar_logged_in', 'true');
                    sessionStorage.setItem('sipintar_user_id', result.id);
                    sessionStorage.setItem('sipintar_user', result.nama);
                    sessionStorage.setItem('sipintar_username', result.username);
                    sessionStorage.setItem('sipintar_role', result.role);

                    if (result.role === 'pemantau') {
                        sessionStorage.setItem('sipintar_pemantau_logged_in', 'true');
                        window.location.href = 'pemantau.php';
                        return;
                    }
                    
                    showDashboard();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal terhubung ke server PHP/MySQL!');
            }
        }

        async function showDashboard() {
            document.getElementById('login-screen').classList.add('hidden');
            document.getElementById('app-dashboard').classList.remove('hidden');

            const userName = sessionStorage.getItem('sipintar_user') || 'Pengguna Sistem';
            document.getElementById('user-name').innerText = userName;
            // Update avatar initials
            const avatarEl = document.getElementById('user-avatar');
            if (avatarEl && userName) avatarEl.textContent = userName.charAt(0).toUpperCase();

            const isAdmin = sessionStorage.getItem('sipintar_role') === 'admin';
            const isStaff = ['admin', 'pegawai'].includes(sessionStorage.getItem('sipintar_role'));
            document.getElementById('tab-akun').classList.toggle('hidden', !isStaff);
            document.getElementById('admin-account-tools').classList.toggle('hidden', !isAdmin);
            document.getElementById('form-ubah-password').classList.toggle('hidden', isAdmin || !isStaff);
            document.getElementById('akun-description').innerText = isAdmin
                ? 'Lihat akun dan password yang terdaftar, atau tambahkan akun baru.'
                : 'Ubah password akun Anda sendiri.';

            await loadMasterBarang();
            await loadTransaksiData();
            
            addBarangRow();
            addBarangMasukRow();
            switchTab('dashboard');
        }

        async function handleLogout() {
            try { await fetch('api.php?action=logout'); } catch (err) {}
            sessionStorage.clear();
            location.reload();
        }

        async function loadAccounts() {
            const tbody = document.getElementById('account-table-body');
            tbody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-slate-400">Memuat akun...</td></tr>';
            try {
                const res = await fetch('api.php?action=get_accounts');
                const result = await res.json();
                if (!result.success) throw new Error(result.message);
                tbody.innerHTML = '';
                result.data.forEach(account => {
                    const tr = document.createElement('tr');
                    tr.className = 'border-b text-xs';
                    [account.nama_lengkap, account.username, account.password, account.role].forEach(value => {
                        const td = document.createElement('td');
                        td.className = 'p-3';
                        td.textContent = value;
                        tr.appendChild(td);
                    });
                    const actions = document.createElement('td');
                    actions.className = 'p-3 whitespace-nowrap';
                    const editButton = document.createElement('button');
                    editButton.type = 'button';
                    editButton.className = 'text-emerald-700 hover:text-emerald-900 font-semibold mr-3';
                    editButton.textContent = 'Edit';
                    editButton.addEventListener('click', () => openEditAccountModal(account));
                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'text-rose-600 hover:text-rose-800 font-semibold';
                    deleteButton.textContent = 'Hapus';
                    deleteButton.addEventListener('click', () => handleDeleteAccount(account));
                    actions.append(editButton, deleteButton);
                    tr.appendChild(actions);
                    tbody.appendChild(tr);
                });
                if (result.data.length === 0) tbody.innerHTML = '<tr><td colspan="5" class="p-3 text-center text-slate-400">Belum ada akun.</td></tr>';
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="5" class="p-3 text-center text-rose-600">${err.message || 'Gagal memuat akun.'}</td></tr>`;
            }
        }

        function openEditAccountModal(account) {
            document.getElementById('edit-akun-id').value = account.id;
            document.getElementById('edit-akun-nama').value = account.nama_lengkap;
            document.getElementById('edit-akun-username').value = account.username;
            document.getElementById('edit-akun-password').value = '';
            document.getElementById('edit-akun-role').value = account.role;
            document.getElementById('modal-edit-akun').classList.remove('hidden');
        }

        function closeEditAccountModal() {
            document.getElementById('modal-edit-akun').classList.add('hidden');
        }

        async function handleEditAccount(e) {
            e.preventDefault();
            const payload = {
                id: document.getElementById('edit-akun-id').value,
                nama_lengkap: document.getElementById('edit-akun-nama').value.trim(),
                username: document.getElementById('edit-akun-username').value.trim(),
                password: document.getElementById('edit-akun-password').value,
                role: document.getElementById('edit-akun-role').value
            };
            try {
                const res = await fetch('api.php?action=edit_account', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) {
                    closeEditAccountModal();
                    await loadAccounts();
                }
            } catch (err) { alert('Gagal memperbarui akun.'); }
        }

        async function handleDeleteAccount(account) {
            if (!confirm(`Hapus akun "${account.username}"? Tindakan ini tidak dapat dibatalkan.`)) return;
            try {
                const res = await fetch('api.php?action=delete_account', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: account.id })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) await loadAccounts();
            } catch (err) { alert('Gagal menghapus akun.'); }
        }

        async function handleCreateAccount(e) {
            e.preventDefault();
            const payload = {
                nama_lengkap: document.getElementById('akun-nama').value.trim(),
                username: document.getElementById('akun-username').value.trim(),
                password: document.getElementById('akun-password').value,
                role: document.getElementById('akun-role').value
            };
            try {
                const res = await fetch('api.php?action=create_account', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) {
                    document.getElementById('form-akun-baru').reset();
                    await loadAccounts();
                }
            } catch (err) { alert('Gagal menambahkan akun.'); }
        }

        async function handleChangeOwnPassword(e) {
            e.preventDefault();
            try {
                const res = await fetch('api.php?action=change_own_password', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        current_password: document.getElementById('password-saat-ini').value,
                        new_password: document.getElementById('password-baru').value
                    })
                });
                const result = await res.json();
                alert(result.message);
                if (result.success) document.getElementById('form-ubah-password').reset();
            } catch (err) { alert('Gagal mengubah password.'); }
        }

        function updateAllSelectOptions() {
            const selectElements = document.querySelectorAll('#table-items-body .item-nama, #bm-table-items-body .item-nama');
            selectElements.forEach(selectElem => {
                const currentValue = selectElem.value;
                let options = '<option value="">-- Pilih Barang --</option>';
                masterBarangList.forEach(b => {
                    const isMasuk = selectElem.closest('tbody').id === 'bm-table-items-body';
                    const label = isMasuk
                        ? `${b.nama_barang} [Jenis: ${b.jenis_barang}] (Stok Saat Ini: ${b.stok} ${b.satuan})`
                        : b.nama_barang;
                    options += `<option value="${b.nama_barang}" data-satuan="${b.satuan}" data-stok="${b.stok}">${label}</option>`;
                });
                selectElem.innerHTML = options;
                selectElem.value = currentValue;
            });
        }

        // --- MASTER BARANG FUNCTIONS ---
        async function loadMasterBarang() {
            try {
                const res = await fetch('api.php?action=get_barang');
                const result = await res.json();
                if (result.success) {
                    masterBarangList = result.data;
                    renderTableBarang();
                    renderDashboardWidgets();
                    updateAllSelectOptions();
                }
            } catch (err) {
                console.error('Gagal memuat barang:', err);
            }
        }

        function renderTableBarang() {
            const tbody = document.getElementById('barang-table-body');
            tbody.innerHTML = '';

            const statusStok = document.getElementById('barang-filter-stok').value;
            const filtered = masterBarangList.filter(b => {
                const cocokStok = !statusStok || (statusStok === 'habis' ? Number(b.stok) <= 0 : statusStok === 'kurang5' ? Number(b.stok) < 5 : Number(b.stok) > 0);
                return cocokStok;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="p-4 text-center text-xs text-slate-400">Tidak ada barang yang sesuai dengan filter.</td></tr>`;
                return;
            }

            filtered.forEach((b, index) => {
                const tr = document.createElement('tr');
                tr.className = "border-b text-xs hover:bg-slate-50";
                const stokBadge = b.stok <= 0
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 badge-pulse">${b.stok}</span>`
                    : b.stok <= 5
                    ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">${b.stok}</span>`
                    : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">${b.stok}</span>`;
                tr.innerHTML = `
                    <td class="p-3 font-semibold text-slate-500">${index + 1}</td>
                    <td class="p-3 font-mono text-slate-600 text-[11px]">${b.kode_barang || '-'}</td>
                    <td class="p-3 font-semibold text-slate-800">${b.nama_barang}</td>
                    <td class="p-3 text-slate-600">${b.jenis_barang}</td>
                    <td class="p-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-semibold ${b.kondisi === 'Baik' ? 'bg-green-100 text-green-700' : b.kondisi === 'Rusak Sedang' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700'}">${b.kondisi || 'Baik'}</span></td>
                    <td class="p-3">${stokBadge}</td>
                    <td class="p-3 text-slate-600">${b.satuan}</td>
                    <td class="p-3 text-slate-500 text-[11px]">${b.updated_at ? new Date(b.updated_at.replace(' ', 'T')).toLocaleString('id-ID') : '-'}</td>
                    <td class="p-3 text-center">
                        <div class="flex justify-center gap-1">
                            <button onclick="editBarang(${b.id})" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            </button>
                            <button onclick="hapusBarang(${b.id})" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) lucide.createIcons();
        }

        function cetakLaporanMasterBarang() {
            if (!window.jspdf) { alert('Komponen PDF belum tersedia.'); return; }
            const statusStok = document.getElementById('barang-filter-stok').value;
            const rows = masterBarangList.filter(b => {
                const cocokStok = !statusStok || (statusStok === 'habis' ? Number(b.stok) <= 0 : statusStok === 'kurang5' ? Number(b.stok) < 5 : Number(b.stok) > 0);
                return cocokStok;
            });
            if (!rows.length) { alert('Tidak ada data barang sesuai filter untuk dicetak.'); return; }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const statusLabel = statusStok === 'habis' ? 'Stok habis' : statusStok === 'kurang5' ? 'Stok < 5' : statusStok === 'tersedia' ? 'Stok tersedia' : 'Semua status stok';
            doc.setFontSize(14);
            doc.text('Laporan Master Barang', 14, 16);
            doc.setFontSize(10);
            doc.text(`Status: ${statusLabel}`, 14, 23);
            doc.autoTable({ startY: 29, head: [['No.', 'Kode', 'Nama Barang', 'Jenis', 'Kondisi', 'Stok', 'Satuan', 'Terakhir Diubah']], body: rows.map((b, i) => [i + 1, b.kode_barang || '-', b.nama_barang, b.jenis_barang, b.kondisi || 'Baik', b.stok, b.satuan, b.updated_at ? new Date(b.updated_at.replace(' ', 'T')).toLocaleDateString('id-ID') : '-']), theme: 'grid', styles: { fontSize: 8 } });
            doc.save('laporan_master_barang.pdf');
        }

        function exportMasterBarangExcel() {
            if (!window.XLSX) { alert('Komponen Excel belum tersedia.'); return; }
            if (!masterBarangList.length) { alert('Belum ada data master barang untuk diekspor.'); return; }
            const rows = masterBarangList.map(barang => ({
                nama_barang: barang.nama_barang,
                jenis_barang: barang.jenis_barang,
                stok: Number(barang.stok),
                satuan: barang.satuan
            }));
            const worksheet = XLSX.utils.json_to_sheet(rows);
            worksheet['!cols'] = [{ wch: 32 }, { wch: 22 }, { wch: 12 }, { wch: 14 }];
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Master Barang');
            XLSX.writeFile(workbook, `backup_master_barang_${new Date().toISOString().slice(0, 10)}.xlsx`);
        }

        // --- DASHBOARD RENDER LOGIC ---
        function renderDashboardWidgets() {
            document.getElementById('dash-total-barang').innerText = masterBarangList.length;

            const kritis = masterBarangList.filter(b => b.stok <= 5);
            document.getElementById('dash-stok-kritis').innerText = kritis.length;

            const countMasuk = rawTransaksiData.filter(t => (t.tipe || 'KELUAR') === 'MASUK').length;
            const countKeluar = rawTransaksiData.filter(t => (t.tipe || 'KELUAR') === 'KELUAR').length;

            document.getElementById('dash-trans-masuk').innerText = countMasuk;
            document.getElementById('dash-trans-keluar').innerText = countKeluar;

            const alertContainer = document.getElementById('dash-alert-container');
            alertContainer.innerHTML = '';

            if (kritis.length === 0) {
                alertContainer.innerHTML = `
                    <div class="p-4 alert-safe rounded-xl text-sm text-emerald-800 flex items-center gap-3">
                        <div class="p-1.5 bg-emerald-200 rounded-lg flex-shrink-0"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-700"></i></div>
                        <span class="font-medium">Semua stok barang berada dalam kondisi aman (di atas 5 Pcs).</span>
                    </div>
                `;
            } else {
                const daftarTampil = showAllCriticalStock ? kritis : kritis.slice(0, 1);
                daftarTampil.forEach(b => {
                    const isHabis = b.stok == 0;
                    const alertClass = isHabis ? 'alert-critical' : 'alert-warning';
                    const badgeClass = isHabis ? 'bg-rose-600 text-white badge-pulse' : 'bg-amber-500 text-white';
                    const statusText = isHabis ? 'STOK HABIS' : 'STOK TIPIS';
                    const iconColor = isHabis ? 'text-rose-600 bg-rose-100' : 'text-amber-600 bg-amber-100';

                    const div = document.createElement('div');
                    div.className = `p-4 border rounded-xl text-sm flex items-center justify-between ${alertClass}`;
                    div.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="p-1.5 ${iconColor} rounded-lg flex-shrink-0">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800">${b.nama_barang}</span>
                                <span class="text-xs text-slate-500 ml-1">(Kategori: ${b.jenis_barang})</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-bold">${b.stok} ${b.satuan}</span>
                            <span class="px-2.5 py-1 text-[10px] rounded-full font-bold ${badgeClass}">${statusText}</span>
                        </div>
                    `;
                    alertContainer.appendChild(div);
                });

                if (kritis.length > 1) {
                    const toggleButton = document.createElement('button');
                    toggleButton.type = 'button';
                    toggleButton.className = 'text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline flex items-center gap-1 mt-1';
                    toggleButton.innerHTML = showAllCriticalStock
                        ? '<i data-lucide="chevron-up" class="w-3.5 h-3.5"></i> Tampilkan lebih sedikit'
                        : `<i data-lucide="chevron-down" class="w-3.5 h-3.5"></i> Lihat semua ${kritis.length} barang kritis`;
                    toggleButton.addEventListener('click', () => {
                        showAllCriticalStock = !showAllCriticalStock;
                        renderDashboardWidgets();
                    });
                    alertContainer.appendChild(toggleButton);
                }
            }

            const recentTbody = document.getElementById('dash-recent-table-body');
            recentTbody.innerHTML = '';

            const recent = rawTransaksiData.slice(0, 5);
            if (recent.length === 0) {
                recentTbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-xs text-slate-400">Belum ada transaksi.</td></tr>`;
            } else {
                recent.forEach(t => {
                    const tipe = t.tipe || 'KELUAR';
                    const tipeBadge = tipe === 'MASUK' 
                        ? `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">BARANG MASUK</span>`
                        : `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">BARANG KELUAR</span>`;

                    const barangStr = t.items && t.items.length > 0 
                        ? t.items.map(i => `${i.nama_barang} (${i.jumlah} ${i.satuan})`).join(', ')
                        : '-';

                    const tr = document.createElement('tr');
                    tr.className = "border-b text-xs hover:bg-slate-50";
                    tr.innerHTML = `
                        <td class="p-3 font-semibold text-emerald-800">${t.kode_transaksi}</td>
                        <td class="p-3">${tipeBadge}</td>
                        <td class="p-3">${t.tanggal_pengambilan}</td>
                        <td class="p-3 font-medium">${t.nama_pengambil} <span class="text-[10px] text-slate-400">(${t.jabatan_unit})</span></td>
                        <td class="p-3 text-slate-600">${barangStr}</td>
                    `;
                    recentTbody.appendChild(tr);
                });
            }

            if (window.lucide) lucide.createIcons();
        }

        // --- UPLOAD TEMPLATE EXCEL FUNCTIONS ---
        function openExcelModal() {
            document.getElementById('excel-file-input').value = "";
            document.getElementById('modal-excel').classList.remove('hidden');
        }

        function closeExcelModal() {
            document.getElementById('modal-excel').classList.add('hidden');
        }

        function downloadExcelTemplate() {
            const templateData = [
                { nama_barang: 'Spidol Snowman Hitam', jenis_barang: 'ATK', stok: 12, satuan: 'Pcs' },
                { nama_barang: 'Kertas HVS A4 80gr', jenis_barang: 'ATK', stok: 5, satuan: 'Rim' },
                { nama_barang: 'Sapu Ijuk', jenis_barang: 'Kebersihan', stok: 10, satuan: 'Pcs' }
            ];

            const worksheet = XLSX.utils.json_to_sheet(templateData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Master Barang");

            worksheet['!cols'] = [{ wch: 30 }, { wch: 20 }, { wch: 10 }, { wch: 10 }];

            XLSX.writeFile(workbook, "template_master_barang_sipintar.xlsx");
        }

        function processExcelUpload() {
            const fileInput = document.getElementById('excel-file-input');
            const file = fileInput.files[0];

            if (!file) {
                alert('Silakan pilih berkas Excel terlebih dahulu!');
                return;
            }

            const reader = new FileReader();
            reader.onload = async function(e) {
                try {
                    const data = new Uint8Array(e.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const firstSheet = workbook.SheetNames[0];
                    const rows = XLSX.utils.sheet_to_json(workbook.Sheets[firstSheet]);

                    if (rows.length === 0) {
                        alert('Berkas Excel kosong atau format tidak sesuai!');
                        return;
                    }

                    const items = rows.map(r => ({
                        nama_barang: r.nama_barang || r['Nama Barang'] || '',
                        jenis_barang: r.jenis_barang || r['Jenis Barang'] || 'Umum',
                        stok: parseInt(r.stok || r['Stok'] || 0),
                        satuan: r.satuan || r['Satuan'] || 'Pcs'
                    })).filter(item => item.nama_barang !== '');

                    if (items.length === 0) {
                        alert('Tidak ditemukan data barang yang valid pada berkas Excel!');
                        return;
                    }

                    const res = await fetch('api.php?action=save_template_barang', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ items })
                    });

                    const result = await res.json();
                    if (result.success) {
                        alert('Berhasil mengimpor data dari Excel ke Master Barang SIPINTAR!');
                        closeExcelModal();
                        await loadMasterBarang();
                    } else {
                        alert(result.message);
                    }
                } catch (err) {
                    alert('Gagal memproses berkas Excel. Pastikan format kolom sesuai!');
                }
            };
            reader.readAsArrayBuffer(file);
        }

        function openBarangModal() {
            document.getElementById('modal-barang-title').innerText = "Tambah Data Barang";
            document.getElementById('barang-id').value = "";
            document.getElementById('barang-nama').value = "";
            document.getElementById('barang-jenis').value = "";
            document.getElementById('barang-kondisi').value = "Baik";
            document.getElementById('barang-stok').value = "0";
            document.getElementById('barang-satuan').value = "";
            document.getElementById('modal-barang').classList.remove('hidden');
        }

        function closeBarangModal() {
            document.getElementById('modal-barang').classList.add('hidden');
        }

        function editBarang(id) {
            const barang = masterBarangList.find(item => Number(item.id) === Number(id));
            if (!barang) return;
            document.getElementById('modal-barang-title').innerText = "Edit Data Barang";
            document.getElementById('barang-id').value = id;
            document.getElementById('barang-nama').value = barang.nama_barang;
            document.getElementById('barang-jenis').value = barang.jenis_barang;
            document.getElementById('barang-kondisi').value = barang.kondisi || 'Baik';
            document.getElementById('barang-stok').value = barang.stok;
            document.getElementById('barang-satuan').value = barang.satuan;
            document.getElementById('modal-barang').classList.remove('hidden');
        }

        async function handleSaveMasterBarang(e) {
            e.preventDefault();
            const id = document.getElementById('barang-id').value;
            const nama = document.getElementById('barang-nama').value;
            const jenis = document.getElementById('barang-jenis').value;
            const kondisi = document.getElementById('barang-kondisi').value;
            const stok = document.getElementById('barang-stok').value;
            const satuan = document.getElementById('barang-satuan').value;

            try {
                const res = await fetch('api.php?action=save_barang', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, nama_barang: nama, jenis_barang: jenis, kondisi, stok, satuan })
                });
                const result = await res.json();
                if (result.success) {
                    alert(result.message);
                    closeBarangModal();
                    await loadMasterBarang();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal menyimpan barang.');
            }
        }

        async function hapusBarang(id) {
            if (confirm('Yakin ingin menghapus barang ini dari master data?')) {
                try {
                    const res = await fetch('api.php?action=hapus_barang', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        await loadMasterBarang();
                    } else {
                        alert(result.message);
                    }
                } catch (err) {
                    alert('Gagal menghapus barang.');
                }
            }
        }

        // --- BARANG MASUK (RESTOK) FUNCTIONS ---
        function addBarangMasukRow() {
            const tbody = document.getElementById('bm-table-items-body');
            const tr = document.createElement('tr');
            tr.className = "border-b text-xs";
            
            let options = '<option value="">-- Pilih Barang --</option>';
            masterBarangList.forEach(b => {
                options += `<option value="${b.nama_barang}" data-satuan="${b.satuan}" data-stok="${b.stok}">${b.nama_barang} [Jenis: ${b.jenis_barang}] (Stok Saat Ini: ${b.stok} ${b.satuan})</option>`;
            });

            tr.innerHTML = `
                <td class="p-2">
                    <select required class="item-nama w-full px-2 py-1.5 border rounded-lg outline-none bg-white text-xs" onchange="updateSatuanInput(this)">
                        ${options}
                    </select>
                </td>
                <td class="p-2"><input type="number" min="1" value="1" required class="item-jumlah w-full px-2 py-1.5 border rounded-lg outline-none text-center text-xs"></td>
                <td class="p-2"><input type="text" readonly class="item-satuan w-full px-2 py-1.5 border rounded-lg bg-slate-100 outline-none text-xs" placeholder="Satuan"></td>
                <td class="p-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 hover:text-rose-800 p-1.5 hover:bg-rose-50 rounded-lg transition">
                        <i data-lucide="trash" class="w-4 h-4"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            if (window.lucide) lucide.createIcons();
        }

        function updateSatuanInput(selectElem) {
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            const tr = selectElem.closest('tr');
            const satuanInput = tr.querySelector('.item-satuan');
            satuanInput.value = selectedOption.getAttribute('data-satuan') || '';
        }

        function setupSignaturePad(id) {
            const canvas = document.getElementById(id), ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#0f172a'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
            let drawing = false;
            const point = event => { const rect = canvas.getBoundingClientRect(), source = event.touches ? event.touches[0] : event; return { x: (source.clientX - rect.left) * canvas.width / rect.width, y: (source.clientY - rect.top) * canvas.height / rect.height }; };
            const start = event => { event.preventDefault(); drawing = true; const p = point(event); ctx.beginPath(); ctx.moveTo(p.x, p.y); };
            const move = event => { if (!drawing) return; event.preventDefault(); const p = point(event); ctx.lineTo(p.x, p.y); ctx.stroke(); };
            canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', () => drawing = false);
            canvas.addEventListener('touchstart', start, { passive: false }); canvas.addEventListener('touchmove', move, { passive: false }); canvas.addEventListener('touchend', () => drawing = false);
        }

        function clearSignature(id) { const canvas = document.getElementById(id); canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height); }
        function getSignature(id) {
            const canvas = document.getElementById(id), pixels = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
            for (let i = 3; i < pixels.length; i += 4) if (pixels[i]) return canvas.toDataURL('image/png');
            return '';
        }

        function getLogoDataUrl() {
            return new Promise(resolve => {
                const img = new Image();
                img.onload = () => { try { const canvas = document.createElement('canvas'); canvas.width = img.naturalWidth; canvas.height = img.naturalHeight; canvas.getContext('2d').drawImage(img, 0, 0); resolve(canvas.toDataURL('image/png')); } catch (e) { resolve(''); } };
                img.onerror = () => resolve('');
                img.src = 'logo.png';
            });
        }

        async function downloadReceiptPdfByCode(kode) {
            const item = rawTransaksiData.find(t => t.kode_transaksi === kode);
            if (item) await downloadReceiptPdf(item);
        }

        async function downloadReceiptPdf(transaction) {
            if (!window.jspdf) { alert('Komponen PDF belum tersedia.'); return; }
            const { jsPDF } = window.jspdf, doc = new jsPDF(), masuk = (transaction.tipe || 'KELUAR') === 'MASUK';
            const logo = await getLogoDataUrl(); if (logo) doc.addImage(logo, 'PNG', 94, 5, 22, 22);
            doc.setFontSize(16); doc.setFont('helvetica', 'bold'); doc.text(masuk ? 'BUKTI PENERIMAAN BARANG MASUK' : 'BUKTI PENERIMAAN BARANG', 105, 34, { align: 'center' });
            doc.setFontSize(10); doc.setFont('helvetica', 'normal'); doc.text('SIPINTAR - Kemenag Kota Palembang', 105, 41, { align: 'center' }); doc.line(15, 46, 195, 46);
            doc.text(`Kode Transaksi: ${transaction.kode_transaksi}`, 15, 56); doc.text(`Tanggal: ${transaction.tanggal_pengambilan}`, 15, 63);
            doc.text(`${masuk ? 'Petugas Penerima' : 'Nama Penerima'}: ${transaction.nama_pengambil}`, 15, 70); doc.text(`${masuk ? 'Unit' : 'Jabatan'}: ${transaction.jabatan_unit || '-'}`, 15, 77);
            if (transaction.keterangan) doc.text(`Keperluan: ${transaction.keterangan}`, 15, 84, { maxWidth: 175 });
            doc.autoTable({ startY: transaction.keterangan ? 92 : 86, head: [['No.', 'Nama Barang', 'Jumlah', 'Satuan']], body: (transaction.items || []).map((i, n) => [n + 1, i.nama_barang, i.jumlah, i.satuan]), theme: 'grid', styles: { fontSize: 10 } });
            let y = doc.lastAutoTable.finalY + 15;
            if (transaction.tanda_tangan) { if (y > 235) { doc.addPage(); y = 25; } doc.text('Penerima,', 145, y, { align: 'center' }); try { doc.addImage(transaction.tanda_tangan, 'PNG', 120, y + 3, 50, 25); } catch (e) {} doc.text(transaction.nama_pengambil || '', 145, y + 34, { align: 'center' }); }
            doc.save(`Bukti_Penerimaan_${transaction.kode_transaksi}.pdf`);
        }

        async function exportBuktiPenerimaan() {
            const signed = getFilteredData().filter(item => item.tanda_tangan);
            if (!signed.length) { alert('Tidak ada bukti penerimaan bertanda tangan yang sesuai filter.'); return; }
            if (!window.jspdf) { alert('Komponen PDF belum tersedia.'); return; }
            const { jsPDF } = window.jspdf, doc = new jsPDF(), logo = await getLogoDataUrl();
            const filterTipe = document.getElementById('filter-tipe').value;
            const filterBulan = document.getElementById('filter-bulan');
            const filterTahun = document.getElementById('filter-tahun').value.trim();
            const labelTipe = filterTipe === 'MASUK' ? 'Barang Masuk' : filterTipe === 'KELUAR' ? 'Barang Keluar' : 'Semua Transaksi';
            const labelBulan = filterBulan.value ? filterBulan.options[filterBulan.selectedIndex].text : 'Semua Bulan';
            const labelPeriode = `${labelBulan}${filterTahun ? ` ${filterTahun}` : ''}`;
            signed.forEach((transaction, index) => {
                if (index > 0) doc.addPage();
                const masuk = (transaction.tipe || 'KELUAR') === 'MASUK';
                if (logo) doc.addImage(logo, 'PNG', 94, 5, 22, 22);
                doc.setFontSize(16); doc.setFont('helvetica', 'bold'); doc.text(masuk ? 'BUKTI PENERIMAAN BARANG MASUK' : 'BUKTI PENERIMAAN BARANG', 105, 34, { align: 'center' });
                doc.setFontSize(10); doc.setFont('helvetica', 'normal'); doc.text('SIPINTAR - Kemenag Kota Palembang', 105, 41, { align: 'center' });
                doc.setFontSize(9); doc.text(`${labelPeriode} - ${labelTipe}`, 105, 48, { align: 'center' }); doc.line(15, 54, 195, 54);
                doc.setFontSize(10); doc.text(`Kode Transaksi: ${transaction.kode_transaksi}`, 15, 64); doc.text(`Tanggal: ${transaction.tanggal_pengambilan}`, 15, 71);
                doc.text(`${masuk ? 'Petugas Penerima' : 'Nama Penerima'}: ${transaction.nama_pengambil}`, 15, 78); doc.text(`${masuk ? 'Unit' : 'Jabatan'}: ${transaction.jabatan_unit || '-'}`, 15, 85);
                if (transaction.keterangan) doc.text(`Keperluan: ${transaction.keterangan}`, 15, 92, { maxWidth: 175 });
                doc.autoTable({ startY: transaction.keterangan ? 100 : 94, head: [['No.', 'Nama Barang', 'Jumlah', 'Satuan']], body: (transaction.items || []).map((item, i) => [i + 1, item.nama_barang, item.jumlah, item.satuan]), theme: 'grid', styles: { fontSize: 10 } });
                let y = doc.lastAutoTable.finalY + 15; if (y > 235) { doc.addPage(); y = 25; }
                doc.text('Penerima,', 145, y, { align: 'center' });
                try { doc.addImage(transaction.tanda_tangan, 'PNG', 120, y + 3, 50, 25); } catch (error) {}
                doc.text(transaction.nama_pengambil || '', 145, y + 34, { align: 'center' });
            });
            doc.save('Rekap_Bukti_Penerimaan.pdf');
        }

        async function handleSaveBarangMasuk(e) {
            e.preventDefault();
            const signature = getSignature('bm-signature');
            if (!signature) { alert('Tanda tangan petugas penerima wajib diisi.'); return; }
            const rows = document.querySelectorAll('#bm-table-items-body tr');
            if (rows.length === 0) {
                alert('Tambahkan minimal 1 barang masuk!');
                return;
            }

            const items = [];
            rows.forEach(r => {
                const selectElem = r.querySelector('.item-nama');
                items.push({
                    nama_barang: selectElem.value,
                    jumlah: parseInt(r.querySelector('.item-jumlah').value),
                    satuan: r.querySelector('.item-satuan').value
                });
            });

            const selectedDate = document.getElementById('bm_tanggal').value;
            const dateStr = selectedDate.replace(/-/g, '');
            const randomNum = Math.floor(100 + Math.random() * 900);
            const kode = `IN-${dateStr}-${randomNum}`;

            const payload = {
                kode_transaksi: kode,
                tipe: 'MASUK',
                tanggal_pengambilan: selectedDate,
                nama_pengambil: document.getElementById('bm_petugas').value,
                jabatan_unit: 'Petugas Inventaris',
                keterangan: '',
                tanda_tangan: signature,
                items: items
            };

            try {
                const res = await fetch('api.php?action=simpan_transaksi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    downloadReceiptPdf(payload);
                    alert('Barang masuk berhasil disimpan! Stok telah otomatis bertambah.');
                    document.getElementById('form-barang-masuk').reset();
                    document.getElementById('bm_tanggal').valueAsDate = new Date();
                    document.getElementById('bm_petugas').value = '';
                    document.getElementById('bm-table-items-body').innerHTML = '';
                    clearSignature('bm-signature');
                    
                    await loadMasterBarang();
                    await loadTransaksiData();
                    addBarangMasukRow();
                    switchTab('history');
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal menyimpan barang masuk.');
            }
        }

        // --- BARANG KELUAR FUNCTIONS ---
        function addBarangRow() {
            const tbody = document.getElementById('table-items-body');
            const tr = document.createElement('tr');
            tr.className = "border-b text-xs";
            
            let options = '<option value="">-- Pilih Barang --</option>';
            masterBarangList.forEach(b => {
                options += `<option value="${b.nama_barang}" data-satuan="${b.satuan}" data-stok="${b.stok}">${b.nama_barang}</option>`;
            });

            tr.innerHTML = `
                <td class="p-2">
                    <select required class="item-nama w-full px-2 py-1.5 border rounded-lg outline-none bg-white text-xs" onchange="updateSatuanAndStok(this)">
                        ${options}
                    </select>
                </td>
                <td class="p-2"><input type="number" min="1" value="1" required class="item-jumlah w-full px-2 py-1.5 border rounded-lg outline-none text-center text-xs"></td>
                <td class="p-2"><input type="text" readonly class="item-satuan w-full px-2 py-1.5 border rounded-lg bg-slate-100 outline-none text-xs" placeholder="Satuan"></td>
                <td class="p-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 hover:text-rose-800 p-1.5 hover:bg-rose-50 rounded-lg transition">
                        <i data-lucide="trash" class="w-4 h-4"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            if (window.lucide) lucide.createIcons();
        }

        function updateSatuanAndStok(selectElem) {
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            const tr = selectElem.closest('tr');
            const satuanInput = tr.querySelector('.item-satuan');
            const jumlahInput = tr.querySelector('.item-jumlah');

            const satuan = selectedOption.getAttribute('data-satuan') || '';
            const maxStok = parseInt(selectedOption.getAttribute('data-stok') || 0);

            satuanInput.value = satuan;
            if (maxStok > 0) {
                jumlahInput.max = maxStok;
            } else {
                jumlahInput.removeAttribute('max');
            }
        }

        async function handleSaveTransaksi(e) {
            e.preventDefault();
            const signature = getSignature('bk-signature');
            if (!signature) { alert('Tanda tangan penerima wajib diisi.'); return; }
            const rows = document.querySelectorAll('#table-items-body tr');
            if (rows.length === 0) {
                alert('Tambahkan minimal 1 barang!');
                return;
            }

            const items = [];
            let stokValid = true;

            rows.forEach(r => {
                const selectElem = r.querySelector('.item-nama');
                const selectedOption = selectElem.options[selectElem.selectedIndex];
                const maxStok = parseInt(selectedOption.getAttribute('data-stok') || 0);
                const jml = parseInt(r.querySelector('.item-jumlah').value);

                if (maxStok > 0 && jml > maxStok) {
                    alert(`Stok untuk barang "${selectElem.value}" tidak mencukupi! (Maksimal stok: ${maxStok})`);
                    stokValid = false;
                    return;
                }

                items.push({
                    nama_barang: selectElem.value,
                    jumlah: jml,
                    satuan: r.querySelector('.item-satuan').value
                });
            });

            if (!stokValid) return;

            const selectedDate = document.getElementById('tanggal_pengambilan').value;
            const dateStr = selectedDate.replace(/-/g, '');
            const randomNum = Math.floor(100 + Math.random() * 900);
            const kode = `BRG-${dateStr}-${randomNum}`;

            const payload = {
                kode_transaksi: kode,
                tipe: 'KELUAR',
                tanggal_pengambilan: selectedDate,
                nama_pengambil: document.getElementById('nama_pengambil').value,
                jabatan_unit: document.getElementById('jabatan_unit').value,
                keterangan: document.getElementById('keterangan').value,
                tanda_tangan: signature,
                items: items
            };

            try {
                const res = await fetch('api.php?action=simpan_transaksi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    downloadReceiptPdf(payload);
                    alert('Transaksi Penerimaan Berhasil Disimpan!');
                    document.getElementById('form-pengambilan').reset();
                    document.getElementById('tanggal_pengambilan').valueAsDate = new Date();
                    document.getElementById('table-items-body').innerHTML = '';
                    clearSignature('bk-signature');
                    
                    await loadMasterBarang();
                    await loadTransaksiData();
                    addBarangRow();
                    switchTab('history');
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal menyimpan data ke database.');
            }
        }

        // --- HISTORY & REPORT FUNCTIONS ---
        async function loadTransaksiData() {
            try {
                const res = await fetch('api.php?action=get_transaksi');
                const result = await res.json();
                if (result.success) {
                    rawTransaksiData = result.data;
                    renderTableHistory();
                    renderDashboardWidgets();
                }
            } catch (err) {
                console.error('Gagal memuat data:', err);
            }
        }

        function getFilteredData() {
            const tipe = document.getElementById('filter-tipe').value;
            const search = document.getElementById('filter-search').value.toLowerCase();
            const bulan = document.getElementById('filter-bulan').value;
            const tahun = document.getElementById('filter-tahun').value;

            return rawTransaksiData.filter(item => {
                const itemTipe = item.tipe || 'KELUAR';
                const itemDate = new Date(item.tanggal_pengambilan);
                const itemBulan = String(itemDate.getMonth() + 1).padStart(2, '0');
                const itemTahun = String(itemDate.getFullYear());

                const matchTipe = !tipe || itemTipe === tipe;

                const matchSearch = item.kode_transaksi.toLowerCase().includes(search) ||
                                    item.nama_pengambil.toLowerCase().includes(search) ||
                                    item.jabatan_unit.toLowerCase().includes(search) ||
                                    (item.items && item.items.some(i => i.nama_barang.toLowerCase().includes(search)));

                const matchBulan = !bulan || itemBulan === bulan;
                const matchTahun = !tahun || itemTahun === tahun;

                return matchTipe && matchSearch && matchBulan && matchTahun;
            });
        }

        function renderTableHistory() {
            const tbody = document.getElementById('history-table-body');
            const filtered = getFilteredData();
            tbody.innerHTML = '';

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="p-4 text-center text-xs text-slate-400">Tidak ada data transaksi yang cocok dengan filter.</td></tr>`;
                return;
            }

            filtered.forEach(item => {
                const tipe = item.tipe || 'KELUAR';
                const tipeBadge = tipe === 'MASUK' 
                    ? `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">BARANG MASUK</span>`
                    : `<span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">BARANG KELUAR</span>`;

                const pihakSubtext = tipe === 'MASUK' ? 'Petugas Penerima' : 'Jabatan';

                const barangList = item.items && item.items.length > 0 
                    ? item.items.map(i => `• ${i.nama_barang} (${i.jumlah} ${i.satuan})`).join('<br>') 
                    : '-';

                const tr = document.createElement('tr');
                tr.className = "border-b text-xs hover:bg-slate-50";
                tr.innerHTML = `
                    <td class="p-3 font-semibold text-emerald-800">${item.kode_transaksi}</td>
                    <td class="p-3">${tipeBadge}</td>
                    <td class="p-3">${item.tanggal_pengambilan}</td>
                    <td class="p-3"><strong>${item.nama_pengambil}</strong><br><span class="text-[10px] text-slate-500">${pihakSubtext}: ${item.jabatan_unit}</span></td>
                    <td class="p-3">${barangList}</td>
                    <td class="p-3 text-slate-500">${item.keterangan || '-'}</td>
                    <td class="p-3 text-center">
                        <div class="flex justify-center gap-1">
                            <button onclick="downloadReceiptPdfByCode('${item.kode_transaksi}')" class="p-1.5 text-rose-600 hover:text-rose-800 hover:bg-rose-50 rounded-lg transition" title="Unduh bukti penerimaan PDF"><i data-lucide="file-down" class="w-3.5 h-3.5"></i></button>
                            <button onclick="editTransaksi('${item.kode_transaksi}')" class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition" title="Edit Transaksi">
                                <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) lucide.createIcons();
        }

        function editTransaksi(kode) {
            const transaksi = rawTransaksiData.find(t => t.kode_transaksi === kode);
            if (!transaksi) return;

            document.getElementById('edit_kode_transaksi').value = transaksi.kode_transaksi;
            document.getElementById('edit_tipe_transaksi').value = transaksi.tipe || 'KELUAR';
            document.getElementById('edit_tanggal').value = transaksi.tanggal_pengambilan;
            document.getElementById('edit_nama_pengambil').value = transaksi.nama_pengambil;
            document.getElementById('edit_jabatan_unit').value = transaksi.jabatan_unit || '';
            document.getElementById('edit_keterangan').value = transaksi.keterangan || '';

            if ((transaksi.tipe || 'KELUAR') === 'MASUK') {
                document.getElementById('edit_label_pengambil').innerText = "Petugas Penerima";
                document.getElementById('edit_label_jabatan').innerText = "Keterangan/Unit";
            } else {
                document.getElementById('edit_label_pengambil').innerText = "Nama Penerima";
                document.getElementById('edit_label_jabatan').innerText = "Jabatan";
            }

            const tbody = document.getElementById('edit-table-items-body');
            tbody.innerHTML = '';

            if (transaksi.items && transaksi.items.length > 0) {
                transaksi.items.forEach(i => {
                    addEditBarangRow(i.nama_barang, i.jumlah, i.satuan);
                });
            } else {
                addEditBarangRow();
            }

            document.getElementById('modal-edit-transaksi').classList.remove('hidden');
        }

        function closeEditTransaksiModal() {
            document.getElementById('modal-edit-transaksi').classList.add('hidden');
        }

        function addEditBarangRow(nama = '', jumlah = 1, satuan = '') {
            const tbody = document.getElementById('edit-table-items-body');
            const tr = document.createElement('tr');
            tr.className = "border-b text-xs";

            let options = '<option value="">-- Pilih Barang --</option>';
            masterBarangList.forEach(b => {
                const selected = b.nama_barang === nama ? 'selected' : '';
                options += `<option value="${b.nama_barang}" data-satuan="${b.satuan}" ${selected}>${b.nama_barang}</option>`;
            });

            tr.innerHTML = `
                <td class="p-1.5">
                    <select required class="item-nama w-full px-2 py-1 border rounded-lg outline-none bg-white" onchange="updateEditSatuan(this)">
                        ${options}
                    </select>
                </td>
                <td class="p-1.5"><input type="number" min="1" value="${jumlah}" required class="item-jumlah w-full px-2 py-1 border rounded-lg outline-none text-center"></td>
                <td class="p-1.5"><input type="text" readonly value="${satuan}" class="item-satuan w-full px-2 py-1 border rounded-lg bg-slate-100 outline-none"></td>
                <td class="p-1.5 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 hover:text-rose-800 p-1 hover:bg-rose-50 rounded-lg transition">
                        <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            if (window.lucide) lucide.createIcons();
        }

        function updateEditSatuan(selectElem) {
            const selectedOption = selectElem.options[selectElem.selectedIndex];
            const tr = selectElem.closest('tr');
            const satuanInput = tr.querySelector('.item-satuan');
            satuanInput.value = selectedOption.getAttribute('data-satuan') || '';
        }

        async function handleSaveEditTransaksi(e) {
            e.preventDefault();
            const rows = document.querySelectorAll('#edit-table-items-body tr');
            if (rows.length === 0) {
                alert('Tambahkan minimal 1 barang!');
                return;
            }

            const items = [];
            rows.forEach(r => {
                const selectElem = r.querySelector('.item-nama');
                items.push({
                    nama_barang: selectElem.value,
                    jumlah: parseInt(r.querySelector('.item-jumlah').value),
                    satuan: r.querySelector('.item-satuan').value
                });
            });

            const payload = {
                kode_transaksi: document.getElementById('edit_kode_transaksi').value,
                tanggal_pengambilan: document.getElementById('edit_tanggal').value,
                nama_pengambil: document.getElementById('edit_nama_pengambil').value,
                jabatan_unit: document.getElementById('edit_jabatan_unit').value,
                keterangan: document.getElementById('edit_keterangan').value,
                items: items
            };

            try {
                const res = await fetch('api.php?action=update_transaksi', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();

                if (result.success) {
                    alert('Transaksi berhasil diperbarui!');
                    closeEditTransaksiModal();
                    await loadMasterBarang();
                    await loadTransaksiData();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal memperbarui transaksi.');
            }
        }

        async function handleHapusTerfilter() {
            const filtered = getFilteredData();
            if (filtered.length === 0) {
                alert('Tidak ada data yang cocok dengan filter aktif!');
                return;
            }

            if (confirm(`Apakah Anda yakin ingin MENGHAPUS PERMANEN ${filtered.length} data transaksi yang sedang tampil?`)) {
                const kodes = filtered.map(f => f.kode_transaksi);
                try {
                    const res = await fetch('api.php?action=hapus_transaksi', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ kodes: kodes })
                    });
                    const result = await res.json();
                    if (result.success) {
                        alert(result.message);
                        loadTransaksiData();
                    } else {
                        alert(result.message);
                    }
                } catch (err) {
                    alert('Gagal menghapus data.');
                }
            }
        }

        function exportPDF() {
            const filtered = getFilteredData();
            if (filtered.length === 0) {
                alert('Tidak ada data untuk dicetak!');
                return;
            }

            const filterTipeVal = document.getElementById('filter-tipe').value;
            const filterBulanElem = document.getElementById('filter-bulan');
            const filterTahunVal = document.getElementById('filter-tahun').value.trim();
            const bulanText = filterBulanElem.options[filterBulanElem.selectedIndex].text;

            let jenisLaporan = 'Laporan Rekapitulasi Inventaris Barang';
            if (filterTipeVal === 'MASUK') jenisLaporan = 'Laporan Rekapitulasi Barang Masuk (Restok)';
            if (filterTipeVal === 'KELUAR') jenisLaporan = 'Laporan Rekapitulasi Barang Keluar (Penerimaan)';

            let wktuText = '';
            if (filterBulanElem.value !== '') {
                wktuText += ` Bulan ${bulanText}`;
            }
            if (filterTahunVal !== '') {
                wktuText += ` ${filterTahunVal}`;
            }

            const judulLaporan = `${jenisLaporan}${wktuText}`;

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            const logoImg = document.getElementById('app-logo-img');
            if (logoImg && logoImg.complete && logoImg.naturalWidth !== 0) {
                try {
                    doc.addImage(logoImg, 'PNG', 14, 8, 16, 16);
                } catch (e) {
                    console.log("Logo skip load");
                }
            }

            doc.setFontSize(14);
            doc.setFont('helvetica', 'bold');
            doc.text('KEMENAG KOTA PALEMBANG', 110, 14, { align: 'center' });
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.text(`${judulLaporan} (SIPINTAR)`, 110, 20, { align: 'center' });
            doc.setLineWidth(0.5);
            doc.line(14, 26, 196, 26);

            const tableRows = [];
            filtered.forEach((item, index) => {
                const barangStr = item.items && item.items.length > 0
                    ? item.items.map(i => `${i.nama_barang} (${i.jumlah} ${i.satuan})`).join(', ') 
                    : '-';

                tableRows.push([
                    index + 1,
                    item.kode_transaksi,
                    item.tipe || 'KELUAR',
                    item.tanggal_pengambilan,
                    `${item.nama_pengambil}\n(${item.jabatan_unit})`,
                    barangStr,
                    item.keterangan || '-'
                ]);
            });

            doc.autoTable({
                startY: 29,
                head: [['No', 'Kode', 'Tipe', 'Tanggal', 'Pihak Terkait', 'Rincian Barang', 'Ket']],
                body: tableRows,
                theme: 'grid',
                headStyles: { fillColor: [6, 95, 70] },
                styles: { fontSize: 8, cellPadding: 3 }
            });

            let finalY = doc.lastAutoTable.finalY + 12;

            if (finalY > 240) {
                doc.addPage();
                finalY = 25;
            }

            const todayStr = new Date().toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });

            doc.setFontSize(9);
            doc.setFont('helvetica', 'normal');

            doc.text('Mengetahui,', 20, finalY);
            doc.text('Kepala Tata Usaha Kemenag Kota Palembang', 20, finalY + 5);

            doc.text(`Palembang, ${todayStr}`, 135, finalY);
            doc.text('Petugas Pengelola Inventaris,', 135, finalY + 5);

            const ttdY = finalY + 30;

            doc.setFont('helvetica', 'bold');
            doc.text('( .................................................... )', 20, ttdY);
            doc.setFont('helvetica', 'normal');
            doc.text('NIP. ...............................................', 20, ttdY + 5);

            doc.setFont('helvetica', 'bold');
            doc.text('( .................................................... )', 135, ttdY);
            doc.setFont('helvetica', 'normal');
            doc.text('NIP. ...............................................', 135, ttdY + 5);

            doc.save(`Laporan_SIPINTAR_${new Date().toISOString().slice(0, 10)}.pdf`);
        }

        function exportExcel() {
            const filtered = getFilteredData();
            if (filtered.length === 0) {
                alert('Tidak ada data untuk diexport!');
                return;
            }

            const excelData = [];
            filtered.forEach((item, index) => {
                const barangStr = item.items && item.items.length > 0 
                    ? item.items.map(i => `${i.nama_barang} (${i.jumlah} ${i.satuan})`).join('; ') 
                    : '-';

                excelData.push({
                    'No': index + 1,
                    'Kode Transaksi': item.kode_transaksi,
                    'Tipe Transaksi': item.tipe || 'KELUAR',
                    'Tanggal Transaksi': item.tanggal_pengambilan,
                    'Penerima / Petugas': item.nama_pengambil,
                    'Jabatan': item.jabatan_unit,
                    'Rincian Barang': barangStr,
                    'Keterangan': item.keterangan || '-'
                });
            });

            const worksheet = XLSX.utils.json_to_sheet(excelData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "Laporan Inventaris");

            worksheet['!cols'] = [
                { wch: 5 },
                { wch: 20 },
                { wch: 15 },
                { wch: 18 },
                { wch: 22 },
                { wch: 20 },
                { wch: 45 },
                { wch: 25 }
            ];

            XLSX.writeFile(workbook, `Laporan_SIPINTAR_${new Date().toISOString().slice(0, 10)}.xlsx`);
        }

        function switchTab(tab) {
            document.getElementById('section-dashboard').classList.add('hidden');
            document.getElementById('section-barang-masuk').classList.add('hidden');
            document.getElementById('section-form').classList.add('hidden');
            document.getElementById('section-master-barang').classList.add('hidden');
            document.getElementById('section-history').classList.add('hidden');
            document.getElementById('section-akun').classList.add('hidden');

            const inactiveStyle = "tab-btn px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-slate-500";
            const activeStyle = "tab-btn tab-active px-4 py-2.5 text-sm font-semibold flex items-center gap-2 whitespace-nowrap text-white";

            document.getElementById('tab-dashboard').className = inactiveStyle;
            document.getElementById('tab-barang-masuk').className = inactiveStyle;
            document.getElementById('tab-form').className = inactiveStyle;
            document.getElementById('tab-master-barang').className = inactiveStyle;
            document.getElementById('tab-history').className = inactiveStyle;
            document.getElementById('tab-akun').className = inactiveStyle;

            if (tab === 'dashboard') {
                document.getElementById('section-dashboard').classList.remove('hidden');
                document.getElementById('tab-dashboard').className = activeStyle;
            } else if (tab === 'barang_masuk') {
                document.getElementById('section-barang-masuk').classList.remove('hidden');
                document.getElementById('tab-barang-masuk').className = activeStyle;
            } else if (tab === 'form') {
                document.getElementById('section-form').classList.remove('hidden');
                document.getElementById('tab-form').className = activeStyle;
            } else if (tab === 'master_barang') {
                document.getElementById('section-master-barang').classList.remove('hidden');
                document.getElementById('tab-master-barang').className = activeStyle;
            } else if (tab === 'history') {
                document.getElementById('section-history').classList.remove('hidden');
                document.getElementById('tab-history').className = activeStyle;
            } else if (tab === 'akun') {
                document.getElementById('section-akun').classList.remove('hidden');
                document.getElementById('tab-akun').className = activeStyle;
                if (sessionStorage.getItem('sipintar_role') === 'admin') loadAccounts();
            }
            document.getElementById('tab-akun').classList.toggle('hidden', !['admin', 'pegawai'].includes(sessionStorage.getItem('sipintar_role')));
        }
    </script>
</body>
</html>
