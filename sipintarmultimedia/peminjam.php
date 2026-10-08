<?php
// -----------------------------------------------------------------------------
// 1. KONEKSI DATABASE
// -----------------------------------------------------------------------------
$db_host = "localhost";
$db_user = "root";       
$db_pass = "";           
$db_name = "db_sarpras"; 

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    $conn = new mysqli($db_host, $db_user, $db_pass, "db_sapras");
}
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

$conn->query("CREATE TABLE IF NOT EXISTS inventory_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_code VARCHAR(50) NOT NULL UNIQUE,
    item_name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'Multimedia',
    item_condition ENUM('Baik', 'Rusak Sedang', 'Rusak Berat') NOT NULL DEFAULT 'Baik',
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
foreach ([
    'inventory_id' => "ALTER TABLE borrowings ADD COLUMN inventory_id INT NULL",
    'stock_deducted' => "ALTER TABLE borrowings ADD COLUMN stock_deducted TINYINT(1) NOT NULL DEFAULT 0",
    'return_condition' => "ALTER TABLE borrowings ADD COLUMN return_condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat') NULL",
] as $column => $alter_sql) {
    $check = $conn->query("SHOW COLUMNS FROM borrowings LIKE '$column'");
    if ($check && $check->num_rows === 0) $conn->query($alter_sql);
}

$success_code = '';
$error_msg = '';

// -----------------------------------------------------------------------------
// 2. HANDLER PROSES PEMINJAMAN DARI PEMINJAM
// -----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['form_type']) && $_POST['form_type'] == 'public_borrowing') {
    $id = 'MAN1-' . date('Ymd') . '-' . rand(100, 999);
    $date = date('Y-m-d');
    $time = date('H:i');
    $name = $conn->real_escape_string($_POST['borrowerName']);
    $type = $_POST['borrowerType'];
    $id_num = $conn->real_escape_string($_POST['borrowerIdNum'] ?: '-');
    
    $inventory_id = intval($_POST['inventory_id'] ?? 0);
    $qty = intval($_POST['itemQty']);
    $purpose = $conn->real_escape_string($_POST['borrowPurpose']);
    $expected = $_POST['expectedReturnDate'];
    $condition = 'Baik';

    $asset = $inventory_id ? $conn->query("SELECT item_name, quantity FROM inventory_items WHERE id=$inventory_id") : false;
    if (!$asset || !$asset->num_rows) {
        $error_msg = "Pilih barang tersedia dari master inventaris.";
    } else {
        $asset = $asset->fetch_assoc();
        $item = $conn->real_escape_string($asset['item_name']);
        if ($qty < 1 || $qty > (int)$asset['quantity']) $error_msg = "Jumlah melebihi stok tersedia (" . (int)$asset['quantity'] . ").";
        else {
            $conn->begin_transaction();
            $sql = "INSERT INTO borrowings (id, date, time, name, type, id_num, item, qty, purpose, expected_return, `condition`, returned, inventory_id, stock_deducted) VALUES ('$id', '$date', '$time', '$name', '$type', '$id_num', '$item', $qty, '$purpose', '$expected', '$condition', 0, $inventory_id, 1)";
            if ($conn->query($sql) && $conn->query("UPDATE inventory_items SET quantity=quantity-$qty WHERE id=$inventory_id AND quantity >= $qty") && $conn->affected_rows === 1) { $conn->commit(); $success_code = $id; }
            else { $conn->rollback(); $error_msg = "Gagal menyimpan peminjaman; stok mungkin sudah berubah."; }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Peminjaman Barang - MAN 1 Palembang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        manGreen: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 500: '#22c55e',
                            600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d', 950: '#052e16',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-emerald-950 min-h-screen flex flex-col justify-between p-4 font-sans text-slate-800">

    <div class="max-w-xl w-full mx-auto my-auto">
        <!-- CARD UTAMA -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-emerald-800">
            
            <!-- HEADER -->
            <div class="bg-manGreen-800 p-6 text-white text-center relative">
                <img src="logo.png" alt="Logo MAN 1" class="w-16 h-16 mx-auto mb-2 object-contain bg-white rounded-full p-1 border border-emerald-300" onerror="this.src='https://via.placeholder.com/60?text=MAN1'">
                <h1 class="text-xl font-bold">MAN 1 PALEMBANG</h1>
                <p class="text-xs text-emerald-200 mt-1">Formulir Pengajuan Peminjaman Sarpras</p>
                <a href="index.php" class="absolute top-4 right-4 text-emerald-200 hover:text-white text-xs flex items-center gap-1 bg-manGreen-900/50 px-2.5 py-1 rounded-lg border border-emerald-700">
                    <i data-lucide="lock" class="w-3.5 h-3.5"></i> Login Petugas
                </a>
            </div>

            <!-- JIKA BERHASIL MENGAJUKAN -->
            <?php if (!empty($success_code)): ?>
                <div class="p-6 text-center space-y-4">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto">
                        <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">Pengajuan Peminjaman Berhasil!</h2>
                    <p class="text-xs text-slate-600">Silakan tunjukkan atau catat kode bukti peminjaman berikut kepada petugas Sarpras:</p>
                    
                    <div class="bg-emerald-50 border-2 border-dashed border-emerald-400 p-4 rounded-xl">
                        <span class="text-xs text-emerald-700 font-semibold uppercase block">Kode Peminjaman</span>
                        <span class="text-2xl font-mono font-extrabold text-manGreen-800 tracking-wider"><?= $success_code ?></span>
                    </div>

                    <div class="text-left bg-slate-50 p-4 rounded-xl border text-xs space-y-1 text-slate-600">
                        <p>• Tunjukkan kode ini saat mengambil barang.</p>
                        <p>• Pastikan mengembalikan barang sebelum tanggal estimasi pengembalian.</p>
                    </div>

                    <a href="peminjam.php" class="block w-full py-3 bg-manGreen-700 hover:bg-manGreen-800 text-white font-bold rounded-lg text-xs transition">
                        + Ajukan Peminjaman Lain
                    </a>
                </div>

            <?php else: ?>
                <!-- FORMULIR INPUT PEMINJAMAN -->
                <form method="POST" class="p-6 space-y-4 text-xs">
                    <input type="hidden" name="form_type" value="public_borrowing">

                    <?php if (!empty($error_msg)): ?>
                        <div class="p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-center font-medium">
                            <?= $error_msg ?>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                            <input type="text" name="borrowerName" required placeholder="Contoh: Muhammad Ali" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Kategori Peminjam *</label>
                            <select name="borrowerType" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none">
                                <option value="Siswa">Siswa</option>
                                <option value="Guru">Guru</option>
                                <option value="Pegawai">Pegawai / Staf</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Identitas (NISN / NIP / No. HP)</label>
                        <input type="text" name="borrowerIdNum" placeholder="Masukkan NISN/NIP atau No. WhatsApp" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none">
                    </div>

                    <div class="border-t pt-3">
                        <label class="block font-semibold text-slate-700 mb-1">Pilih Barang dari Master Inventaris *</label>
                        <div class="grid grid-cols-3 gap-2 mb-2">
                            <select id="presetItemSelect" name="inventory_id" required onchange="autoFillItem()" class="col-span-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none">
                                <option value="">-- Pilih barang tersedia --</option>
                                <?php $available_items = $conn->query("SELECT id, item_name, quantity FROM inventory_items WHERE quantity > 0 ORDER BY item_name"); if ($available_items): while ($available = $available_items->fetch_assoc()): ?>
                                <option value="<?= intval($available['id']) ?>" data-stock="<?= intval($available['quantity']) ?>"><?= htmlspecialchars($available['item_name']) ?> (tersedia <?= intval($available['quantity']) ?>)</option>
                                <?php endwhile; endif; ?>
                            </select>
                            <input type="number" name="itemQty" value="1" min="1" required class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-center font-bold focus:ring-2 focus:ring-emerald-600 outline-none" title="Jumlah Unit">
                        </div>
                        <p class="text-[11px] text-slate-500">Stok berkurang saat pengajuan tercatat dan kembali saat barang dikembalikan.</p>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Keperluan Peminjaman *</label>
                        <input type="text" name="borrowPurpose" required placeholder="Contoh: Mengajar KBM di Kelas XII IPA 1" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Rencana Tanggal Pengembalian *</label>
                        <input type="date" name="expectedReturnDate" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none">
                    </div>

                    <button type="submit" class="w-full py-3 bg-manGreen-700 hover:bg-manGreen-800 text-white font-bold rounded-lg text-xs transition shadow-md flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> Kirim Pengajuan Peminjaman
                    </button>
                </form>
            <?php endif; ?>

        </div>

        <p class="text-center text-[11px] text-emerald-300/80 mt-4">
            &copy; <?= date('Y') ?> Sarana & Prasarana MAN 1 Palembang. All rights reserved.
        </p>
    </div>

    <script>
        lucide.createIcons();
        function autoFillItem() {
            const select = document.getElementById('presetItemSelect');
            const option = select.options[select.selectedIndex];
            const qty = document.querySelector('[name="itemQty"]');
            qty.max = option.dataset.stock || '';
            if (option.dataset.stock) qty.value = Math.min(parseInt(qty.value || '1'), parseInt(option.dataset.stock));
        }
    </script>
</body>
</html>
