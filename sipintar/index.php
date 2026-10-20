<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPINTAR - MAN 1 Palembang</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- jsPDF & autoTable -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <!-- SheetJS / XLSX Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-800">

    <!-- LOGIN SCREEN -->
    <div id="login-screen" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-80 p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="bg-emerald-800 p-6 text-center text-white">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-3 p-1 shadow-md overflow-hidden">
                    <img id="app-logo-img" src="logo.png" alt="Logo MAN 1 Palembang" class="w-full h-full object-contain rounded-full" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-emerald-800 font-bold text-xs text-center\'>MAN 1</div>';">
                </div>
                <h2 class="text-xl font-bold">SIPINTAR</h2>
                <p class="text-emerald-100 text-xs mt-1">Sistem Pengelolaan Inventaris MAN 1 Palembang</p>
            </div>
            
            <div class="p-6 space-y-4">
                <form id="form-login" onsubmit="handleLogin(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Username</label>
                        <input type="text" id="login-username" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm outline-none" placeholder="Masukkan username">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Password</label>
                        <input type="password" id="login-password" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-emerald-500 text-sm outline-none" placeholder="Masukkan password">
                    </div>
                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-2.5 rounded-lg font-semibold text-sm transition shadow-md">
                        Masuk ke Sistem
                    </button>
                </form>

                <div class="relative my-3 text-center">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                    <span class="relative bg-white px-2 text-[11px] text-slate-400">Atau Akses Pengunjung</span>
                </div>

                <a href="pengunjung.php" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-lg font-medium text-xs transition border border-slate-300 flex items-center justify-center gap-1.5">
                    <i data-lucide="user-check" class="w-4 h-4 text-slate-500"></i> Buka Form Pengunjung (Tanpa Login)
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN APP DASHBOARD -->
    <div id="app-dashboard" class="hidden">
        <!-- HEADER / NAVBAR -->
        <header id="main-header" class="bg-emerald-800 text-white shadow-md sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-0.5 bg-white rounded-lg w-10 h-10 flex items-center justify-center overflow-hidden">
                        <img src="logo.png" alt="Logo MAN 1 Palembang" class="w-full h-full object-contain" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-emerald-800 font-bold text-[9px] text-center\'>MAN 1</div>';">
                    </div>
                    <div>
                        <h1 class="font-bold text-lg leading-tight">SIPINTAR</h1>
                        <p class="text-xs text-emerald-200">Sistem Pengelolaan Inventaris MAN 1 Palembang</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p id="user-name" class="text-xs font-semibold text-emerald-100">Pengguna Sistem</p>
                        <p class="text-[10px] text-emerald-300">Petugas Inventaris</p>
                    </div>
                    <button onclick="handleLogout()" class="bg-rose-600 hover:bg-rose-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium flex items-center gap-1 shadow transition">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Logout
                    </button>
                </div>
            </div>
        </header>

        <!-- MAIN CONTAINER -->
        <main class="max-w-7xl mx-auto px-4 py-6 space-y-6">
            <!-- TAB NAVIGATION -->
            <div id="main-tabs" class="flex border-b border-slate-200 gap-2 overflow-x-auto">
                <button onclick="switchTab('dashboard')" id="tab-dashboard" class="px-4 py-2.5 text-sm font-semibold border-b-2 border-emerald-600 text-emerald-800 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                </button>
                <button onclick="switchTab('barang_masuk')" id="tab-barang-masuk" class="px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="arrow-down-left" class="w-4 h-4"></i> Barang Masuk (Restok)
                </button>
                <button onclick="switchTab('form')" id="tab-form" class="px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="arrow-up-right" class="w-4 h-4"></i> Form Penerimaan (Keluar)
                </button>
                <button onclick="switchTab('master_barang')" id="tab-master-barang" class="px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="package" class="w-4 h-4"></i> Data Master Barang
                </button>
                <button onclick="switchTab('history')" id="tab-history" class="px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="history" class="w-4 h-4"></i> Riwayat & Laporan
                </button>
                <button onclick="switchTab('akun')" id="tab-akun" class="px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 whitespace-nowrap">
                    <i data-lucide="users" class="w-4 h-4"></i> Kelola Akun
                </button>
            </div>

            <!-- SECTION 0: DASHBOARD STATISTIK -->
            <section id="section-dashboard" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Total Jenis Barang</p>
                            <h3 id="dash-total-barang" class="text-2xl font-bold text-slate-800 mt-1">0</h3>
                        </div>
                        <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                            <i data-lucide="boxes" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Stok Kritis (≤ 5)</p>
                            <h3 id="dash-stok-kritis" class="text-2xl font-bold text-amber-600 mt-1">0</h3>
                        </div>
                        <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Transaksi Barang Masuk</p>
                            <h3 id="dash-trans-masuk" class="text-2xl font-bold text-emerald-600 mt-1">0</h3>
                        </div>
                        <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                            <i data-lucide="arrow-down-left" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Transaksi Barang Keluar</p>
                            <h3 id="dash-trans-keluar" class="text-2xl font-bold text-rose-600 mt-1">0</h3>
                        </div>
                        <div class="p-3 bg-rose-50 text-rose-600 rounded-lg">
                            <i data-lucide="arrow-up-right" class="w-6 h-6"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h2 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
                        <i data-lucide="bell" class="w-5 h-5 text-amber-500"></i> Peringatan Notifikasi Stok Kritis
                    </h2>
                    <div id="dash-alert-container" class="space-y-3"></div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <i data-lucide="clock" class="w-5 h-5 text-emerald-600"></i> Transaksi Terbaru
                        </h2>
                        <button onclick="switchTab('history')" class="text-xs font-semibold text-emerald-700 hover:text-emerald-800">Lihat Semua →</button>
                    </div>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 border-b text-xs font-semibold text-slate-600">
                                <tr>
                                    <th class="p-3">Kode Transaksi</th>
                                    <th class="p-3">Tipe</th>
                                    <th class="p-3">Tanggal</th>
                                    <th class="p-3">Pihak Terkait</th>
                                    <th class="p-3">Rincian Barang</th>
                                </tr>
                            </thead>
                            <tbody id="dash-recent-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- SECTION 1: BARANG MASUK -->
            <section id="section-barang-masuk" class="hidden bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b flex items-center gap-2">
                    <i data-lucide="box" class="w-5 h-5 text-emerald-600"></i> Form Restok / Barang Masuk
                </h2>
                
                <form id="form-barang-masuk" onsubmit="handleSaveBarangMasuk(event)" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Masuk</label>
                            <input type="date" id="bm_tanggal" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Petugas Penerima</label>
                            <input type="text" id="bm_petugas" required placeholder="Nama Petugas Inventaris" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <div class="mt-6">
                        <label class="block text-xs font-semibold text-slate-600 mb-2">Daftar Barang Masuk</label>
                        <div class="overflow-x-auto border rounded-lg">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-slate-50 border-b text-xs font-semibold text-slate-600">
                                    <tr>
                                        <th class="p-3">Nama Barang</th>
                                        <th class="p-3 w-32">Jumlah Masuk</th>
                                        <th class="p-3 w-36">Satuan</th>
                                        <th class="p-3 w-16 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="bm-table-items-body"></tbody>
                            </table>
                        </div>
                        <button type="button" onclick="addBarangMasukRow()" class="mt-2 text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Baris Barang
                        </button>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm shadow transition flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Barang Masuk
                        </button>
                    </div>
                </form>
            </section>

            <!-- SECTION 2: BARANG KELUAR -->
            <section id="section-form" class="hidden bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b flex items-center gap-2">
                    <i data-lucide="clipboard-list" class="w-5 h-5 text-emerald-600"></i> Form Penerimaan Barang (Barang Keluar)
                </h2>
                
                <div id="form-holder">
                    <form id="form-pengambilan" onsubmit="handleSaveTransaksi(event)" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal Penerimaan</label>
                                <input type="date" id="tanggal_pengambilan" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Penerima</label>
                                <input type="text" id="nama_pengambil" required placeholder="Nama Guru / Staf Penerima" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Jabatan</label>
                                <input type="text" id="jabatan_unit" required placeholder="Contoh: Guru IPA / Staf TU" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Daftar Barang yang Diterima</label>
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-slate-50 border-b text-xs font-semibold text-slate-600">
                                        <tr>
                                            <th class="p-3">Nama Barang</th>
                                            <th class="p-3 w-32">Jumlah</th>
                                            <th class="p-3 w-36">Satuan</th>
                                            <th class="p-3 w-16 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-items-body"></tbody>
                                </table>
                            </div>
                            <button type="button" onclick="addBarangRow()" class="mt-2 text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Baris Barang
                            </button>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan / Keperluan (Opsional)</label>
                            <textarea id="keterangan" rows="2" placeholder="Catatan atau keperluan barang..." class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                        </div>

                        <div class="pt-3 flex justify-end">
                            <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-lg font-semibold text-sm shadow transition flex items-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Penerimaan
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- SECTION 3: MASTER BARANG -->
            <section id="section-master-barang" class="hidden bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Master Data Barang</h2>
                        <p class="text-xs text-slate-500">Kelola jenis barang, nama barang, dan stok ketersediaan inventaris.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="openExcelModal()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow transition">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Upload Excel
                        </button>
                        <button onclick="openBarangModal()" class="bg-emerald-800 hover:bg-emerald-900 text-white px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow transition">
                            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Barang
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-100 border-b text-xs font-semibold text-slate-600">
                            <tr>
                                <th class="p-3 w-12">No</th>
                                <th class="p-3">Kode Barang</th>
                                <th class="p-3">Nama Barang</th>
                                <th class="p-3">Jenis Barang</th>
                                <th class="p-3">Kondisi</th>
                                <th class="p-3">Stok Tersedia</th>
                                <th class="p-3">Satuan</th>
                                <th class="p-3">Terakhir Diubah</th>
                                <th class="p-3 w-28 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="barang-table-body"></tbody>
                    </table>
                </div>
            </section>

            <!-- SECTION 4: RIWAYAT & LAPORAN -->
            <section id="section-history" class="hidden bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-4 border-b">
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Riwayat & Laporan Inventaris</h2>
                        <p class="text-xs text-slate-500">Rekapitulasi transaksi barang masuk & keluar serta rekap PDF/Excel.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button onclick="exportPDF()" class="bg-rose-700 hover:bg-rose-800 text-white px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow transition">
                            <i data-lucide="file-text" class="w-4 h-4"></i> Cetak PDF
                        </button>
                        <button onclick="exportExcel()" class="bg-emerald-700 hover:bg-emerald-800 text-white px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow transition">
                            <i data-lucide="sheet" class="w-4 h-4"></i> Cetak Excel
                        </button>
                        <button onclick="handleHapusTerfilter()" class="bg-amber-600 hover:bg-amber-700 text-white px-3.5 py-2 rounded-lg text-xs font-semibold flex items-center gap-1.5 shadow transition">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Terfilter
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4 bg-slate-50 p-3 rounded-lg border">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tipe Transaksi</label>
                        <select id="filter-tipe" onchange="renderTableHistory()" class="w-full px-3 py-1.5 text-xs border rounded-md outline-none">
                            <option value="">Semua Transaksi</option>
                            <option value="MASUK">Barang Masuk (Restok)</option>
                            <option value="KELUAR">Barang Keluar (Penerimaan)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Pencarian</label>
                        <input type="text" id="filter-search" oninput="renderTableHistory()" placeholder="Cari Kode/Pihak/Barang..." class="w-full px-3 py-1.5 text-xs border rounded-md outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Bulan</label>
                        <select id="filter-bulan" onchange="renderTableHistory()" class="w-full px-3 py-1.5 text-xs border rounded-md outline-none">
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
                        <label class="block text-[11px] font-semibold text-slate-500 mb-1">Tahun</label>
                        <input type="number" id="filter-tahun" oninput="renderTableHistory()" placeholder="Contoh: 2026" class="w-full px-3 py-1.5 text-xs border rounded-md outline-none">
                    </div>
                </div>

                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-100 border-b text-xs font-semibold text-slate-600">
                            <tr>
                                <th class="p-3">Kode Transaksi</th>
                                <th class="p-3">Tipe</th>
                                <th class="p-3">Tanggal</th>
                                <th class="p-3">Penerima / Petugas</th>
                                <th class="p-3">Rincian Barang</th>
                                <th class="p-3">Keterangan</th>
                                <th class="p-3 w-20 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="history-table-body"></tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>

    <!-- MODAL EDIT TRANSAKSI -->
    <div id="modal-edit-transaksi" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full overflow-hidden">
            <div class="bg-emerald-800 text-white px-5 py-3 flex justify-between items-center">
                <h3 class="font-bold text-sm">Edit Data Transaksi</h3>
                <button onclick="closeEditTransaksiModal()" class="text-emerald-200 hover:text-white">&times;</button>
            </div>
            <form id="form-edit-transaksi" onsubmit="handleSaveEditTransaksi(event)" class="p-5 space-y-4 max-h-[85vh] overflow-y-auto">
                <input type="hidden" id="edit_kode_transaksi">
                <input type="hidden" id="edit_tipe_transaksi">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Tanggal</label>
                        <input type="date" id="edit_tanggal" required class="w-full px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label id="edit_label_pengambil" class="block text-xs font-semibold text-slate-600 mb-1">Nama Penerima</label>
                        <input type="text" id="edit_nama_pengambil" required class="w-full px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label id="edit_label_jabatan" class="block text-xs font-semibold text-slate-600 mb-1">Jabatan</label>
                        <input type="text" id="edit_jabatan_unit" class="w-full px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-2">Daftar Barang</label>
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 border-b font-semibold text-slate-600">
                                <tr>
                                    <th class="p-2">Nama Barang</th>
                                    <th class="p-2 w-28">Jumlah</th>
                                    <th class="p-2 w-28">Satuan</th>
                                    <th class="p-2 w-12 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="edit-table-items-body"></tbody>
                        </table>
                    </div>
                    <button type="button" onclick="addEditBarangRow()" class="mt-2 text-xs font-semibold text-emerald-700 hover:text-emerald-800 flex items-center gap-1">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Tambah Baris Barang
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan</label>
                    <textarea id="edit_keterangan" rows="2" class="w-full px-3 py-1.5 border rounded-lg text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2 border-t">
                    <button type="button" onclick="closeEditTransaksiModal()" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL UPLOAD EXCEL -->
    <div id="modal-excel" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="bg-emerald-800 text-white px-5 py-3 flex justify-between items-center">
                <h3 class="font-bold text-sm">Upload Template Excel Barang</h3>
                <button onclick="closeExcelModal()" class="text-emerald-200 hover:text-white">&times;</button>
            </div>
            <div class="p-5 space-y-4">
                <div class="bg-emerald-50 border border-emerald-200 p-3 rounded-lg text-xs text-emerald-800">
                    <p class="font-semibold mb-1">Format Kolom Excel yang Dibutuhkan:</p>
                    <p class="font-mono text-[11px]">nama_barang | jenis_barang | stok | satuan</p>
                </div>

                <button onclick="downloadExcelTemplate()" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold border flex items-center justify-center gap-1.5">
                    <i data-lucide="download" class="w-4 h-4"></i> Download Format Template Excel
                </button>

                <hr>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" id="excel-file-input" accept=".xlsx, .xls" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border rounded-lg p-1">
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeExcelModal()" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="button" onclick="processExcelUpload()" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold">Upload & Import</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT / TAMBAH BARANG -->
    <div id="modal-barang" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
            <div class="bg-emerald-800 text-white px-5 py-3 flex justify-between items-center">
                <h3 id="modal-barang-title" class="font-bold text-sm">Tambah Data Barang</h3>
                <button onclick="closeBarangModal()" class="text-emerald-200 hover:text-white">&times;</button>
            </div>
            <form id="form-barang" onsubmit="handleSaveMasterBarang(event)" class="p-5 space-y-4">
                <input type="hidden" id="barang-id">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Nama Barang</label>
                    <input type="text" id="barang-nama" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: Spidol Boardmarker">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jenis / Kategori Barang</label>
                    <input type="text" id="barang-jenis" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Contoh: ATK / Alat Kebersihan">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Kondisi Barang</label>
                    <select id="barang-kondisi" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Sedang">Rusak Sedang</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah Stok</label>
                        <input type="number" id="barang-stok" required min="0" value="0" class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Satuan</label>
                        <input type="text" id="barang-satuan" required class="w-full px-3 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Pcs/Box/Pack">
                    </div>
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeBarangModal()" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-xs font-semibold">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>

    <!-- KELOLA AKUN -->
    <section id="section-akun" class="hidden max-w-7xl mx-auto px-4 pb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="mb-6 pb-4 border-b">
                <h2 class="text-base font-bold text-slate-800">Kelola Akun</h2>
                <p id="akun-description" class="text-xs text-slate-500">Kelola akun pengguna SIPINTAR.</p>
            </div>
            <div id="admin-account-tools" class="hidden space-y-6">
                <form id="form-akun-baru" onsubmit="handleCreateAccount(event)" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end bg-slate-50 p-4 rounded-lg border">
                    <div><label class="block text-[11px] font-semibold text-slate-600 mb-1">Nama lengkap</label><input id="akun-nama" required class="w-full px-3 py-2 border rounded-lg text-sm" autocomplete="name"></div>
                    <div><label class="block text-[11px] font-semibold text-slate-600 mb-1">Username</label><input id="akun-username" required class="w-full px-3 py-2 border rounded-lg text-sm" autocomplete="username"></div>
                    <div><label class="block text-[11px] font-semibold text-slate-600 mb-1">Password</label><input id="akun-password" type="password" required minlength="4" class="w-full px-3 py-2 border rounded-lg text-sm" autocomplete="new-password"></div>
                    <div><label class="block text-[11px] font-semibold text-slate-600 mb-1">Peran</label><select id="akun-role" class="w-full px-3 py-2 border rounded-lg text-sm"><option value="pegawai">Pegawai</option><option value="admin">Admin</option><option value="pemantau">Pemantau</option></select></div>
                    <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-3 py-2 rounded-lg text-sm font-semibold">Tambah Akun</button>
                </form>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm text-left"><thead class="bg-slate-100 border-b text-xs font-semibold text-slate-600"><tr><th class="p-3">Nama</th><th class="p-3">Username</th><th class="p-3">Password</th><th class="p-3">Peran</th><th class="p-3">Aksi</th></tr></thead><tbody id="account-table-body"></tbody></table>
                </div>
                <div id="modal-edit-akun" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-900 bg-opacity-50 p-4">
                    <form onsubmit="handleEditAccount(event)" class="bg-white rounded-xl shadow-xl max-w-md w-full p-5 space-y-4">
                        <div class="flex items-center justify-between"><h3 class="font-bold">Edit Akun</h3><button type="button" onclick="closeEditAccountModal()" class="text-slate-500 text-xl">&times;</button></div>
                        <input id="edit-akun-id" type="hidden">
                        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Nama lengkap</label><input id="edit-akun-nama" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
                        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Username</label><input id="edit-akun-username" required class="w-full px-3 py-2 border rounded-lg text-sm"></div>
                        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Password baru</label><input id="edit-akun-password" type="password" minlength="4" autocomplete="new-password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3 py-2 border rounded-lg text-sm"></div>
                        <div><label class="block text-xs font-semibold text-slate-600 mb-1">Peran</label><select id="edit-akun-role" class="w-full px-3 py-2 border rounded-lg text-sm"><option value="pegawai">Pegawai</option><option value="admin">Admin</option><option value="pemantau">Pemantau</option></select></div>
                        <div class="flex justify-end gap-2"><button type="button" onclick="closeEditAccountModal()" class="px-4 py-2 border rounded-lg text-sm">Batal</button><button type="submit" class="px-4 py-2 bg-emerald-700 text-white rounded-lg text-sm font-semibold">Simpan Perubahan</button></div>
                    </form>
                </div>
            </div>
            <form id="form-ubah-password" onsubmit="handleChangeOwnPassword(event)" class="hidden max-w-md space-y-4">
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Password saat ini</label><input id="password-saat-ini" type="password" required autocomplete="current-password" class="w-full px-3 py-2 border rounded-lg text-sm"></div>
                <div><label class="block text-xs font-semibold text-slate-600 mb-1">Password baru (minimal 4 karakter)</label><input id="password-baru" type="password" required minlength="4" autocomplete="new-password" class="w-full px-3 py-2 border rounded-lg text-sm"></div>
                <button type="submit" class="bg-emerald-700 hover:bg-emerald-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">Ubah Password</button>
            </form>
        </div>
    </section>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        let rawTransaksiData = [];
        let masterBarangList = [];
        let showAllCriticalStock = false;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons();
            
            const today = new Date();
            document.getElementById('tanggal_pengambilan').valueAsDate = today;
            document.getElementById('bm_tanggal').valueAsDate = today;

            const isLoggedIn = sessionStorage.getItem('sipintar_logged_in') === 'true';

            if (isLoggedIn) {
                showDashboard();
            }
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

            if (masterBarangList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="p-4 text-center text-xs text-slate-400">Belum ada data barang. Silakan unggah Excel atau tambah manual.</td></tr>`;
                return;
            }

            masterBarangList.forEach((b, index) => {
                const tr = document.createElement('tr');
                tr.className = "border-b text-xs hover:bg-slate-50";
                tr.innerHTML = `
                    <td class="p-3 font-semibold">${index + 1}</td>
                    <td class="p-3 font-mono text-slate-600">${b.kode_barang || '-'}</td>
                    <td class="p-3 font-semibold text-slate-800">${b.nama_barang}</td>
                    <td class="p-3 text-slate-600">${b.jenis_barang}</td>
                    <td class="p-3 text-slate-600">${b.kondisi || 'Baik'}</td>
                    <td class="p-3 font-bold ${b.stok <= 5 ? 'text-rose-600' : 'text-emerald-700'}">${b.stok}</td>
                    <td class="p-3">${b.satuan}</td>
                    <td class="p-3 text-slate-500">${b.updated_at ? new Date(b.updated_at.replace(' ', 'T')).toLocaleString('id-ID') : '-'}</td>
                    <td class="p-3 text-center flex justify-center gap-2">
                        <button onclick="editBarang(${b.id})" class="text-blue-600 hover:text-blue-800 p-1" title="Edit">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
                        <button onclick="hapusBarang(${b.id})" class="text-rose-600 hover:text-rose-800 p-1" title="Hapus">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (window.lucide) lucide.createIcons();
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
                    <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs text-emerald-800 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Semua stok barang berada dalam kondisi aman (di atas 5 Pcs).</span>
                    </div>
                `;
            } else {
                const daftarTampil = showAllCriticalStock ? kritis : kritis.slice(0, 1);
                daftarTampil.forEach(b => {
                    const statusClass = b.stok == 0 ? 'bg-rose-50 border-rose-300 text-rose-800' : 'bg-amber-50 border-amber-300 text-amber-800';
                    const badgeClass = b.stok == 0 ? 'bg-rose-600 text-white' : 'bg-amber-500 text-white';
                    const statusText = b.stok == 0 ? 'STOK HABIS' : 'STOK TIPIS';

                    const div = document.createElement('div');
                    div.className = `p-3 border rounded-lg text-xs flex items-center justify-between ${statusClass}`;
                    div.innerHTML = `
                        <div class="flex items-center gap-2.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                            <div>
                                <span class="font-bold">${b.nama_barang}</span>
                                <span class="text-[11px] opacity-80"> (Kategori: ${b.jenis_barang})</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm">${b.stok} ${b.satuan}</span>
                            <span class="px-2 py-0.5 text-[10px] rounded font-bold ${badgeClass}">${statusText}</span>
                        </div>
                    `;
                    alertContainer.appendChild(div);
                });

                if (kritis.length > 1) {
                    const toggleButton = document.createElement('button');
                    toggleButton.type = 'button';
                    toggleButton.className = 'text-xs font-semibold text-emerald-700 hover:text-emerald-900 hover:underline';
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
                recentTbody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-xs text-slate-400">Belum ada transaksi.</td></tr>`;
            } else {
                recent.forEach(t => {
                    const tipe = t.tipe || 'KELUAR';
                    const tipeBadge = tipe === 'MASUK' 
                        ? `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">BARANG MASUK</span>`
                        : `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">BARANG KELUAR</span>`;

                    const barangStr = t.items && t.items.length > 0 
                        ? t.items.map(i => `${i.nama_barang} (${i.jumlah} ${i.satuan})`).join(', ')
                        : '-';

                    const tr = document.createElement('tr');
                    tr.className = "border-b text-xs hover:bg-slate-50";
                    tr.innerHTML = `
                        <td class="p-3 font-semibold">${t.kode_transaksi}</td>
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
                    <select required class="item-nama w-full px-2 py-1.5 border rounded outline-none bg-white" onchange="updateSatuanInput(this)">
                        ${options}
                    </select>
                </td>
                <td class="p-2"><input type="number" min="1" value="1" required class="item-jumlah w-full px-2 py-1.5 border rounded outline-none text-center"></td>
                <td class="p-2"><input type="text" readonly class="item-satuan w-full px-2 py-1.5 border rounded bg-slate-100 outline-none" placeholder="Satuan"></td>
                <td class="p-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 hover:text-rose-800 p-1">
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

        async function handleSaveBarangMasuk(e) {
            e.preventDefault();
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
                    alert('Barang masuk berhasil disimpan! Stok telah otomatis bertambah.');
                    document.getElementById('form-barang-masuk').reset();
                    document.getElementById('bm_tanggal').valueAsDate = new Date();
                    document.getElementById('bm_petugas').value = '';
                    document.getElementById('bm-table-items-body').innerHTML = '';
                    
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
                    <select required class="item-nama w-full px-2 py-1.5 border rounded outline-none bg-white" onchange="updateSatuanAndStok(this)">
                        ${options}
                    </select>
                </td>
                <td class="p-2"><input type="number" min="1" value="1" required class="item-jumlah w-full px-2 py-1.5 border rounded outline-none text-center"></td>
                <td class="p-2"><input type="text" readonly class="item-satuan w-full px-2 py-1.5 border rounded bg-slate-100 outline-none" placeholder="Satuan"></td>
                <td class="p-2 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 hover:text-rose-800 p-1">
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
                    alert('Transaksi Penerimaan Berhasil Disimpan!');
                    document.getElementById('form-pengambilan').reset();
                    document.getElementById('tanggal_pengambilan').valueAsDate = new Date();
                    document.getElementById('table-items-body').innerHTML = '';
                    
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
                    ? `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">BARANG MASUK</span>`
                    : `<span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">BARANG KELUAR</span>`;

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
                        <button onclick="editTransaksi('${item.kode_transaksi}')" class="text-blue-600 hover:text-blue-800 p-1" title="Edit Transaksi">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                        </button>
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
                    <select required class="item-nama w-full px-2 py-1 border rounded outline-none bg-white" onchange="updateEditSatuan(this)">
                        ${options}
                    </select>
                </td>
                <td class="p-1.5"><input type="number" min="1" value="${jumlah}" required class="item-jumlah w-full px-2 py-1 border rounded outline-none text-center"></td>
                <td class="p-1.5"><input type="text" readonly value="${satuan}" class="item-satuan w-full px-2 py-1 border rounded bg-slate-100 outline-none"></td>
                <td class="p-1.5 text-center">
                    <button type="button" onclick="this.closest('tr').remove()" class="text-rose-600 hover:text-rose-800 p-1">
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
            doc.text('MAN 1 PALEMBANG', 110, 14, { align: 'center' });
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
            doc.text('Kepala Tata Usaha MAN 1 Palembang', 20, finalY + 5);

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

            const inactiveStyle = "px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 flex items-center gap-2 whitespace-nowrap";
            const activeStyle = "px-4 py-2.5 text-sm font-semibold border-b-2 border-emerald-600 text-emerald-800 flex items-center gap-2 whitespace-nowrap";

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
        }
    </script>
    <footer class="max-w-7xl mx-auto px-4 py-6 text-center text-xs text-slate-500">
        <p>&copy; 2026 MAN 1 Palembang</p>
        <p class="mt-1">dikembangkan oleh muthic</p>
    </footer>
</body>
</html>
