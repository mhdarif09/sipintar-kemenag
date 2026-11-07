<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Penerimaan Barang - SIPINTAR</title>
    <link rel="icon" type="image/png" href="logo.png">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Animated gradient background */
        .bg-animated {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 25%, #f0fdf4 50%, #f1f5f9 75%, #e2e8f0 100%);
            background-size: 400% 400%;
            animation: gradientShift 12s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Header gradient */
        .header-gradient {
            background: linear-gradient(135deg, #064e3b 0%, #065f46 40%, #047857 70%, #059669 100%);
            position: relative;
            overflow: hidden;
        }
        .header-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at center, rgba(255,255,255,0.08) 0%, transparent 60%);
            animation: shimmer 6s ease-in-out infinite;
        }
        .header-gradient::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(167,243,208,0.5), transparent);
        }
        @keyframes shimmer {
            0%, 100% { transform: translateX(-10%) translateY(-10%) rotate(0deg); }
            50% { transform: translateX(10%) translateY(10%) rotate(180deg); }
        }

        /* Decorative circles in header */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        /* Input focus styles */
        .form-input {
            transition: all 0.2s ease;
            border: 1.5px solid #e2e8f0;
        }
        .form-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.12);
            outline: none;
        }

        /* Card shadow */
        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 20px 60px -10px rgba(5,150,105,0.1), 0 0 0 1px rgba(0,0,0,0.04);
        }

        /* Label icon wrapper */
        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            pointer-events: none;
        }
        .input-icon-wrap input,
        .input-icon-wrap textarea {
            padding-left: 2.5rem;
        }

        /* Section divider */
        .section-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.5rem 0 1rem;
        }
        .section-divider span {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #059669;
            white-space: nowrap;
        }
        .section-divider::before,
        .section-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, #d1fae5);
        }
        .section-divider::after {
            background: linear-gradient(90deg, #d1fae5, transparent);
        }

        /* Table styles */
        .items-table th {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #064e3b;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 12px 14px;
        }
        .items-table td {
            padding: 10px 10px;
            border-bottom: 1px solid #f1f5f9;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .items-table tbody tr:hover td {
            background-color: #f0fdf4;
        }
        .items-table select,
        .items-table input[type="number"],
        .items-table input[type="text"] {
            font-size: 0.8rem;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1.5px solid #e2e8f0;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: white;
            width: 100%;
        }
        .items-table select:focus,
        .items-table input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1);
            outline: none;
        }
        .items-table input[readonly] {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
        }

        /* Signature area */
        .signature-wrap {
            background: linear-gradient(135deg, #fafff9, #f0fdf4);
            border: 2px dashed #6ee7b7;
            border-radius: 16px;
            padding: 4px;
            transition: border-color 0.2s;
        }
        .signature-wrap:focus-within {
            border-color: #10b981;
        }
        #visitor-signature {
            border-radius: 12px;
            background: white;
            display: block;
        }
        .signature-hint {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            gap: 6px;
            transition: opacity 0.3s;
        }

        /* Submit button */
        .btn-submit {
            background: linear-gradient(135deg, #059669, #047857);
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(5,150,105,0.35);
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #047857, #065f46);
            box-shadow: 0 6px 20px rgba(5,150,105,0.5);
            transform: translateY(-1px);
        }
        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(5,150,105,0.3);
        }

        /* Add row button */
        .btn-add-row {
            border: 1.5px dashed #6ee7b7;
            transition: all 0.2s;
        }
        .btn-add-row:hover {
            border-color: #10b981;
            background: #f0fdf4;
        }

        /* Toast notification */
        #toast-success {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-120px);
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 9999;
        }
        #toast-success.show {
            transform: translateX(-50%) translateY(0);
        }

        /* Floating nav glassmorphism */
        .floating-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(167, 243, 208, 0.5);
            box-shadow: 0 8px 32px rgba(5, 150, 105, 0.15), 0 2px 8px rgba(0,0,0,0.08);
        }
        .nav-link {
            transition: all 0.2s;
        }
        .nav-link:hover {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #065f46;
            transform: translateY(-1px);
        }

        /* Label badge */
        .label-required {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.65rem;
            font-weight: 700;
            color: #ef4444;
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 1px 6px;
            border-radius: 20px;
            vertical-align: middle;
            margin-left: 4px;
        }

        /* Delete button in table */
        .btn-delete-row {
            transition: all 0.2s;
            border-radius: 8px;
            padding: 6px;
        }
        .btn-delete-row:hover {
            background: #fff1f2;
            color: #e11d48;
        }

        /* Pulse animation for logo */
        @keyframes logoPulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(167,243,208,0.4); }
            50% { box-shadow: 0 0 0 12px rgba(167,243,208,0); }
        }
        .logo-ring {
            animation: logoPulse 2.5s ease-in-out infinite;
        }

        /* Step badges */
        .step-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-size: 0.65rem;
            font-weight: 800;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-animated min-h-screen text-slate-800 p-3 md:p-8 pb-28">

    <!-- Toast Success Notification -->
    <div id="toast-success" class="bg-white rounded-2xl shadow-2xl border border-emerald-100 px-6 py-4 flex items-center gap-4 min-w-72 max-w-sm">
        <div class="flex-shrink-0 w-11 h-11 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-md">
            <i data-lucide="check-circle-2" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <p class="text-sm font-700 text-slate-800 font-bold">Berhasil Dikirim!</p>
            <p class="text-xs text-slate-500 mt-0.5">Data penerimaan barang berhasil disimpan.</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="max-w-3xl mx-auto card-shadow rounded-2xl overflow-hidden bg-white">

        <!-- HEADER BANNER -->
        <div class="header-gradient p-7 md:p-10 text-white text-center relative">
            <!-- Decorative circles -->
            <div class="deco-circle w-40 h-40 -top-10 -right-10"></div>
            <div class="deco-circle w-24 h-24 bottom-0 -left-6"></div>
            <div class="deco-circle w-16 h-16 top-4 left-1/4" style="background:rgba(255,255,255,0.04)"></div>

            <div class="relative z-10">
                <!-- Logo -->
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 p-1.5 shadow-xl logo-ring overflow-hidden">
                    <img src="logo.png" alt="Logo Kemenag Kota Palembang" class="w-full h-full object-contain rounded-full"
                        onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'text-emerald-800 font-bold text-sm text-center leading-tight\'>MAN<br>1</div>';">
                </div>

                <!-- School Name & System Name -->
                <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1 mb-3">
                    <i data-lucide="school" class="w-3.5 h-3.5 text-emerald-200"></i>
                    <span class="text-xs font-semibold text-emerald-100">Kemenag Kota Palembang</span>
                </div>

                <h1 class="text-2xl md:text-3xl font-800 font-extrabold tracking-tight">SIPINTAR</h1>
                <p class="text-emerald-200 text-xs mt-1 mb-4 font-medium">Sistem Pengelolaan Inventaris Kemenag Kota Palembang</p>

                <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl px-5 py-3 inline-block">
                    <div class="flex items-center gap-2 justify-center">
                        <i data-lucide="clipboard-list" class="w-5 h-5 text-emerald-300"></i>
                        <div class="text-left">
                            <p class="text-base font-bold text-white">Form Penerimaan Barang</p>
                            <p class="text-emerald-300 text-xs">Isi formulir dengan lengkap dan benar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM -->
        <form id="form-pengambilan" onsubmit="handleSaveTransaksi(event)" class="p-5 md:p-8 space-y-2">

            <!-- Section 1: Data Penerima -->
            <div class="section-divider">
                <div class="step-badge">1</div>
                <span>Data Penerima</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tanggal -->
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-700 font-bold text-slate-600 mb-2">
                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-500"></i>
                        Tanggal Penerimaan
                    </label>
                    <input type="date" id="tanggal_pengambilan" required
                        class="form-input w-full px-3 py-3 rounded-xl text-sm text-slate-700 bg-white">
                </div>

                <!-- Nama Penerima -->
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-emerald-500"></i>
                        Nama Penerima
                    </label>
                    <input type="text" id="nama_pengambil" required placeholder="Nama Guru / Staf Penerima"
                        class="form-input w-full px-3 py-3 rounded-xl text-sm text-slate-700 bg-white">
                </div>

                <!-- Jabatan -->
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                        <i data-lucide="briefcase" class="w-3.5 h-3.5 text-emerald-500"></i>
                        Jabatan / Unit Kerja
                    </label>
                    <input type="text" id="jabatan_unit" required placeholder="Contoh: Guru IPA / TU"
                        class="form-input w-full px-3 py-3 rounded-xl text-sm text-slate-700 bg-white">
                </div>
            </div>

            <!-- Section 2: Daftar Barang -->
            <div class="section-divider">
                <div class="step-badge">2</div>
                <span>Daftar Barang Diterima</span>
            </div>

            <div class="bg-gradient-to-br from-slate-50 to-emerald-50/30 rounded-2xl border border-slate-100 overflow-hidden">
                <!-- Table wrapper for horizontal scroll -->
                <div class="overflow-x-auto">
                    <table class="items-table w-full text-sm text-left min-w-[480px]">
                        <thead>
                            <tr>
                                <th style="min-width:200px">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="package" class="w-3.5 h-3.5"></i>
                                        Nama Barang
                                    </div>
                                </th>
                                <th style="width:110px">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="hash" class="w-3.5 h-3.5"></i>
                                        Jumlah
                                    </div>
                                </th>
                                <th style="width:120px">
                                    <div class="flex items-center gap-1.5">
                                        <i data-lucide="ruler" class="w-3.5 h-3.5"></i>
                                        Satuan
                                    </div>
                                </th>
                                <th style="width:60px" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="table-items-body"></tbody>
                    </table>
                </div>

                <!-- Add row button -->
                <div class="p-3">
                    <button type="button" onclick="addBarangRow()"
                        class="btn-add-row w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-xs font-bold text-emerald-700 hover:text-emerald-900">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Tambah Baris Barang
                    </button>
                </div>
            </div>

            <!-- Section 3: Keterangan -->
            <div class="section-divider">
                <div class="step-badge">3</div>
                <span>Keterangan</span>
            </div>

            <div class="input-icon-wrap">
                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600 mb-2">
                    <i data-lucide="file-text" class="w-3.5 h-3.5 text-emerald-500"></i>
                    Keterangan / Keperluan
                    <span class="text-slate-400 font-normal">(Opsional)</span>
                </label>
                <textarea id="keterangan" rows="3" placeholder="Catatan, keperluan, atau sumber barang..."
                    class="form-input w-full px-4 py-3 rounded-xl text-sm text-slate-700 bg-white resize-none"></textarea>
            </div>

            <!-- Section 4: Tanda Tangan -->
            <div class="section-divider">
                <div class="step-badge">4</div>
                <span>Tanda Tangan Penerima</span>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-600">
                        <i data-lucide="pen-tool" class="w-3.5 h-3.5 text-emerald-500"></i>
                        Tanda Tangan
                        <span class="label-required">&#10033; Wajib</span>
                    </label>
                    <button type="button" onclick="clearVisitorSignature()"
                        class="flex items-center gap-1 text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors bg-rose-50 hover:bg-rose-100 px-3 py-1 rounded-full">
                        <i data-lucide="eraser" class="w-3 h-3"></i>
                        Hapus
                    </button>
                </div>

                <!-- Signature canvas wrapper -->
                <div class="signature-wrap relative">
                    <!-- Hint text (shown until user signs) -->
                    <div id="sig-hint" class="signature-hint">
                        <i data-lucide="pencil" class="w-7 h-7 text-emerald-300"></i>
                        <p class="text-xs font-semibold text-emerald-400">Tanda tangani di sini</p>
                        <p class="text-xs text-slate-400">Gunakan jari atau stylus untuk membubuhkan tanda tangan</p>
                    </div>
                    <canvas id="visitor-signature" width="800" height="220"
                        class="w-full touch-none rounded-xl" style="height:160px;"></canvas>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="pt-5">
                <button type="submit"
                    class="btn-submit w-full text-white px-6 py-4 rounded-2xl font-bold text-base shadow-lg flex items-center justify-center gap-3">
                    <i data-lucide="send" class="w-5 h-5"></i>
                    Kirim Formulir Penerimaan
                </button>
                <p class="text-center text-xs text-slate-400 mt-2.5">
                    <i data-lucide="shield-check" class="w-3 h-3 inline-block text-emerald-400 mr-1"></i>
                    Data tersimpan aman di server SIPINTAR Kemenag Kota Palembang
                </p>
            </div>

        </form>
    </div>

    <script>
        let masterBarangList = [];
        let signatureDrawing = false;

        function setupVisitorSignature() {
            const canvas = document.getElementById('visitor-signature'), ctx = canvas.getContext('2d');
            ctx.strokeStyle = '#0f172a'; ctx.lineWidth = 2.5; ctx.lineCap = 'round'; ctx.lineJoin = 'round';
            const point = event => { const rect = canvas.getBoundingClientRect(), source = event.touches ? event.touches[0] : event; return { x: (source.clientX - rect.left) * canvas.width / rect.width, y: (source.clientY - rect.top) * canvas.height / rect.height }; };
            const start = event => { event.preventDefault(); signatureDrawing = true; const p = point(event); ctx.beginPath(); ctx.moveTo(p.x, p.y); hideSigHint(); };
            const move = event => { if (!signatureDrawing) return; event.preventDefault(); const p = point(event); ctx.lineTo(p.x, p.y); ctx.stroke(); };
            canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', () => signatureDrawing = false);
            canvas.addEventListener('touchstart', start, { passive: false }); canvas.addEventListener('touchmove', move, { passive: false }); canvas.addEventListener('touchend', () => signatureDrawing = false);
        }

        function hideSigHint() {
            const hint = document.getElementById('sig-hint');
            if (hint) hint.style.opacity = '0';
        }

        function clearVisitorSignature() { const canvas = document.getElementById('visitor-signature'); canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height); const hint = document.getElementById('sig-hint'); if (hint) hint.style.opacity = '1'; }
        function getVisitorSignature() {
            const canvas = document.getElementById('visitor-signature'), pixels = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height).data;
            for (let i = 3; i < pixels.length; i += 4) if (pixels[i]) return canvas.toDataURL('image/png');
            return '';
        }

        document.addEventListener('DOMContentLoaded', async () => {
            if (window.lucide) lucide.createIcons();
            setupVisitorSignature();
            document.getElementById('tanggal_pengambilan').valueAsDate = new Date();
            await loadMasterBarang();
            addBarangRow();
        });

        async function loadMasterBarang() {
            try {
                const res = await fetch('api.php?action=get_barang');
                const result = await res.json();
                if (result.success) {
                    masterBarangList = result.data;
                }
            } catch (err) {
                console.error('Gagal memuat barang:', err);
            }
        }

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
                    <button type="button" onclick="this.closest('tr').remove()" class="btn-delete-row text-rose-600 hover:text-rose-800 p-1">
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

        function showToast() {
            const toast = document.getElementById('toast-success');
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
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
                    alert(`Stok untuk barang "${selectElem.value}" tidak mencukupi! (Maksimal: ${maxStok})`);
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

            const signature = getVisitorSignature();
            if (!signature) { alert('Tanda tangan penerima wajib diisi.'); return; }

            const selectedDate = document.getElementById('tanggal_pengambilan').value;
            const dateStr = selectedDate.replace(/-/g, '');
            const randomNum = Math.floor(100 + Math.random() * 900);
            const kode = `BRG-${dateStr}-${randomNum}`;

            const payload = {
                kode_transaksi: kode,
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
                    showToast();
                    document.getElementById('form-pengambilan').reset();
                    document.getElementById('tanggal_pengambilan').valueAsDate = new Date();
                    document.getElementById('table-items-body').innerHTML = '';
                    clearVisitorSignature();
                    await loadMasterBarang();
                    addBarangRow();
                } else {
                    alert(result.message);
                }
            } catch (err) {
                alert('Gagal menyimpan data ke database.');
            }
        }
    </script>

    <!-- FLOATING NAVIGATION -->
    <nav aria-label="Navigasi halaman"
        class="floating-nav fixed bottom-4 left-1/2 -translate-x-1/2 z-40 flex items-center gap-1 rounded-2xl p-1.5">
        <a href="index.php"
            class="nav-link flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-600">
            <i data-lucide="layout-dashboard" class="h-4 w-4 text-emerald-600"></i>
            Halaman Utama
        </a>
        <div class="w-px h-5 bg-slate-200 mx-0.5"></div>
        <a href="pemantau.php"
            class="nav-link flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-600">
            <i data-lucide="chart-no-axes-combined" class="h-4 w-4 text-emerald-600"></i>
            Pemantau
        </a>
    </nav>

    <!-- FOOTER -->
    <footer class="max-w-3xl mx-auto pt-4 pb-20 text-center text-xs text-slate-400">
        <div class="flex items-center justify-center gap-2">
            <i data-lucide="server" class="w-3 h-3 text-emerald-400"></i>
            <span>SIPINTAR Dev &copy; 2026 &mdash; Kemenag Kota Palembang</span>
        </div>
    </footer>

</body>
</html>
