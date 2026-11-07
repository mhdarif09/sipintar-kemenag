<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPINTAR - Mode Pemantau</title>
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
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ---- Animated background shapes ---- */
        .login-bg {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 30%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.08;
            animation: float 8s ease-in-out infinite;
        }
        .shape-1 { width: 320px; height: 320px; background: #34d399; top: -80px; left: -80px; animation-delay: 0s; }
        .shape-2 { width: 200px; height: 200px; background: #6366f1; bottom: -60px; right: -40px; animation-delay: 2s; }
        .shape-3 { width: 140px; height: 140px; background: #10b981; bottom: 30%; left: 10%; animation-delay: 4s; }
        .shape-4 { width: 100px; height: 100px; background: #818cf8; top: 25%; right: 12%; animation-delay: 1s; }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-24px) scale(1.06); }
        }

        /* ---- Login card fade-in ---- */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(32px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .login-card { animation: fadeInUp 0.65s cubic-bezier(.22,.68,0,1.2) both; }

        /* ---- Glassmorphism card ---- */
        .glass {
            background: rgba(255,255,255,0.08);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.15);
        }

        /* ---- Header gradient ---- */
        .header-gradient {
            background: linear-gradient(90deg, #065f46 0%, #047857 60%, #0f4c35 100%);
        }

        /* ---- Stat cards gradients ---- */
        .card-blue   { background: linear-gradient(135deg, #1e40af, #3b82f6); }
        .card-amber  { background: linear-gradient(135deg, #92400e, #f59e0b); }
        .card-emerald{ background: linear-gradient(135deg, #065f46, #10b981); }
        .card-rose   { background: linear-gradient(135deg, #9f1239, #f43f5e); }

        /* ---- Number counter animation ---- */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stat-number { animation: countUp 0.5s ease both; }

        /* ---- Pulse badge (stok habis) ---- */
        .pulse-badge {
            animation: pulse-badge 1.5s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.65; transform: scale(1.05); }
        }

        /* ---- Tab active indicator ---- */
        .tab-btn {
            position: relative;
            transition: color .2s, background .2s;
        }
        .tab-btn.active {
            color: #065f46;
            font-weight: 700;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0; left: 8px; right: 8px;
            height: 3px;
            background: linear-gradient(90deg, #059669, #34d399);
            border-radius: 99px 99px 0 0;
        }

        /* ---- Table striped ---- */
        .striped-table tbody tr:nth-child(even) { background: #f8fafc; }
        .striped-table tbody tr:hover { background: #ecfdf5; transition: background .15s; }

        /* ---- Section fade ---- */
        @keyframes sectionFade {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .section-visible { animation: sectionFade .3s ease both; }

        /* ---- Scrollbar ---- */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #34d399; border-radius: 99px; }

        /* ---- Monitor badge ---- */
        .monitor-badge {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #fff;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em;
            padding: 3px 10px;
            border-radius: 99px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 10px rgba(99,102,241,.45);
            animation: fadeInUp 1s .3s both;
        }

        /* ---- Avatar initials ---- */
        .avatar {
            background: linear-gradient(135deg, #34d399, #059669);
            color: #fff;
            font-weight: 800;
            font-size: 13px;
            width: 36px; height: 36px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,.2);
        }

        /* ---- Realtime clock ---- */
        #realtime-clock { font-variant-numeric: tabular-nums; letter-spacing: .04em; }

        /* ---- Alert stok kritis card ---- */
        .alert-stok { transition: box-shadow .2s; }
        .alert-stok:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }

        /* ---- Input focus ---- */
        input:focus, select:focus { outline: none; box-shadow: 0 0 0 3px rgba(16,185,129,.25); border-color: #10b981; }

        /* ---- Footer ---- */
        .app-footer {
            background: linear-gradient(90deg, #064e3b, #0f172a);
            color: #6ee7b7;
        }

        /* ---- Read-only ribbon ---- */
        .readonly-ribbon {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            color: #e0e7ff;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .1em;
            padding: 2px 10px;
            border-radius: 99px;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

    <!-- ============================================================
         LOGIN SCREEN FOR PEMANTAU
    ============================================================ -->
    <div id="login-screen" class="login-bg fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Animated shapes -->
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
        <div class="shape shape-4"></div>

        <div class="login-card glass w-full max-w-sm rounded-3xl shadow-2xl overflow-hidden">
            <!-- Card header -->
            <div class="flex flex-col items-center pt-10 pb-6 px-8 gap-3">
                <!-- Logo -->
                <div class="relative">
                    <div class="w-24 h-24 bg-white rounded-2xl flex items-center justify-center shadow-xl p-1.5 overflow-hidden">
                        <img id="login-logo-img" src="logo.png" alt="Logo Kemenag Kota Palembang"
                             class="w-full h-full object-contain rounded-xl"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-emerald-800 font-bold text-xs text-center\'>KEMENAG</div>';">
                    </div>
                    <!-- Dot indicator -->
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-emerald-900 rounded-full"></span>
                </div>

                <!-- Title -->
                <div class="text-center">
                    <h1 class="text-2xl font-extrabold text-white tracking-tight">SIPINTAR</h1>
                    <p class="text-emerald-300 text-xs mt-0.5">Sistem Pengelolaan Inventaris<br>Kemenag Kota Palembang</p>
                </div>

                <!-- Monitor Badge -->
                <div class="monitor-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    MODE PEMANTAU
                </div>
            </div>

            <!-- Form -->
            <div class="px-8 pb-10 space-y-4">
                <form id="form-login" onsubmit="handleLogin(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-emerald-200 mb-1.5">Username</label>
                        <input type="text" id="login-username" required
                               class="w-full px-4 py-2.5 bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl text-sm focus:bg-white/15 transition"
                               placeholder="Masukkan username pemantau">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-200 mb-1.5">Password</label>
                        <input type="password" id="login-password" required
                               class="w-full px-4 py-2.5 bg-white/10 border border-white/20 text-white placeholder-white/40 rounded-xl text-sm focus:bg-white/15 transition"
                               placeholder="Masukkan password">
                    </div>
                    <button type="submit"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 active:bg-emerald-600 text-white py-3 rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-900/40 flex items-center justify-center gap-2 mt-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk sebagai Pemantau
                    </button>
                </form>

                <div class="relative my-1 text-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-white/15"></div></div>
                    <span class="relative bg-transparent px-3 text-[11px] text-white/40">atau</span>
                </div>

                <a href="index.php"
                   class="w-full border border-white/20 hover:bg-white/10 text-white/70 hover:text-white py-2.5 rounded-xl font-medium text-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali ke Halaman Utama
                </a>
            </div>
        </div>
    </div>

    <!-- ============================================================
         MAIN APP DASHBOARD (Hidden by default)
    ============================================================ -->
    <div id="app-dashboard" class="hidden min-h-screen flex flex-col">

        <!-- HEADER / NAVBAR -->
        <header class="header-gradient text-white shadow-xl sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center gap-4">
                <!-- Left: logo + title -->
                <div class="flex items-center gap-3 min-w-0">
                    <div class="p-1 bg-white rounded-xl w-11 h-11 flex items-center justify-center overflow-hidden flex-shrink-0 shadow-md">
                        <img id="app-logo-img" src="logo.png" alt="Logo Kemenag Kota Palembang"
                             class="w-full h-full object-contain"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-emerald-800 font-bold text-[9px] text-center\'>KEMENAG</div>';">
                    </div>
                    <div class="min-w-0">
                        <h1 class="font-extrabold text-lg leading-tight">SIPINTAR</h1>
                        <p class="text-[11px] text-emerald-200 leading-tight truncate">Sistem Pengelolaan Inventaris Kemenag Kota Palembang</p>
                    </div>
                </div>

                <!-- Right: clock, badge, user, logout -->
                <div class="flex items-center gap-3 flex-shrink-0">
                    <!-- Realtime clock -->
                    <div class="hidden md:flex flex-col items-end">
                        <span id="realtime-clock" class="text-sm font-bold text-emerald-100">--:--:--</span>
                        <span id="realtime-date" class="text-[10px] text-emerald-300"></span>
                    </div>

                    <!-- READ ONLY badge -->
                    <span class="readonly-ribbon hidden sm:inline-flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        READ ONLY
                    </span>

                    <!-- User badge -->
                    <div class="hidden sm:flex items-center gap-2">
                        <div class="avatar" id="user-avatar-initials">P</div>
                        <div class="text-right">
                            <p id="user-name" class="text-xs font-bold text-emerald-100 leading-tight">Pemantau</p>
                            <p class="text-[10px] text-emerald-300 leading-tight">Mode Monitoring</p>
                        </div>
                    </div>

                    <!-- Logout -->
                    <button onclick="handleLogout()"
                            class="bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white px-3 py-2 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow-md transition">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span class="hidden xs:inline">Keluar</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="max-w-7xl mx-auto w-full px-4 py-6 space-y-6 flex-1">

            <!-- TAB NAVIGATION -->
            <div class="-mx-4 -mt-6 mb-6 flex max-w-none items-center gap-0.5 overflow-x-auto bg-white border-b border-slate-200 px-4 shadow-sm">
                <button onclick="switchTab('dashboard')" id="tab-dashboard"
                        class="tab-btn active px-5 py-3.5 text-sm font-semibold text-slate-500 hover:text-emerald-700 flex items-center gap-2 whitespace-nowrap relative">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                </button>
                <button onclick="switchTab('history')" id="tab-history"
                        class="tab-btn px-5 py-3.5 text-sm font-semibold text-slate-500 hover:text-emerald-700 flex items-center gap-2 whitespace-nowrap relative">
                    <i data-lucide="chart-no-axes-column" class="w-4 h-4"></i> Laporan
                </button>
                <button onclick="switchTab('master_barang')" id="tab-master-barang"
                        class="tab-btn px-5 py-3.5 text-sm font-semibold text-slate-500 hover:text-emerald-700 flex items-center gap-2 whitespace-nowrap relative">
                    <i data-lucide="package" class="w-4 h-4"></i> Master Barang
                </button>
                <button onclick="switchTab('akun')" id="tab-akun"
                        class="tab-btn px-5 py-3.5 text-sm font-semibold text-slate-500 hover:text-emerald-700 flex items-center gap-2 whitespace-nowrap relative">
                    <i data-lucide="key-round" class="w-4 h-4"></i> Akun Saya
                </button>
            </div>

            <!-- ================================================
                 SECTION 0: DASHBOARD STATISTIK
            ================================================ -->
            <section id="section-dashboard" class="space-y-6 section-visible">

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Jenis Barang -->
                    <div class="card-blue rounded-2xl p-5 shadow-xl shadow-blue-900/20 flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs font-semibold text-blue-100 uppercase tracking-wide">Total Jenis Barang</p>
                            <h3 id="dash-total-barang" class="text-3xl font-extrabold mt-1 stat-number">0</h3>
                            <p class="text-[11px] text-blue-200 mt-0.5">jenis barang terdaftar</p>
                        </div>
                        <div class="p-3.5 bg-white/15 rounded-xl">
                            <i data-lucide="boxes" class="w-7 h-7 text-white"></i>
                        </div>
                    </div>

                    <!-- Stok Kritis -->
                    <div class="card-amber rounded-2xl p-5 shadow-xl shadow-amber-900/20 flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs font-semibold text-amber-100 uppercase tracking-wide">Stok Kritis (≤ 5)</p>
                            <h3 id="dash-stok-kritis" class="text-3xl font-extrabold mt-1 stat-number">0</h3>
                            <p class="text-[11px] text-amber-200 mt-0.5">perlu segera diisi ulang</p>
                        </div>
                        <div class="p-3.5 bg-white/15 rounded-xl">
                            <i data-lucide="alert-triangle" class="w-7 h-7 text-white"></i>
                        </div>
                    </div>

                    <!-- Transaksi Masuk -->
                    <div class="card-emerald rounded-2xl p-5 shadow-xl shadow-emerald-900/20 flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs font-semibold text-emerald-100 uppercase tracking-wide">Transaksi Masuk</p>
                            <h3 id="dash-trans-masuk" class="text-3xl font-extrabold mt-1 stat-number">0</h3>
                            <p class="text-[11px] text-emerald-200 mt-0.5">total restok barang</p>
                        </div>
                        <div class="p-3.5 bg-white/15 rounded-xl">
                            <i data-lucide="arrow-down-left" class="w-7 h-7 text-white"></i>
                        </div>
                    </div>

                    <!-- Transaksi Keluar -->
                    <div class="card-rose rounded-2xl p-5 shadow-xl shadow-rose-900/20 flex items-center justify-between text-white">
                        <div>
                            <p class="text-xs font-semibold text-rose-100 uppercase tracking-wide">Transaksi Keluar</p>
                            <h3 id="dash-trans-keluar" class="text-3xl font-extrabold mt-1 stat-number">0</h3>
                            <p class="text-[11px] text-rose-200 mt-0.5">total penerimaan barang</p>
                        </div>
                        <div class="p-3.5 bg-white/15 rounded-xl">
                            <i data-lucide="arrow-up-right" class="w-7 h-7 text-white"></i>
                        </div>
                    </div>
                </div>

                <!-- Alert Stok Kritis -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6">
                    <h2 class="text-sm font-bold text-slate-700 mb-4 flex items-center gap-2">
                        <span class="p-2 bg-amber-50 rounded-lg"><i data-lucide="bell" class="w-4 h-4 text-amber-500"></i></span>
                        Peringatan Notifikasi Stok Kritis
                    </h2>
                    <div id="dash-alert-container" class="space-y-3"></div>
                </div>

                <!-- Transaksi Terbaru -->
                <div class="bg-white rounded-2xl shadow-md border border-slate-100 p-6">
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                            <span class="p-2 bg-emerald-50 rounded-lg"><i data-lucide="clock" class="w-4 h-4 text-emerald-600"></i></span>
                            Transaksi Terbaru
                        </h2>
                        <button onclick="switchTab('history')"
                                class="text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition">
                            Lihat Semua →
                        </button>
                    </div>
                    <div class="overflow-x-auto rounded-xl border border-slate-100">
                        <table class="w-full text-sm text-left striped-table">
                            <thead>
                                <tr class="bg-gradient-to-r from-slate-800 to-slate-700 text-white">
                                    <th class="px-4 py-3 text-xs font-semibold tracking-wide rounded-tl-xl">Kode Transaksi</th>
                                    <th class="px-4 py-3 text-xs font-semibold tracking-wide">Tipe</th>
                                    <th class="px-4 py-3 text-xs font-semibold tracking-wide">Tanggal</th>
                                    <th class="px-4 py-3 text-xs font-semibold tracking-wide">Pihak Terkait</th>
                                    <th class="px-4 py-3 text-xs font-semibold tracking-wide rounded-tr-xl">Rincian Barang</th>
                                </tr>
                            </thead>
                            <tbody id="dash-recent-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- ================================================
                 SECTION 1: RIWAYAT & LAPORAN REKAP
            ================================================ -->
            <section id="section-history" class="hidden bg-white rounded-2xl shadow-md border border-slate-100 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-800">Riwayat &amp; Laporan Inventaris</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Pemantauan data transaksi barang masuk &amp; keluar serta pencetakan laporan.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="exportBuktiPenerimaan()"
                                class="bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-indigo-200 transition">
                            <i data-lucide="file-down" class="w-4 h-4"></i> Rekap Bukti Penerimaan
                        </button>
                        <button onclick="exportPDF()"
                                class="bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-rose-200 transition">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Cetak PDF
                        </button>
                        <button onclick="exportExcel()"
                                class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-emerald-200 transition">
                            <i data-lucide="sheet" class="w-4 h-4"></i> Cetak Excel
                        </button>
                    </div>
                </div>

                <!-- FILTER CONTROLS -->
                <p class="mb-3 text-[11px] text-slate-400 italic">Filter bulan, tahun, dan tipe transaksi di bawah juga diterapkan pada Rekap Bukti Penerimaan.</p>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-5 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Tipe Transaksi</label>
                        <select id="filter-tipe" onchange="renderTableHistory()"
                                class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white text-slate-700 outline-none transition">
                            <option value="">Semua Transaksi</option>
                            <option value="MASUK">Barang Masuk (Restok)</option>
                            <option value="KELUAR">Barang Keluar (Penerimaan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Pencarian</label>
                        <input type="text" id="filter-search" oninput="renderTableHistory()"
                               placeholder="Cari Kode/Pihak/Barang..."
                               class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white text-slate-700 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Bulan</label>
                        <select id="filter-bulan" onchange="renderTableHistory()"
                                class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white text-slate-700 outline-none transition">
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
                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase tracking-wide">Tahun</label>
                        <input type="number" id="filter-tahun" oninput="renderTableHistory()"
                               placeholder="Contoh: 2026"
                               class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg bg-white text-slate-700 outline-none transition">
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-sm text-left striped-table">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-800 to-slate-700 text-white">
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide rounded-tl-xl">Kode Transaksi</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide">Tipe</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide">Penerima / Petugas</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide">Rincian Barang</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide">Keterangan</th>
                                <th class="px-4 py-3 text-xs font-semibold tracking-wide text-center rounded-tr-xl">Bukti</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body"></tbody>
                    </table>
                </div>
            </section>

            <!-- ================================================
                 SECTION 2: MASTER BARANG
            ================================================ -->
            <section id="section-master-barang" class="hidden bg-white rounded-2xl shadow-md border border-slate-100 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-5 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-800">Master Data &amp; Stok Barang</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Melihat daftar barang dan jumlah stok yang tersedia saat ini.</p>
                    </div>
                    <button onclick="exportMasterBarangExcel()"
                            class="bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-sky-200 transition">
                        <i data-lucide="download" class="w-4 h-4"></i> Export Excel
                    </button>
                </div>

                <div class="flex flex-wrap items-end gap-3 mb-5">
                    <div>
                        <label for="barang-filter-stok" class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-wide">Status Stok</label>
                        <select id="barang-filter-stok" onchange="renderTableBarang()"
                                class="px-3 py-2 border border-slate-200 rounded-lg text-xs bg-white text-slate-700 outline-none transition">
                            <option value="">Semua barang</option>
                            <option value="habis">Stok habis</option>
                            <option value="kurang5">Stok &lt; 5</option>
                            <option value="tersedia">Stok tersedia</option>
                        </select>
                    </div>
                    <button onclick="cetakLaporanMasterBarang()"
                            class="bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white px-4 py-2 rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md shadow-rose-200 transition">
                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-100">
                    <table class="w-full text-sm text-left striped-table">
                        <thead>
                            <tr class="bg-gradient-to-r from-slate-800 to-slate-700 text-white">
                                <th class="px-4 py-3 text-xs font-semibold w-12 rounded-tl-xl">No</th>
                                <th class="px-4 py-3 text-xs font-semibold">Nama Barang</th>
                                <th class="px-4 py-3 text-xs font-semibold">Jenis Barang</th>
                                <th class="px-4 py-3 text-xs font-semibold">Stok Tersedia</th>
                                <th class="px-4 py-3 text-xs font-semibold rounded-tr-xl">Satuan</th>
                            </tr>
                        </thead>
                        <tbody id="barang-table-body"></tbody>
                    </table>
                </div>
            </section>

            <!-- ================================================
                 SECTION 3: AKUN SAYA
            ================================================ -->
            <section id="section-akun" class="hidden bg-white rounded-2xl shadow-md border border-slate-100 p-6">
                <div class="mb-6 pb-5 border-b border-slate-100">
                    <h2 class="text-base font-extrabold text-slate-800">Akun Saya</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Ubah password akun pemantau Anda.</p>
                </div>
                <form id="form-ubah-password" onsubmit="handleChangeOwnPassword(event)" class="max-w-md space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Password saat ini</label>
                        <input id="password-saat-ini" type="password" required autocomplete="current-password"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white transition outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1.5 uppercase tracking-wide">Password baru <span class="text-slate-400 normal-case font-normal">(minimal 4 karakter)</span></label>
                        <input id="password-baru" type="password" required minlength="4" autocomplete="new-password"
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm bg-slate-50 focus:bg-white transition outline-none">
                    </div>
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-emerald-200 transition">
                        Ubah Password
                    </button>
                </form>
            </section>

        </main>

        <!-- FOOTER -->
        <footer class="app-footer py-4 text-center text-xs font-medium">
            SIPINTAR Dev &copy; 2026 Kemenag Kota Palembang &mdash; SIPINTAR Mode Pemantau
        </footer>
    </div>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let rawTransaksiData = [];
        let masterBarangList = [];
        let showAllCriticalStock = false;

        document.addEventListener('DOMContentLoaded', async () => {
            if (window.lucide) lucide.createIcons();

            const isLoggedIn = sessionStorage.getItem('sipintar_pemantau_logged_in') === 'true'
                && sessionStorage.getItem('sipintar_role') === 'pemantau';

            if (isLoggedIn) {
                showDashboard();
            }

            // Realtime clock (visual only, no logic dependency)
            updateClock();
            setInterval(updateClock, 1000);
        });

        function updateClock() {
            const now = new Date();
            const clockEl = document.getElementById('realtime-clock');
            const dateEl = document.getElementById('realtime-date');
            if (!clockEl) return;
            clockEl.textContent = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
            }
        }

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
                    if (result.role !== 'pemantau') {
                        alert('Akun ini bukan akun dengan hak akses Pemantau!');
                        await fetch('api.php?action=logout');
                        return;
                    }
                    sessionStorage.setItem('sipintar_pemantau_logged_in', 'true');
                    sessionStorage.setItem('sipintar_logged_in', 'true');
                    sessionStorage.setItem('sipintar_role', result.role);
                    sessionStorage.setItem('sipintar_user_id', result.id);
                    sessionStorage.setItem('sipintar_user', result.nama);
                    sessionStorage.setItem('sipintar_username', result.username);

                    showDashboard();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal terhubung ke server!');
            }
        }

        async function showDashboard() {
            document.getElementById('login-screen').classList.add('hidden');
            document.getElementById('app-dashboard').classList.remove('hidden');

            const userName = sessionStorage.getItem('sipintar_user') || 'Pemantau';
            document.getElementById('user-name').innerText = userName;

            // Update avatar initials
            const avatarEl = document.getElementById('user-avatar-initials');
            if (avatarEl && userName) {
                const parts = userName.trim().split(' ');
                const initials = parts.length >= 2
                    ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase()
                    : userName.substring(0, 2).toUpperCase();
                avatarEl.textContent = initials;
            }

            await loadMasterBarang();
            await loadTransaksiData();
            switchTab('dashboard');
        }

        async function handleLogout() {
            try { await fetch('api.php?action=logout'); } catch (err) {}
            sessionStorage.clear();
            location.reload();
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

        async function loadMasterBarang() {
            try {
                const res = await fetch('api.php?action=get_barang');
                const result = await res.json();
                if (result.success) {
                    masterBarangList = result.data;
                    renderTableBarang();
                    renderDashboardWidgets();
                }
            } catch (err) {
                console.error('Gagal memuat barang:', err);
            }
        }

        function renderTableBarang() {
            const tbody = document.getElementById('barang-table-body');
            tbody.innerHTML = '';
            const statusStok = document.getElementById('barang-filter-stok').value;
            const filtered = masterBarangList.filter(b => !statusStok || (statusStok === 'habis' ? Number(b.stok) <= 0 : statusStok === 'kurang5' ? Number(b.stok) < 5 : Number(b.stok) > 0));

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-xs text-slate-400">Tidak ada barang yang sesuai dengan filter.</td></tr>`;
                return;
            }

            filtered.forEach((b, index) => {
                const tr = document.createElement('tr');
                tr.className = "border-b text-xs";
                const stokBadge = b.stok <= 0
                    ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 pulse-badge">HABIS</span>`
                    : b.stok <= 5
                        ? `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">TIPIS</span>`
                        : `<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">AMAN</span>`;
                tr.innerHTML = `
                    <td class="px-4 py-3 font-semibold text-slate-500">${index + 1}</td>
                    <td class="px-4 py-3 font-semibold text-slate-800">${b.nama_barang}</td>
                    <td class="px-4 py-3 text-slate-500">${b.jenis_barang}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-sm ${b.stok <= 5 ? 'text-rose-600' : 'text-emerald-700'}">${b.stok}</span>
                            ${stokBadge}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-slate-500">${b.satuan}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function cetakLaporanMasterBarang() {
            if (!window.jspdf) { alert('Komponen PDF belum tersedia.'); return; }
            const statusStok = document.getElementById('barang-filter-stok').value;
            const rows = masterBarangList.filter(b => !statusStok || (statusStok === 'habis' ? Number(b.stok) <= 0 : statusStok === 'kurang5' ? Number(b.stok) < 5 : Number(b.stok) > 0));
            if (!rows.length) { alert('Tidak ada data barang sesuai filter untuk dicetak.'); return; }
            const { jsPDF } = window.jspdf, doc = new jsPDF();
            const statusLabel = statusStok === 'habis' ? 'Stok habis' : statusStok === 'kurang5' ? 'Stok < 5' : statusStok === 'tersedia' ? 'Stok tersedia' : 'Semua status stok';
            doc.setFontSize(14); doc.text('Laporan Master Barang', 14, 16);
            doc.setFontSize(10); doc.text(`Status: ${statusLabel}`, 14, 23);
            doc.autoTable({ startY: 29, head: [['No.', 'Nama Barang', 'Jenis', 'Stok', 'Satuan']], body: rows.map((b, i) => [i + 1, b.nama_barang, b.jenis_barang, b.stok, b.satuan]), theme: 'grid', styles: { fontSize: 9 } });
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
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-800 flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-lg flex-shrink-0"><i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i></div>
                        <span class="font-medium">Semua stok barang berada dalam kondisi aman (di atas 5 Pcs).</span>
                    </div>
                `;
            } else {
                const daftarTampil = showAllCriticalStock ? kritis : kritis.slice(0, 1);
                daftarTampil.forEach(b => {
                    const isHabis = b.stok == 0;
                    const statusClass = isHabis ? 'bg-rose-50 border-rose-200' : 'bg-amber-50 border-amber-200';
                    const badgeClass = isHabis ? 'bg-rose-600 text-white pulse-badge' : 'bg-amber-500 text-white';
                    const statusText = isHabis ? 'STOK HABIS' : 'STOK TIPIS';
                    const iconColor = isHabis ? 'text-rose-500' : 'text-amber-500';

                    const div = document.createElement('div');
                    div.className = `alert-stok p-4 border rounded-xl text-xs flex items-center justify-between gap-3 ${statusClass}`;
                    div.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="p-2 ${isHabis ? 'bg-rose-100' : 'bg-amber-100'} rounded-lg flex-shrink-0">
                                <i data-lucide="alert-circle" class="w-4 h-4 ${iconColor}"></i>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800">${b.nama_barang}</span>
                                <span class="text-[11px] text-slate-500 ml-1">(Kategori: ${b.jenis_barang})</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <span class="font-extrabold text-sm text-slate-700">${b.stok} ${b.satuan}</span>
                            <span class="px-2.5 py-1 text-[10px] rounded-full font-bold ${badgeClass}">${statusText}</span>
                        </div>
                    `;
                    alertContainer.appendChild(div);
                });

                if (kritis.length > 1) {
                    const toggleButton = document.createElement('button');
                    toggleButton.type = 'button';
                    toggleButton.className = 'text-xs font-bold text-emerald-700 hover:text-emerald-900 hover:underline mt-1';
                    toggleButton.textContent = showAllCriticalStock
                        ? 'Tampilkan lebih sedikit'
                        : `Lihat semua ${kritis.length} barang kritis`;
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
                recentTbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-xs text-slate-400">Belum ada transaksi.</td></tr>`;
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
                    tr.className = "border-b text-xs";
                    tr.innerHTML = `
                        <td class="px-4 py-3 font-bold text-emerald-700">${t.kode_transaksi}</td>
                        <td class="px-4 py-3">${tipeBadge}</td>
                        <td class="px-4 py-3 text-slate-500">${t.tanggal_pengambilan}</td>
                        <td class="px-4 py-3 font-medium text-slate-700">${t.nama_pengambil} <span class="text-[10px] text-slate-400 font-normal">(${t.jabatan_unit})</span></td>
                        <td class="px-4 py-3 text-slate-500">${barangStr}</td>
                    `;
                    recentTbody.appendChild(tr);
                });
            }

            if (window.lucide) lucide.createIcons();
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
                tbody.innerHTML = `<tr><td colspan="7" class="p-6 text-center text-xs text-slate-400">Tidak ada data transaksi yang cocok dengan filter.</td></tr>`;
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
                tr.className = "border-b text-xs";
                tr.innerHTML = `
                    <td class="px-4 py-3 font-bold text-emerald-700">${item.kode_transaksi}</td>
                    <td class="px-4 py-3">${tipeBadge}</td>
                    <td class="px-4 py-3 text-slate-500">${item.tanggal_pengambilan}</td>
                    <td class="px-4 py-3"><strong class="text-slate-800">${item.nama_pengambil}</strong><br><span class="text-[10px] text-slate-400">${pihakSubtext}: ${item.jabatan_unit}</span></td>
                    <td class="px-4 py-3 text-slate-600 leading-relaxed">${barangList}</td>
                    <td class="px-4 py-3 text-slate-400">${item.keterangan || '-'}</td>
                    <td class="px-4 py-3 text-center"><button onclick="downloadReceiptPdfByCode('${item.kode_transaksi}')" class="text-rose-500 hover:text-rose-700 p-1.5 hover:bg-rose-50 rounded-lg transition" title="Unduh bukti penerimaan PDF"><i data-lucide="file-down" class="w-4 h-4"></i></button></td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) lucide.createIcons();
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
            const transaction = rawTransaksiData.find(item => item.kode_transaksi === kode);
            if (!transaction) return;
            const { jsPDF } = window.jspdf, doc = new jsPDF(), masuk = (transaction.tipe || 'KELUAR') === 'MASUK';
            const logo = await getLogoDataUrl(); if (logo) doc.addImage(logo, 'PNG', 94, 5, 22, 22);
            doc.setFontSize(16); doc.setFont('helvetica', 'bold'); doc.text(masuk ? 'BUKTI PENERIMAAN BARANG MASUK' : 'BUKTI PENERIMAAN BARANG', 105, 34, { align: 'center' });
            doc.setFontSize(10); doc.setFont('helvetica', 'normal'); doc.text('SIPINTAR - Kemenag Kota Palembang', 105, 41, { align: 'center' }); doc.line(15, 46, 195, 46);
            doc.text(`Kode Transaksi: ${transaction.kode_transaksi}`, 15, 56); doc.text(`Tanggal: ${transaction.tanggal_pengambilan}`, 15, 63);
            doc.text(`${masuk ? 'Petugas Penerima' : 'Nama Penerima'}: ${transaction.nama_pengambil}`, 15, 70); doc.text(`${masuk ? 'Unit' : 'Jabatan'}: ${transaction.jabatan_unit || '-'}`, 15, 77);
            if (transaction.keterangan) doc.text(`Keperluan: ${transaction.keterangan}`, 15, 84, { maxWidth: 175 });
            doc.autoTable({ startY: transaction.keterangan ? 92 : 86, head: [['No.', 'Nama Barang', 'Jumlah', 'Satuan']], body: (transaction.items || []).map((item, index) => [index + 1, item.nama_barang, item.jumlah, item.satuan]), theme: 'grid', styles: { fontSize: 10 } });
            let y = doc.lastAutoTable.finalY + 15;
            if (transaction.tanda_tangan) { if (y > 235) { doc.addPage(); y = 25; } doc.text('Penerima,', 145, y, { align: 'center' }); try { doc.addImage(transaction.tanda_tangan, 'PNG', 120, y + 3, 50, 25); } catch (error) {} doc.text(transaction.nama_pengambil || '', 145, y + 34, { align: 'center' }); }
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
            document.getElementById('section-history').classList.add('hidden');
            document.getElementById('section-master-barang').classList.add('hidden');
            document.getElementById('section-akun').classList.add('hidden');

            const inactiveStyle = "tab-btn px-5 py-3.5 text-sm font-semibold text-slate-500 hover:text-emerald-700 flex items-center gap-2 whitespace-nowrap relative";
            const activeStyle = "tab-btn active px-5 py-3.5 text-sm font-semibold text-slate-500 hover:text-emerald-700 flex items-center gap-2 whitespace-nowrap relative";

            document.getElementById('tab-dashboard').className = inactiveStyle;
            document.getElementById('tab-history').className = inactiveStyle;
            document.getElementById('tab-master-barang').className = inactiveStyle;
            document.getElementById('tab-akun').className = inactiveStyle;

            if (tab === 'dashboard') {
                const sec = document.getElementById('section-dashboard');
                sec.classList.remove('hidden');
                sec.classList.add('section-visible');
                document.getElementById('tab-dashboard').className = activeStyle;
            } else if (tab === 'history') {
                const sec = document.getElementById('section-history');
                sec.classList.remove('hidden');
                sec.classList.add('section-visible');
                document.getElementById('tab-history').className = activeStyle;
            } else if (tab === 'master_barang') {
                const sec = document.getElementById('section-master-barang');
                sec.classList.remove('hidden');
                sec.classList.add('section-visible');
                document.getElementById('tab-master-barang').className = activeStyle;
            } else if (tab === 'akun') {
                const sec = document.getElementById('section-akun');
                sec.classList.remove('hidden');
                sec.classList.add('section-visible');
                document.getElementById('tab-akun').className = activeStyle;
            }
        }
    </script>
</body>
</html>
