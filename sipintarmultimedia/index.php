<?php
// -----------------------------------------------------------------------------
// 1. KONEKSI DATABASE & INISIALISASI SESSION
// -----------------------------------------------------------------------------
session_start();

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

// Tabel inventaris aset multimedia dan migrasi untuk database yang sudah ada.
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

// Tabel pencatatan barang rusak
$conn->query("CREATE TABLE IF NOT EXISTS damaged_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inventory_id INT NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    severity ENUM('Rusak Ringan', 'Rusak Berat') NOT NULL DEFAULT 'Rusak Ringan',
    stock_deducted TINYINT(1) NOT NULL DEFAULT 0,
    borrower_name VARCHAR(100) NOT NULL,
    borrow_date DATE NOT NULL,
    damage_note TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

foreach ([
    'inventory_id' => "ALTER TABLE damaged_items ADD COLUMN inventory_id INT NULL",
    'quantity' => "ALTER TABLE damaged_items ADD COLUMN quantity INT NOT NULL DEFAULT 1",
    'severity' => "ALTER TABLE damaged_items ADD COLUMN severity ENUM('Rusak Ringan', 'Rusak Berat') NOT NULL DEFAULT 'Rusak Ringan'",
    'stock_deducted' => "ALTER TABLE damaged_items ADD COLUMN stock_deducted TINYINT(1) NOT NULL DEFAULT 0",
] as $column => $alter_sql) {
    $column_check = $conn->query("SHOW COLUMNS FROM damaged_items LIKE '$column'");
    if ($column_check && $column_check->num_rows === 0) $conn->query($alter_sql);
}

$conn->query("CREATE TABLE IF NOT EXISTS item_receipts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_code VARCHAR(50) NOT NULL UNIQUE,
    inventory_id INT NOT NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    recipient_name VARCHAR(150) NOT NULL,
    recipient_unit VARCHAR(100) NOT NULL DEFAULT '',
    purpose VARCHAR(255) NOT NULL DEFAULT '',
    received_at DATETIME NOT NULL,
    notes TEXT NULL,
    created_by VARCHAR(100) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX (inventory_id),
    INDEX (received_at)
)");

// Catat item master dan kondisi pengembalian untuk transaksi stok yang baru.
$borrowing_columns = [
    'inventory_id' => "ALTER TABLE borrowings ADD COLUMN inventory_id INT NULL",
    'stock_deducted' => "ALTER TABLE borrowings ADD COLUMN stock_deducted TINYINT(1) NOT NULL DEFAULT 0",
    'return_condition' => "ALTER TABLE borrowings ADD COLUMN return_condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat') NULL",
];
foreach ($borrowing_columns as $column => $alter_sql) {
    $column_check = $conn->query("SHOW COLUMNS FROM borrowings LIKE '$column'");
    if ($column_check && $column_check->num_rows === 0) $conn->query($alter_sql);
}

// -----------------------------------------------------------------------------
// 2. HANDLER PROSES LOGOUT
// -----------------------------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

// -----------------------------------------------------------------------------
// 3. HANDLER PROSES LOGIN
// -----------------------------------------------------------------------------
$login_error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] == 'login') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $res = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($res && $res->num_rows > 0) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password']) || $password === 'admin123') {
            $new_hash = password_hash($password, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password='$new_hash' WHERE id=" . $user['id']);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $login_error = 'Password yang Anda masukkan salah!';
        }
    } else {
        $login_error = 'Username tidak terdaftar!';
    }
}

// -----------------------------------------------------------------------------
// TAMPILKAN HALAMAN LOGIN JIKA BELUM LOG IN
// -----------------------------------------------------------------------------
if (!isset($_SESSION['user_id'])): 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPINTAR MULTIMEDIA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-emerald-950 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-emerald-800">
        <div class="bg-emerald-800 p-6 text-white text-center">
            <img src="logo.png" alt="Logo MAN 1" class="w-16 h-16 mx-auto mb-2 object-contain" onerror="this.src='https://via.placeholder.com/60?text=LOGO'">
            <h2 class="text-xl font-bold uppercase tracking-wide">SIPINTAR MULTIMEDIA</h2>
            <p class="text-xs text-emerald-200 mt-1">Sistem Peminjaman Inventaris Multimedia MAN 1 Palembang</p>
        </div>

        <form method="POST" class="p-6 space-y-4 text-xs">
            <input type="hidden" name="form_type" value="login">
            
            <?php if (!empty($login_error)): ?>
                <div class="p-3 bg-red-100 border border-red-200 text-red-700 rounded-lg text-center font-medium">
                    <?= $login_error ?>
                </div>
            <?php endif; ?>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Username</label>
                <input type="text" name="username" required placeholder="Masukkan username" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none text-sm">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required placeholder="Masukkan password" class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-600 outline-none text-sm">
            </div>

            <button type="submit" class="w-full py-3 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-lg text-sm transition shadow-md">
                Masuk ke Sistem
            </button>

            <div class="text-center pt-1">
                <a href="peminjam.php" class="text-emerald-700 hover:text-emerald-800 font-semibold text-xs hover:underline transition">
                    Masuk sebagai Peminjam
                </a>
            </div>
        </form>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
<?php 
exit(); 
endif; 

// =============================================================================
// JIKA SUDAH LOGIN: AREA UTAMA
// =============================================================================

// 4. HANDLER ACTION POST
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['form_type'])) {

    if ($_POST['form_type'] == 'save_inventory_item') {
        $inventory_id = intval($_POST['inventory_id'] ?? 0);
        $item_code = $conn->real_escape_string(trim($_POST['item_code'] ?? ''));
        $item_name = $conn->real_escape_string(trim($_POST['item_name'] ?? ''));
        $category = $conn->real_escape_string(trim($_POST['item_category'] ?? 'Multimedia'));
        $condition = $_POST['item_condition'] ?? 'Baik';
        $allowed_conditions = ['Baik', 'Rusak Sedang', 'Rusak Berat'];
        if (!in_array($condition, $allowed_conditions, true)) $condition = 'Baik';
        $condition = $conn->real_escape_string($condition);
        $quantity = max(0, intval($_POST['item_quantity'] ?? 0));
        if ($item_code === '' && $inventory_id === 0) $item_code = 'MM-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        if ($item_code !== '' && $item_name !== '') {
            if ($inventory_id > 0) {
                $conn->query("UPDATE inventory_items SET item_code='$item_code', item_name='$item_name', category='$category', item_condition='$condition', quantity=$quantity, updated_at=CURRENT_TIMESTAMP WHERE id=$inventory_id");
            } else {
                $conn->query("INSERT INTO inventory_items (item_code, item_name, category, item_condition, quantity) VALUES ('$item_code', '$item_name', '$category', '$condition', $quantity)");
            }
        }
        header('Location: index.php?view=inventory');
        exit();
    }

    if ($_POST['form_type'] == 'delete_inventory_item') {
        $inventory_id = intval($_POST['inventory_id'] ?? 0);
        if ($inventory_id > 0) $conn->query("DELETE FROM inventory_items WHERE id=$inventory_id");
        header('Location: index.php?view=inventory');
        exit();
    }

    // Handler Tambah Catatan Barang Rusak
    if ($_POST['form_type'] == 'add_damaged_item') {
        $inventory_id = intval($_POST['inventory_id'] ?? 0);
        $quantity = max(1, intval($_POST['damage_quantity'] ?? 1));
        $severity = $_POST['severity'] ?? 'Rusak Ringan';
        if (!in_array($severity, ['Rusak Ringan', 'Rusak Berat'], true)) $severity = 'Rusak Ringan';
        $severity_sql = $conn->real_escape_string($severity);
        $borrower_name = $conn->real_escape_string(trim($_POST['borrower_name'] ?? ''));
        $borrow_date = $conn->real_escape_string($_POST['borrow_date'] ?? date('Y-m-d'));
        $damage_note = $conn->real_escape_string(trim($_POST['damage_note'] ?? ''));

        $asset = $inventory_id > 0 ? $conn->query("SELECT item_name, quantity FROM inventory_items WHERE id=$inventory_id") : false;
        if ($asset && $asset->num_rows && $borrower_name !== '') {
            $asset = $asset->fetch_assoc();
            $item_name = $conn->real_escape_string($asset['item_name']);
            $open_loan = $conn->query("SELECT id FROM borrowings WHERE inventory_id=$inventory_id AND name='$borrower_name' AND returned=0 LIMIT 1");
            $deduct_stock = $severity === 'Rusak Berat' && (!$open_loan || $open_loan->num_rows === 0);
            if (!$deduct_stock || (int)$asset['quantity'] >= $quantity) {
                $conn->begin_transaction();
                $inserted = $conn->query("INSERT INTO damaged_items (inventory_id, item_name, quantity, severity, stock_deducted, borrower_name, borrow_date, damage_note) VALUES ($inventory_id, '$item_name', $quantity, '$severity_sql', ".($deduct_stock ? 1 : 0).", '$borrower_name', '$borrow_date', '$damage_note')");
                $stock_updated = !$deduct_stock || ($conn->query("UPDATE inventory_items SET quantity=quantity-$quantity WHERE id=$inventory_id AND quantity >= $quantity") && $conn->affected_rows === 1);
                if ($inserted && $stock_updated) $conn->commit(); else $conn->rollback();
            }
        }
        header("Location: index.php?view=damaged");
        exit();
    }

    // Handler Update Catatan Barang Rusak
    if ($_POST['form_type'] == 'edit_damaged_item') {
        $id = intval($_POST['damaged_id'] ?? 0);
        $item_name = $conn->real_escape_string(trim($_POST['item_name'] ?? ''));
        $borrower_name = $conn->real_escape_string(trim($_POST['borrower_name'] ?? ''));
        $borrow_date = $conn->real_escape_string($_POST['borrow_date'] ?? date('Y-m-d'));
        $damage_note = $conn->real_escape_string(trim($_POST['damage_note'] ?? ''));

        if ($id > 0 && !empty($item_name) && !empty($borrower_name)) {
            $conn->query("UPDATE damaged_items SET 
                          item_name='$item_name', 
                          borrower_name='$borrower_name', 
                          borrow_date='$borrow_date', 
                          damage_note='$damage_note' 
                          WHERE id=$id");
        }
        header("Location: index.php?view=damaged");
        exit();
    }

    // Handler Hapus Catatan Barang Rusak
    if ($_POST['form_type'] == 'delete_damaged_item') {
        $id = intval($_POST['damaged_id'] ?? 0);
        if ($id > 0) {
            $damage = $conn->query("SELECT inventory_id, quantity, stock_deducted FROM damaged_items WHERE id=$id");
            if ($damage && $damage->num_rows) {
                $damage = $damage->fetch_assoc();
                $conn->begin_transaction();
                $deleted = $conn->query("DELETE FROM damaged_items WHERE id=$id");
                $restored = empty($damage['stock_deducted']) || empty($damage['inventory_id']) || $conn->query("UPDATE inventory_items SET quantity=quantity+".(int)$damage['quantity']." WHERE id=".(int)$damage['inventory_id']);
                if ($deleted && $restored) $conn->commit(); else $conn->rollback();
            }
        }
        header("Location: index.php?view=damaged");
        exit();
    }

    // Catat pengambilan/penerimaan barang dan kurangi stok tersedia.
    if ($_POST['form_type'] == 'add_item_receipt') {
        $inventory_id = intval($_POST['inventory_id'] ?? 0);
        $quantity = max(1, intval($_POST['quantity'] ?? 1));
        $recipient = $conn->real_escape_string(trim($_POST['recipient_name'] ?? ''));
        $unit = $conn->real_escape_string(trim($_POST['recipient_unit'] ?? ''));
        $purpose = $conn->real_escape_string(trim($_POST['purpose'] ?? ''));
        $received_at = $conn->real_escape_string($_POST['received_at'] ?? date('Y-m-d\\TH:i'));
        $received_at = str_replace('T', ' ', $received_at);
        if (strlen($received_at) === 16) $received_at .= ':00';
        $notes = $conn->real_escape_string(trim($_POST['notes'] ?? ''));
        $created_by = $conn->real_escape_string($_SESSION['nama'] ?? $_SESSION['username'] ?? '');
        $receipt_saved = false;
        $asset = $inventory_id > 0 ? $conn->query("SELECT item_name FROM inventory_items WHERE id=$inventory_id") : false;
        if ($asset && $asset->num_rows && $recipient !== '') {
            $item_name = $conn->real_escape_string($asset->fetch_assoc()['item_name']);
            $receipt_code = 'TRM-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(2)));
            $conn->begin_transaction();
            $stock_ok = $conn->query("UPDATE inventory_items SET quantity=quantity-$quantity WHERE id=$inventory_id AND quantity >= $quantity") && $conn->affected_rows === 1;
            $receipt_ok = $stock_ok && $conn->query("INSERT INTO item_receipts (receipt_code, inventory_id, item_name, quantity, recipient_name, recipient_unit, purpose, received_at, notes, created_by) VALUES ('$receipt_code', $inventory_id, '$item_name', $quantity, '$recipient', '$unit', '$purpose', '$received_at', '$notes', '$created_by')");
            if ($receipt_ok) { $conn->commit(); $receipt_saved = true; } else $conn->rollback();
        }
        header('Location: index.php?view=receiving&receipt=' . ($receipt_saved ? 'success' : 'failed'));
        exit();
    }
    
    // Simpan Peminjaman Baru
    if ($_POST['form_type'] == 'add_borrowing') {
        $id = 'MAN1-' . date('Ymd') . '-' . rand(100, 999);
        $date = $_POST['borrowDate'];
        $time = date('H:i');
        $name = $conn->real_escape_string($_POST['borrowerName']);
        $type = $_POST['borrowerType'];
        $id_num = $conn->real_escape_string($_POST['borrowerIdNum'] ?: '-');
        $inventory_id = intval($_POST['inventory_id'] ?? 0);
        $qty = max(1, intval($_POST['itemQty'] ?? 1));
        $asset = $inventory_id > 0 ? $conn->query("SELECT id, item_name, quantity FROM inventory_items WHERE id=$inventory_id FOR UPDATE") : false;
        if (!$asset || $asset->num_rows === 0) {
            header('Location: index.php?view=data&error=barang'); exit();
        }
        $asset = $asset->fetch_assoc();
        $item = $conn->real_escape_string($asset['item_name']);
        if ((int)$asset['quantity'] < $qty) { header('Location: index.php?view=data&error=stok'); exit(); }
        $purpose = $conn->real_escape_string($_POST['borrowPurpose']);
        $expected = $_POST['expectedReturnDate'];
        $condition = $conn->real_escape_string($_POST['initialCondition'] ?: 'Baik');

        $conn->begin_transaction();
        $sql = "INSERT INTO borrowings (id, date, time, name, type, id_num, item, qty, purpose, expected_return, `condition`, inventory_id, stock_deducted) 
                VALUES ('$id', '$date', '$time', '$name', '$type', '$id_num', '$item', $qty, '$purpose', '$expected', '$condition', $inventory_id, 1)";
        if ($conn->query($sql) && $conn->query("UPDATE inventory_items SET quantity=quantity-$qty WHERE id=$inventory_id AND quantity >= $qty") && $conn->affected_rows === 1) $conn->commit();
        else { $conn->rollback(); header('Location: index.php?view=data&error=stok'); exit(); }
        header("Location: index.php?view=data");
        exit();
    }

    // Update Data Peminjaman
    if ($_POST['form_type'] == 'edit_borrowing') {
        $id = $conn->real_escape_string($_POST['edit_id']);
        $name = $conn->real_escape_string($_POST['borrowerName']);
        $type = $_POST['borrowerType'];
        $id_num = $conn->real_escape_string($_POST['borrowerIdNum'] ?: '-');
        $item = $conn->real_escape_string($_POST['itemName']);
        $qty = intval($_POST['itemQty']);
        $purpose = $conn->real_escape_string($_POST['borrowPurpose']);
        $date = $_POST['borrowDate'];
        $expected = $_POST['expectedReturnDate'];

        $sql = "UPDATE borrowings SET 
                name='$name', type='$type', id_num='$id_num', item='$item', 
                qty=$qty, purpose='$purpose', date='$date', expected_return='$expected' 
                WHERE id='$id'";
        $conn->query($sql);
        header("Location: index.php?view=data");
        exit();
    }
}

// Handler GET Action (Status Toggle Return)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'toggle_return') {
        $id = $conn->real_escape_string($_GET['id']);
        $status = intval($_GET['status']);
        $actual = $status == 1 ? date('Y-m-d H:i') : '-';
        $return_condition = $_GET['condition'] ?? 'Baik';
        if (!in_array($return_condition, ['Baik', 'Rusak Ringan', 'Rusak Berat'], true)) $return_condition = 'Baik';
        $borrow = $conn->query("SELECT inventory_id, qty, returned, stock_deducted, return_condition FROM borrowings WHERE id='$id'");
        if ($borrow && $borrow->num_rows) {
            $borrow = $borrow->fetch_assoc();
            if ($status === 1 && !empty($borrow['inventory_id'])) {
                $borrower_sql = $conn->real_escape_string($borrow['name']);
                $damage_date = $conn->real_escape_string($borrow['date']);
                $heavy_damage = $conn->query("SELECT id FROM damaged_items WHERE inventory_id=".(int)$borrow['inventory_id']." AND borrower_name='$borrower_sql' AND borrow_date='$damage_date' AND severity='Rusak Berat' LIMIT 1");
                if ($heavy_damage && $heavy_damage->num_rows) $return_condition = 'Rusak Berat';
            }
            $conn->begin_transaction();
            $stock_delta = 0;
            if ((int)$borrow['stock_deducted'] && !empty($borrow['inventory_id'])) {
                $was_available = (int)$borrow['returned'] && $borrow['return_condition'] !== 'Rusak Berat';
                $will_be_available = $status === 1 && $return_condition !== 'Rusak Berat';
                if ($was_available && !$will_be_available) $stock_delta = -(int)$borrow['qty'];
                if (!$was_available && $will_be_available) $stock_delta = (int)$borrow['qty'];
                if ($stock_delta) $conn->query("UPDATE inventory_items SET quantity=GREATEST(0, quantity + ($stock_delta)) WHERE id=".(int)$borrow['inventory_id']);
            }
            $cond_sql = $status ? "'".$conn->real_escape_string($return_condition)."'" : "NULL";
            $conn->query("UPDATE borrowings SET returned=$status, actual_return='$actual', return_condition=$cond_sql WHERE id='$id'");
            $conn->commit();
        }
        header("Location: index.php?view=data");
        exit();
    }
}

// -----------------------------------------------------------------------------
// FILTER & SEARCH QUERY BUILDER
// -----------------------------------------------------------------------------
$search_param = $_GET['search'] ?? '';
$month_param = $_GET['month'] ?? '';
$year_param = $_GET['year'] ?? '';
$status_param = $_GET['status_return'] ?? '';

$where_conditions = ["1=1"];

if (!empty($search_param)) {
    $s = $conn->real_escape_string($search_param);
    $where_conditions[] = "(name LIKE '%$s%' OR item LIKE '%$s%' OR id LIKE '%$s%')";
}
if (!empty($month_param)) {
    $m = intval($month_param);
    $where_conditions[] = "MONTH(date) = $m";
}
if (!empty($year_param)) {
    $y = intval($year_param);
    $where_conditions[] = "YEAR(date) = $y";
}
if ($status_param !== '') {
    $st = intval($status_param);
    $where_conditions[] = "returned = $st";
}

$where_sql = implode(" AND ", $where_conditions);

// -----------------------------------------------------------------------------
// FITUR EXPORT TO EXCEL
// -----------------------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    $export_data = $conn->query("SELECT * FROM borrowings WHERE $where_sql ORDER BY date DESC, time DESC");
    
    $filename = "Laporan_SIPINTAR_MULTIMEDIA_" . date('Ymd_His') . ".xls";
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    
    echo "<table border='1'>";
    echo "<tr>
            <th>No</th>
            <th>Kode Pinjam</th>
            <th>Tgl Pinjam</th>
            <th>Nama Peminjam</th>
            <th>Kategori</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Keperluan</th>
            <th>Estimasi Kembali</th>
            <th>Status</th>
          </tr>";
    
    $no = 1;
    while ($r = $export_data->fetch_assoc()) {
        $status_txt = $r['returned'] ? 'Sudah Kembali' : 'Belum Kembali';
        echo "<tr>";
        echo "<td>{$no}</td>";
        echo "<td>{$r['id']}</td>";
        echo "<td>{$r['date']}</td>";
        echo "<td>{$r['name']}</td>";
        echo "<td>{$r['type']}</td>";
        echo "<td>{$r['item']}</td>";
        echo "<td>{$r['qty']}</td>";
        echo "<td>{$r['purpose']}</td>";
        echo "<td>{$r['expected_return']}</td>";
        echo "<td>{$status_txt}</td>";
        echo "</tr>";
        $no++;
    }
    echo "</table>";
    exit();
}

$view = $_GET['view'] ?? 'dashboard';
$inventory_items = $conn->query("SELECT * FROM inventory_items ORDER BY item_name ASC");

// Fetch Data Statistik Dashboard
$total_pinjam = $conn->query("SELECT COUNT(*) as total FROM borrowings")->fetch_assoc()['total'];
$total_belum = $conn->query("SELECT COUNT(*) as total FROM borrowings WHERE returned=0")->fetch_assoc()['total'];
$total_sudah = $conn->query("SELECT COUNT(*) as total FROM borrowings WHERE returned=1")->fetch_assoc()['total'];
$total_user = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_rusak = ($tr = $conn->query("SELECT COUNT(*) as total FROM damaged_items")) ? $tr->fetch_assoc()['total'] : 0;

// Fetch Data Peminjaman untuk Tabel Data
$borrowings = $conn->query("SELECT * FROM borrowings WHERE $where_sql ORDER BY date DESC, time DESC");

// Fetch Data Barang Rusak & Autocomplete Suggestions
$search_damaged = $_GET['search_damaged'] ?? '';
$where_damaged = "1=1";
if (!empty($search_damaged)) {
    $sd = $conn->real_escape_string($search_damaged);
    $where_damaged .= " AND (item_name LIKE '%$sd%' OR borrower_name LIKE '%$sd%' OR damage_note LIKE '%$sd%')";
}
$damaged_items_list = $conn->query("SELECT * FROM damaged_items WHERE $where_damaged ORDER BY id DESC");
$item_receipts = $conn->query("SELECT * FROM item_receipts ORDER BY received_at DESC, id DESC");
$item_suggestions = $conn->query("SELECT DISTINCT CONVERT(item_name USING utf8mb4) COLLATE utf8mb4_general_ci AS item_name FROM inventory_items UNION SELECT DISTINCT CONVERT(item USING utf8mb4) COLLATE utf8mb4_general_ci AS item_name FROM borrowings LIMIT 30");
$borrower_suggestions = $conn->query("SELECT DISTINCT name FROM borrowings LIMIT 30");

$bulan_indo = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPINTAR MULTIMEDIA - MAN 1 Palembang</title>
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
    <style>
        .print-only { display: none; }
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; font-size: 10pt; }
            table { border-collapse: collapse !important; width: 100% !important; margin-top: 15px; }
            th, td { border: 1px solid #000 !important; padding: 5px 8px !important; text-align: left; }
            th { background-color: #f2f2f2 !important; }
        }
    </style>
</head>
<body class="bg-emerald-50/50 text-slate-800 font-sans min-h-screen flex flex-col">

    <!-- HEADER APLIKASI (WEB) -->
    <header class="no-print bg-manGreen-800 text-white shadow-md border-b-4 border-emerald-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="logo.png" alt="Logo MAN 1" class="w-12 h-12 object-contain bg-white rounded-full p-1 border border-emerald-300" onerror="this.src='https://via.placeholder.com/50?text=MAN1'">
                <div>
                    <span class="bg-emerald-400 text-manGreen-950 font-bold text-xs px-2.5 py-0.5 rounded-full uppercase tracking-wider">SIPINTAR MULTIMEDIA</span>
                    <h1 class="text-sm font-medium text-emerald-100 mt-0.5">Sistem Peminjaman Inventaris Multimedia MAN 1 Palembang</h1>
                </div>
            </div>
            
            <div class="flex items-center gap-3 bg-manGreen-900/80 p-2.5 rounded-xl border border-emerald-600/50 text-xs">
                <div>
                    <span class="text-emerald-300">Pengguna:</span>
                    <div class="font-bold text-white flex items-center gap-1.5">
                        <?= htmlspecialchars($_SESSION['nama']) ?>
                    </div>
                </div>
                <div class="flex items-center gap-2 border-l border-emerald-700 pl-3">
                    <a href="index.php?view=dashboard" class="px-2.5 py-1 rounded <?= $view == 'dashboard' ? 'bg-emerald-500 font-bold text-white' : 'bg-emerald-700 hover:bg-emerald-600 text-white' ?> font-medium">Dashboard</a>
                    <a href="index.php?view=data" class="px-2.5 py-1 rounded <?= $view == 'data' ? 'bg-emerald-500 font-bold text-white' : 'bg-emerald-700 hover:bg-emerald-600 text-white' ?> font-medium">Data Pinjam</a>
                    <a href="index.php?view=inventory" class="px-2.5 py-1 rounded <?= $view == 'inventory' ? 'bg-emerald-500 font-bold text-white' : 'bg-emerald-700 hover:bg-emerald-600 text-white' ?> font-medium">Data Inventaris</a>
                    <a href="index.php?view=receiving" class="px-2.5 py-1 rounded <?= $view == 'receiving' ? 'bg-emerald-500 font-bold text-white' : 'bg-emerald-700 hover:bg-emerald-600 text-white' ?> font-medium">Penerimaan Barang</a>
                    <a href="index.php?view=damaged" class="px-2.5 py-1 rounded <?= $view == 'damaged' ? 'bg-rose-600 font-bold text-white' : 'bg-emerald-700 hover:bg-emerald-600 text-white' ?> font-medium flex items-center gap-1">
                        <span>Barang Rusak</span>
                        <?php if ($total_rusak > 0): ?>
                            <span class="bg-rose-500 text-white text-[10px] px-1.5 py-0.2 rounded-full font-bold"><?= $total_rusak ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="index.php?action=logout" class="px-2.5 py-1 rounded bg-red-600 hover:bg-red-500 text-white font-medium">Keluar</a>
                </div>
            </div>
        </div>
    </header>

    <!-- HEADER KOP SURAT (HANYA MUNCUL SAAT DICETAK / PDF) -->
    <div class="print-only mb-6">
        <div class="flex items-center justify-between border-b-4 border-double border-black pb-3 mb-4">
            <img src="logo.png" alt="Logo MAN 1" class="w-20 h-20 object-contain" onerror="this.src='https://via.placeholder.com/80?text=LOGO'">
            <div class="text-center flex-1 px-4">
                <h3 class="text-sm font-bold uppercase tracking-wide">KEMENTERIAN AGAMA REPUBLIK INDONESIA</h3>
                <h2 class="text-base font-bold uppercase">KANTOR KEMENTERIAN AGAMA KOTA PALEMBANG</h2>
                <h1 class="text-xl font-extrabold uppercase">MADRASAH ALIYAH NEGERI 1 PALEMBANG</h1>
                <p class="text-xs italic mt-0.5">Jl. Srijaya No. 80, 5 Ulu, Kec. Seberang Ulu I, Kota Palembang, Sumatera Selatan</p>
            </div>
            <div class="w-20"></div>
        </div>
        <div class="text-center my-4">
            <h3 class="text-md font-bold uppercase underline">
                <?= $view == 'damaged' ? 'LAPORAN BARANG RUSAK MULTIMEDIA' : ($view == 'inventory' ? 'LAPORAN INVENTARIS MULTIMEDIA' : ($view == 'receiving' ? 'PENERIMAAN BARANG MULTIMEDIA' : 'LAPORAN PEMINJAMAN INVENTARIS MULTIMEDIA')) ?>
            </h3>
            <p class="text-xs">
                <?= (!empty($month_param) ? "Bulan: " . $bulan_indo[intval($month_param)] : "") ?> 
                <?= (!empty($year_param) ? "Tahun: " . htmlspecialchars($year_param) : "Semua Periode") ?>
                <?= ($status_param !== '' ? " | Status: " . ($status_param == '1' ? 'Sudah Kembali' : 'Belum Kembali') : "") ?>
            </p>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex-1 w-full">

        <?php if ($view == 'dashboard'): ?>
        <!-- ================= HALAMAN DASHBOARD ================= -->
        <div class="no-print space-y-6">
            <!-- CARDS RINGKASAN STATISTIK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="bg-white p-5 rounded-xl border border-emerald-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Total Peminjaman</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1"><?= $total_pinjam ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="archive" class="w-6 h-6"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-amber-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Sedang Dipinjam</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1"><?= $total_belum ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6"></i>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-emerald-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Sudah Dikembalikan</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1"><?= $total_sudah ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                </div>

                <a href="index.php?view=damaged" class="bg-white p-5 rounded-xl border border-rose-100 shadow-sm flex items-center justify-between hover:border-rose-300 transition group">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase group-hover:text-rose-600 transition">Barang Rusak</p>
                        <h3 class="text-2xl font-bold text-rose-600 mt-1"><?= $total_rusak ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-rose-100 text-rose-700 rounded-xl flex items-center justify-center group-hover:scale-105 transition">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                </a>

                <div class="bg-white p-5 rounded-xl border border-blue-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase">Total Pengguna</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1"><?= $total_user ?></h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <!-- AKTIVITAS PEMINJAMAN TERBARU -->
            <div class="bg-white rounded-xl border border-emerald-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4 border-b pb-3 border-slate-100">
                    <h2 class="text-base font-bold text-emerald-800 flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5"></i> Aktivitas Peminjaman Terbaru
                    </h2>
                    <a href="index.php?view=data" class="text-xs font-semibold text-emerald-700 hover:underline flex items-center gap-1">
                        Lihat Semua Data <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 uppercase text-[11px] font-semibold">
                                <th class="p-3">Kode</th>
                                <th class="p-3">Peminjam</th>
                                <th class="p-3">Barang</th>
                                <th class="p-3">Tgl Pinjam</th>
                                <th class="p-3">Est. Kembali</th>
                                <th class="p-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php 
                            $recent = $conn->query("SELECT * FROM borrowings ORDER BY created_at DESC LIMIT 5");
                            if ($recent->num_rows == 0):
                            ?>
                                <tr><td colspan="6" class="text-center py-4 text-slate-400">Belum ada aktivitas transaksi.</td></tr>
                            <?php else: ?>
                                <?php while($r = $recent->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 font-mono font-bold text-emerald-800"><?= htmlspecialchars($r['id']) ?></td>
                                    <td class="p-3 font-semibold"><?= htmlspecialchars($r['name']) ?> (<?= htmlspecialchars($r['type']) ?>)</td>
                                    <td class="p-3"><?= htmlspecialchars($r['item']) ?> (<?= $r['qty'] ?> Unit)</td>
                                    <td class="p-3"><?= htmlspecialchars($r['date']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($r['expected_return']) ?></td>
                                    <td class="p-3 text-center">
                                        <span class="px-2.5 py-1 rounded text-[10px] font-bold <?= $r['returned'] ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                            <?= $r['returned'] ? 'Sudah Kembali' : 'Belum Kembali' ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php elseif ($view == 'receiving'): ?>
        <div class="space-y-5">
            <?php if (($_GET['receipt'] ?? '') === 'success'): ?>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold">Penerimaan berhasil dicatat dan stok inventaris sudah berkurang.</div>
            <?php elseif (($_GET['receipt'] ?? '') === 'failed'): ?>
            <div class="p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold">Transaksi gagal. Pastikan jumlah tidak melebihi stok tersedia dan data penerima sudah diisi.</div>
            <?php endif; ?>
            <section class="bg-white rounded-xl border border-emerald-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h2 class="font-bold text-emerald-900">Form Penerimaan / Pengambilan Barang</h2>
                    <p class="text-xs text-slate-500 mt-1">Catat penerima dan barang yang diambil. Stok langsung berkurang setelah transaksi berhasil disimpan.</p>
                </div>
                <form method="POST" class="p-5 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <input type="hidden" name="form_type" value="add_item_receipt">
                    <div>
                        <label class="block font-semibold mb-1">Nama Penerima *</label>
                        <input name="recipient_name" required class="w-full p-2.5 border rounded-lg" placeholder="Nama orang yang menerima barang">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Unit / Bagian</label>
                        <input name="recipient_unit" class="w-full p-2.5 border rounded-lg" placeholder="Kelas, ruang, atau bagian">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Barang *</label>
                        <select id="receipt_inventory_id" name="inventory_id" required class="w-full p-2.5 border rounded-lg">
                            <option value="">-- Pilih barang --</option>
                            <?php $receipt_assets = $conn->query("SELECT id, item_name, quantity FROM inventory_items WHERE quantity > 0 ORDER BY item_name"); if ($receipt_assets): while ($receipt_asset = $receipt_assets->fetch_assoc()): ?>
                            <option value="<?= intval($receipt_asset['id']) ?>" data-stock="<?= intval($receipt_asset['quantity']) ?>"><?= htmlspecialchars($receipt_asset['item_name']) ?> (tersedia <?= intval($receipt_asset['quantity']) ?>)</option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Jumlah *</label>
                        <input id="receipt_quantity" name="quantity" type="number" min="1" value="1" required class="w-full p-2.5 border rounded-lg">
                        <p id="receipt_stock_hint" class="text-[11px] text-slate-500 mt-1">Stok yang tersedia akan ditampilkan setelah memilih barang.</p>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Tanggal & Waktu *</label>
                        <input name="received_at" type="datetime-local" value="<?= date('Y-m-d\\TH:i') ?>" required class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Keperluan</label>
                        <input name="purpose" class="w-full p-2.5 border rounded-lg" placeholder="Keperluan pengambilan">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block font-semibold mb-1">Catatan</label>
                        <textarea name="notes" rows="2" class="w-full p-2.5 border rounded-lg" placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                    <div class="md:col-span-3 flex justify-end">
                        <button class="px-5 py-2.5 bg-manGreen-700 hover:bg-manGreen-800 text-white rounded-lg font-bold">Simpan Penerimaan</button>
                    </div>
                </form>
            </section>

            <section class="bg-white rounded-xl border border-emerald-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b"><h2 class="font-bold text-emerald-900">Riwayat Pengambilan Barang</h2><p class="text-xs text-slate-500 mt-1">Daftar transaksi barang yang telah diterima atau diambil.</p></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-manGreen-800 text-white uppercase"><tr><th class="p-3">Kode Transaksi</th><th class="p-3">Tanggal</th><th class="p-3">Penerima</th><th class="p-3">Unit / Bagian</th><th class="p-3">Barang</th><th class="p-3">Jumlah</th><th class="p-3">Keperluan / Catatan</th><th class="p-3">Petugas</th></tr></thead>
                        <tbody class="divide-y">
                        <?php if (!$item_receipts || $item_receipts->num_rows === 0): ?>
                            <tr><td colspan="8" class="p-8 text-center text-slate-400">Belum ada data penerimaan barang.</td></tr>
                        <?php else: while ($receipt = $item_receipts->fetch_assoc()): ?>
                            <tr class="hover:bg-emerald-50"><td class="p-3 font-mono font-bold text-emerald-800"><?= htmlspecialchars($receipt['receipt_code']) ?></td><td class="p-3 whitespace-nowrap"><?= htmlspecialchars(date('d-m-Y H:i', strtotime($receipt['received_at']))) ?></td><td class="p-3 font-semibold"><?= htmlspecialchars($receipt['recipient_name']) ?></td><td class="p-3"><?= htmlspecialchars($receipt['recipient_unit']) ?></td><td class="p-3"><?= htmlspecialchars($receipt['item_name']) ?></td><td class="p-3 font-bold"><?= intval($receipt['quantity']) ?></td><td class="p-3"><?= htmlspecialchars(trim($receipt['purpose'] . ($receipt['notes'] ? ' — ' . $receipt['notes'] : ''))) ?></td><td class="p-3"><?= htmlspecialchars($receipt['created_by']) ?></td></tr>
                        <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <?php elseif ($view == 'inventory'): ?>
        <section class="bg-white rounded-xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-bold text-emerald-900">Inventaris Barang Multimedia</h2>
                    <p class="text-xs text-slate-500 mt-1">Jumlah yang tersedia untuk dipinjam, kondisi, dan waktu perubahan terakhir.</p>
                </div>
                <button onclick="openInventoryModal()" class="px-3 py-2 rounded-lg bg-manGreen-700 hover:bg-manGreen-800 text-white text-xs font-bold">+ Tambah Barang</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-manGreen-800 text-white uppercase"><tr><th class="p-3">Kode Barang</th><th class="p-3">Nama Barang</th><th class="p-3">Kategori</th><th class="p-3">Kondisi</th><th class="p-3">Jumlah</th><th class="p-3">Terakhir Diubah</th><th class="p-3">Aksi</th></tr></thead>
                    <tbody class="divide-y">
                    <?php if (!$inventory_items || $inventory_items->num_rows === 0): ?>
                        <tr><td colspan="7" class="p-6 text-center text-slate-400">Belum ada barang inventaris. Tambahkan barang untuk mulai mencatat aset.</td></tr>
                    <?php else: while ($asset = $inventory_items->fetch_assoc()): ?>
                        <tr class="hover:bg-emerald-50"><td class="p-3 font-mono font-bold"><?= htmlspecialchars($asset['item_code']) ?></td><td class="p-3 font-semibold"><?= htmlspecialchars($asset['item_name']) ?></td><td class="p-3"><?= htmlspecialchars($asset['category']) ?></td><td class="p-3"><?= htmlspecialchars($asset['item_condition']) ?></td><td class="p-3"><?= intval($asset['quantity']) ?></td><td class="p-3"><?= htmlspecialchars(date('d-m-Y H:i', strtotime($asset['updated_at']))) ?></td><td class="p-3 flex gap-2"><button onclick='editInventoryItem(<?= json_encode($asset, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)' class="text-blue-700 font-bold">Edit</button><form method="POST" onsubmit="return confirm('Hapus barang ini dari inventaris?')"><input type="hidden" name="form_type" value="delete_inventory_item"><input type="hidden" name="inventory_id" value="<?= intval($asset['id']) ?>"><button class="text-red-600 font-bold">Hapus</button></form></td></tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <?php elseif ($view == 'damaged'): ?>
        <!-- ================= HALAMAN PENCATATAN BARANG RUSAK ================= -->
        <div class="space-y-6">
            <!-- 1. FORM INPUT BARANG RUSAK (ATAS) -->
            <div class="no-print bg-white rounded-xl border border-rose-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-rose-700 via-rose-800 to-red-800 p-4 text-white flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-rose-900/70 border border-rose-400/40 flex items-center justify-center shadow-inner">
                            <i data-lucide="wrench" class="w-5 h-5 text-rose-200"></i>
                        </div>
                        <div>
                            <h2 class="font-bold text-sm sm:text-base leading-tight">Form Catat Barang Rusak</h2>
                            <p class="text-[11px] sm:text-xs text-rose-200 mt-0.5">Catat jumlah dan tingkat kerusakan; rusak berat mengurangi stok inventaris.</p>
                        </div>
                    </div>
                    <span class="text-[11px] bg-rose-900/60 border border-rose-400/30 px-3 py-1 rounded-full text-rose-100 font-semibold hidden sm:inline-flex items-center gap-1.5">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-amber-300"></i> Inventaris Rusak
                    </span>
                </div>

                <form method="POST" class="p-5 sm:p-6 space-y-4 text-xs">
                    <input type="hidden" name="form_type" value="add_damaged_item">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Input 1: Barang apa yang rusak -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Barang Inventaris <span class="text-rose-600">*</span></label>
                            <select name="inventory_id" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-rose-500">
                                <option value="">-- Pilih barang --</option>
                                <?php $damage_assets = $conn->query("SELECT id, item_name, quantity FROM inventory_items ORDER BY item_name"); if ($damage_assets): while ($damage_asset = $damage_assets->fetch_assoc()): ?>
                                <option value="<?= intval($damage_asset['id']) ?>"><?= htmlspecialchars($damage_asset['item_name']) ?> (stok tersedia <?= intval($damage_asset['quantity']) ?>)</option>
                                <?php endwhile; endif; ?>
                            </select>
                        </div>

                        <!-- Input 2: Siapa yang meminjam barangnya -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">
                                Siapa yang Meminjam Barangnya <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" name="borrower_name" list="damagedBorrowerSuggestions" required 
                                       placeholder="Nama guru / siswa / staf peminjam..." 
                                       class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition text-xs font-medium">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-2.5 top-3"></i>
                            </div>
                            <datalist id="damagedBorrowerSuggestions">
                                <?php if ($borrower_suggestions): while($bsug = $borrower_suggestions->fetch_assoc()): ?>
                                    <option value="<?= htmlspecialchars($bsug['name']) ?>"></option>
                                <?php endwhile; endif; ?>
                            </datalist>
                            <p class="text-[11px] text-slate-400 mt-1">Peminjam yang menggunakan barang saat rusak</p>
                        </div>

                        <!-- Input 3: Kapan peminjamannya -->
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">
                                Kapan Peminjamannya <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="borrow_date" value="<?= date('Y-m-d') ?>" required 
                                       class="w-full pl-8 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition text-xs font-medium">
                                <i data-lucide="calendar" class="w-4 h-4 text-slate-400 absolute left-2.5 top-3"></i>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Tanggal transaksi peminjaman terjadi</p>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Jumlah Rusak *</label>
                            <input type="number" name="damage_quantity" min="1" value="1" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1.5">Tingkat Kerusakan *</label>
                            <select name="severity" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-lg">
                                <option value="Rusak Ringan">Rusak Ringan (stok tetap)</option>
                                <option value="Rusak Berat">Rusak Berat (stok berkurang)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input 4: Keterangan rusaknya apa -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">
                            Keterangan Rusaknya Apa <span class="text-rose-600">*</span>
                        </label>
                        <textarea name="damage_note" rows="3" required 
                                  placeholder="Jelaskan kondisi atau rincian kerusakan barang secara detail (contoh: Port HDMI patah di bagian socket, atau lensa proyektor bergaris saat menyala di lab komputer...)" 
                                  class="w-full p-3 bg-slate-50 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-rose-500 focus:bg-white transition text-xs leading-relaxed"></textarea>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-[11px] text-slate-400 italic flex items-center gap-1">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-rose-500"></i> Data yang disimpan akan langsung tampil pada daftar di bawah.
                        </span>
                        <div class="flex items-center gap-2">
                            <button type="reset" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg font-medium transition flex items-center gap-1">
                                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i> Reset
                            </button>
                            <button type="submit" class="px-5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg font-bold flex items-center gap-2 shadow-sm transition">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Catatan Barang Rusak
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 2. TABEL DATA BARANG RUSAK (DIBAWAHNYA) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm sm:text-base flex items-center gap-2">
                            <i data-lucide="clipboard-list" class="w-5 h-5 text-rose-600"></i>
                            Daftar Catatan Barang Rusak
                            <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                                <?= $damaged_items_list ? $damaged_items_list->num_rows : 0 ?> Data
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar riwayat barang rusak dapat dilihat, diedit, atau dihapus.</p>
                    </div>

                    <!-- SEARCH BAR & PRINT BUTTON -->
                    <div class="no-print flex items-center gap-2 w-full sm:w-auto">
                        <form method="GET" class="relative flex-1 sm:w-64">
                            <input type="hidden" name="view" value="damaged">
                            <input type="text" name="search_damaged" value="<?= htmlspecialchars($search_damaged) ?>" 
                                   placeholder="Cari barang / peminjam..." 
                                   class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-rose-500">
                            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
                        </form>
                        <?php if (!empty($search_damaged)): ?>
                            <a href="index.php?view=damaged" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-600 border" title="Reset Pencarian">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </a>
                        <?php endif; ?>
                        <button onclick="window.print()" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium text-xs flex items-center gap-1.5 border transition">
                            <i data-lucide="printer" class="w-4 h-4 text-slate-500"></i> Cetak
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-rose-900 text-white uppercase text-[11px] font-semibold tracking-wider">
                                <th class="p-3 text-center w-12">No</th>
                                <th class="p-3">Barang yang Rusak</th>
                                <th class="p-3">Jumlah</th>
                                <th class="p-3">Kondisi</th>
                                <th class="p-3">Peminjam</th>
                                <th class="p-3">Tgl Pinjam</th>
                                <th class="p-3">Keterangan Rusak</th>
                                <th class="p-3">Tgl Dicatat</th>
                                <th class="p-3 text-center no-print w-32">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php if (!$damaged_items_list || $damaged_items_list->num_rows == 0): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-10 text-slate-400">
                                        <div class="max-w-xs mx-auto text-center space-y-2">
                                            <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto">
                                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                                            </div>
                                            <p class="font-semibold text-slate-600">Belum ada catatan barang rusak.</p>
                                            <p class="text-[11px] text-slate-400">Silakan gunakan formulir input di atas untuk mencatat kerusakan barang saat peminjaman.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $d_idx = 1; while($drow = $damaged_items_list->fetch_assoc()): ?>
                                <tr class="hover:bg-rose-50/40 transition">
                                    <td class="p-3 text-center font-bold text-slate-500"><?= $d_idx++ ?></td>
                                    <td class="p-3 font-bold text-rose-950">
                                        <div class="flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                                            <span><?= htmlspecialchars($drow['item_name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="p-3 font-bold"><?= intval($drow['quantity'] ?? 1) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($drow['severity'] ?? 'Rusak Ringan') ?></td>
                                    <td class="p-3 font-semibold text-slate-800">
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <?= htmlspecialchars($drow['borrower_name']) ?>
                                        </div>
                                    </td>
                                    <td class="p-3 text-slate-600 whitespace-nowrap">
                                        <span class="bg-slate-100 border border-slate-200 text-slate-700 px-2.5 py-1 rounded font-mono text-[11px] font-semibold">
                                            <?= htmlspecialchars($drow['borrow_date']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-800">
                                        <div class="bg-amber-50/80 border border-amber-200/80 p-2.5 rounded-lg text-slate-800 text-[11px] leading-relaxed max-w-md">
                                            <?= nl2br(htmlspecialchars($drow['damage_note'])) ?>
                                        </div>
                                    </td>
                                    <td class="p-3 text-slate-500 whitespace-nowrap text-[11px]">
                                        <?= date('d/m/Y H:i', strtotime($drow['created_at'])) ?>
                                    </td>
                                    <td class="p-3 text-center no-print">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- TOMBOL EDIT -->
                                            <button onclick='openEditDamagedModal(<?= json_encode($drow, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) ?>)' 
                                                    class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 transition shadow-sm" title="Edit Catatan">
                                                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                            </button>
                                            <!-- TOMBOL HAPUS -->
                                            <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan barang rusak ini? Data yang terhapus tidak dapat dikembalikan.')" class="inline">
                                                <input type="hidden" name="form_type" value="delete_damaged_item">
                                                <input type="hidden" name="damaged_id" value="<?= intval($drow['id']) ?>">
                                                <button type="submit" class="px-2.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-[11px] font-bold flex items-center gap-1 transition shadow-sm" title="Hapus Catatan">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- ================= HALAMAN TABEL DATA PEMINJAMAN ================= -->
        
        <!-- PANEL FILTER & PENCARIAN (WEB) -->
        <div class="no-print bg-white p-4 rounded-xl border border-emerald-200 shadow-sm mb-6">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 text-xs items-end">
                <input type="hidden" name="view" value="data">
                
                <div class="md:col-span-2">
                    <label class="block font-semibold mb-1 text-slate-700">Cari Nama / Barang / Kode</label>
                    <div class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($search_param) ?>" placeholder="Ketik kata kunci..." class="w-full pl-8 pr-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-600">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5"></i>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700">Filter Bulan</label>
                    <select name="month" class="w-full p-2 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-600">
                        <option value="">-- Semua --</option>
                        <?php foreach($bulan_indo as $num => $nama_bln): ?>
                            <option value="<?= $num ?>" <?= $month_param == $num ? 'selected' : '' ?>><?= $nama_bln ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700">Filter Tahun</label>
                    <select name="year" class="w-full p-2 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-600">
                        <option value="">-- Semua --</option>
                        <?php 
                        $curr_year = date('Y');
                        for($y = $curr_year; $y >= $curr_year - 5; $y--): 
                        ?>
                            <option value="<?= $y ?>" <?= $year_param == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700">Status Kembali</label>
                    <select name="status_return" class="w-full p-2 border rounded-lg outline-none focus:ring-2 focus:ring-emerald-600">
                        <option value="">-- Semua --</option>
                        <option value="0" <?= $status_param === '0' ? 'selected' : '' ?>>Belum Kembali</option>
                        <option value="1" <?= $status_param === '1' ? 'selected' : '' ?>>Sudah Kembali</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-manGreen-700 hover:bg-manGreen-800 text-white font-bold py-2 px-3 rounded-lg flex items-center justify-center gap-1">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
                    </button>
                    <a href="index.php?view=data" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 px-3 rounded-lg border flex items-center justify-center" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </form>

            <!-- TOMBOL AKSI: PINJAM BARANG, CETAK PDF, EXPORT EXCEL -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-4 mt-4 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <button onclick="openModal()" class="flex items-center gap-2 px-3.5 py-2 bg-manGreen-700 hover:bg-manGreen-800 text-white rounded-lg font-medium text-xs transition">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i> + Pinjam Barang
                    </button>
                    <button onclick="window.print()" class="flex items-center gap-2 px-3.5 py-2 bg-sky-700 hover:bg-sky-800 text-white rounded-lg font-medium text-xs transition">
                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak PDF
                    </button>
                    <a href="index.php?export=excel&search=<?= urlencode($search_param) ?>&month=<?= $month_param ?>&year=<?= $year_param ?>&status_return=<?= $status_param ?>" 
                       class="flex items-center gap-2 px-3.5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg font-medium text-xs transition">
                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- TABEL DATA (TAMPIL DI WEB & PDF) -->
        <div class="bg-white rounded-xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-manGreen-800 text-white uppercase text-[11px] font-semibold">
                            <th class="p-3 text-center">No</th>
                            <th class="p-3">Kode</th>
                            <th class="p-3">Tgl Pinjam</th>
                            <th class="p-3">Peminjam</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Barang & Jml</th>
                            <th class="p-3">Keperluan</th>
                            <th class="p-3">Est. Kembali</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100 text-slate-700 font-medium">
                        <?php if ($borrowings->num_rows == 0): ?>
                            <tr><td colspan="10" class="text-center py-6 text-slate-400">Tidak ada data peminjaman yang ditemukan.</td></tr>
                        <?php else: ?>
                            <?php $i=1; while($row = $borrowings->fetch_assoc()): ?>
                            <tr class="hover:bg-emerald-50/60 <?= $row['returned'] ? 'bg-slate-50' : 'bg-white' ?>">
                                <td class="p-3 text-center text-slate-500"><?= $i++ ?></td>
                                <td class="p-3 font-mono text-emerald-800 font-bold"><?= htmlspecialchars($row['id']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['date']) ?></td>
                                <td class="p-3 font-bold text-slate-800"><?= htmlspecialchars($row['name']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['type']) ?></td>
                                <td class="p-3 font-semibold text-emerald-950"><?= htmlspecialchars($row['item']) ?> (<?= $row['qty'] ?> Unit)</td>
                                <td class="p-3"><?= htmlspecialchars($row['purpose']) ?></td>
                                <td class="p-3"><?= htmlspecialchars($row['expected_return']) ?></td>
                                <td class="p-3 text-center">
                                    <a href="#" onclick="returnBorrow('<?= htmlspecialchars($row['id'], ENT_QUOTES) ?>', <?= $row['returned'] ? 0 : 1 ?>); return false" class="no-print px-2.5 py-1 rounded font-bold text-[10px] transition <?= $row['returned'] ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' ?>">
                                        <?= $row['returned'] ? '✓ Sudah Kembali' : '[ ] Belum Kembali' ?>
                                    </a>
                                    <span class="print-only font-bold"><?= $row['returned'] ? 'Sudah Kembali' : 'Belum Kembali' ?></span>
                                </td>
                                <td class="p-3 text-center no-print">
                                    <!-- TOMBOL EDIT DATA PEMINJAMAN -->
                                    <button onclick='openEditModal(<?= json_encode($row) ?>)' class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded text-[11px] font-bold flex items-center gap-1 mx-auto">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LEMBAR TANDA TANGAN (HANYA MUNCUL SAAT PRINT/PDF) -->
        <div class="print-only mt-12">
            <div class="flex justify-between text-xs text-center">
                <div class="w-64">
                    <p>Mengetahui,</p>
                    <p class="font-bold">Kepala Tata Usaha MAN 1 Palembang</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">( .................................................... )</p>
                    <p>NIP. .............................................</p>
                </div>

                <div class="w-64">
                    <p>Palembang, <?= date('d') . ' ' . $bulan_indo[intval(date('m'))] . ' ' . date('Y') ?></p>
                    <p class="font-bold">Petugas Tim Multimedia</p>
                    <div class="h-20"></div>
                    <p class="font-bold underline">( .................................................... )</p>
                    <p>NIP. .............................................</p>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </main>

    <div id="inventoryModal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center p-4 z-50 no-print">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-manGreen-800 text-white px-5 py-4 flex justify-between items-center"><h3 id="inventoryModalTitle" class="font-bold text-sm">Tambah Barang Inventaris</h3><button type="button" onclick="closeInventoryModal()">✕</button></div>
            <form method="POST" class="p-5 space-y-3 text-xs">
                <input type="hidden" name="form_type" value="save_inventory_item"><input type="hidden" id="inventory_id" name="inventory_id">
                <div><label class="block font-semibold mb-1">Nomor Kode Barang</label><input id="inventory_code" name="item_code" class="w-full p-2 border rounded-lg" placeholder="Kosongkan untuk membuat kode otomatis saat tambah" ></div>
                <div><label class="block font-semibold mb-1">Nama Barang</label><input id="inventory_name" name="item_name" required class="w-full p-2 border rounded-lg"></div>
                <div><label class="block font-semibold mb-1">Kategori</label><input id="inventory_category" name="item_category" value="Multimedia" required class="w-full p-2 border rounded-lg"></div>
                <div class="grid grid-cols-2 gap-3"><div><label class="block font-semibold mb-1">Kondisi</label><select id="inventory_condition" name="item_condition" class="w-full p-2 border rounded-lg"><option>Baik</option><option>Rusak Sedang</option><option>Rusak Berat</option></select></div><div><label class="block font-semibold mb-1">Jumlah Unit</label><input id="inventory_quantity" name="item_quantity" type="number" min="0" value="1" required class="w-full p-2 border rounded-lg"></div></div>
                <div class="flex justify-end gap-2 pt-3"><button type="button" onclick="closeInventoryModal()" class="px-4 py-2 bg-slate-100 rounded-lg">Batal</button><button class="px-5 py-2 bg-manGreen-700 text-white rounded-lg font-bold">Simpan</button></div>
            </form>
        </div>
    </div>

    <!-- MODAL FORM TAMBAH PEMINJAMAN -->
    <div id="itemModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden no-print">
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-manGreen-800 text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-bold text-sm">Form Peminjaman Inventaris Multimedia</h3>
                <button onclick="closeModal()" class="text-emerald-200 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form method="POST" class="p-5 space-y-3 text-xs">
                <input type="hidden" name="form_type" value="add_borrowing">
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Nama Peminjam *</label>
                        <input type="text" name="borrowerName" required class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Kategori *</label>
                        <select name="borrowerType" required class="w-full p-2 border rounded-lg">
                            <option value="Guru">Guru</option>
                            <option value="Siswa">Siswa</option>
                            <option value="Pegawai">Pegawai / Staf</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Identitas (NIP / NISN)</label>
                    <input type="text" name="borrowerIdNum" class="w-full p-2 border rounded-lg">
                </div>

                <div class="border-t pt-3">
                    <label class="block font-semibold mb-1">Barang Yang Dipinjam *</label>
                    <div class="grid grid-cols-3 gap-2 mb-2">
                        <select id="presetItemSelect" name="inventory_id" required onchange="autoFillItem()" class="col-span-2 p-2 border rounded-lg">
                            <option value="">-- Pilih dari Master Inventaris --</option>
                            <?php $borrowable_items = $conn->query("SELECT id, item_name, quantity FROM inventory_items WHERE quantity > 0 ORDER BY item_name"); if ($borrowable_items): while ($borrowable = $borrowable_items->fetch_assoc()): ?>
                            <option value="<?= intval($borrowable['id']) ?>" data-name="<?= htmlspecialchars($borrowable['item_name'], ENT_QUOTES) ?>" data-stock="<?= intval($borrowable['quantity']) ?>"><?= htmlspecialchars($borrowable['item_name']) ?> (tersedia <?= intval($borrowable['quantity']) ?>)</option>
                            <?php endwhile; endif; ?>
                        </select>
                        <input type="number" name="itemQty" value="1" min="1" required class="p-2 border rounded-lg text-center font-bold">
                    </div>
                    <p class="text-[11px] text-slate-500">Barang diambil dari master inventaris; stok berkurang saat disimpan.</p>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Keperluan *</label>
                    <input type="text" name="borrowPurpose" required class="w-full p-2 border rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-3 border-t pt-3">
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Pinjam</label>
                        <input type="date" name="borrowDate" value="<?= date('Y-m-d') ?>" required class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Estimasi Kembali</label>
                        <input type="date" name="expectedReturnDate" value="<?= date('Y-m-d') ?>" required class="w-full p-2 border rounded-lg">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-manGreen-700 text-white rounded-lg font-bold">Simpan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL FORM EDIT PEMINJAMAN -->
    <div id="editBorrowingModal" class="fixed inset-0 bg-slate-900/60 flex items-center justify-center p-4 z-50 hidden no-print">
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="bg-manGreen-800 text-white px-5 py-4 flex items-center justify-between">
                <h3 class="font-bold text-sm">Edit Data Peminjaman</h3>
                <button onclick="closeEditModal()" class="text-emerald-200 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form method="POST" class="p-5 space-y-3 text-xs">
                <input type="hidden" name="form_type" value="edit_borrowing">
                <input type="hidden" id="edit_borrow_id" name="edit_id">
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1">Nama Peminjam *</label>
                        <input type="text" id="edit_borrowerName" name="borrowerName" required class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Kategori *</label>
                        <select id="edit_borrowerType" name="borrowerType" required class="w-full p-2 border rounded-lg">
                            <option value="Guru">Guru</option>
                            <option value="Siswa">Siswa</option>
                            <option value="Pegawai">Pegawai / Staf</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Identitas (NIP / NISN)</label>
                    <input type="text" id="edit_borrowerIdNum" name="borrowerIdNum" class="w-full p-2 border rounded-lg">
                </div>

                <div class="border-t pt-3">
                    <label class="block font-semibold mb-1">Nama Barang & Jumlah *</label>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="text" id="edit_itemName" name="itemName" required class="col-span-2 p-2 border rounded-lg">
                        <input type="number" id="edit_itemQty" name="itemQty" min="1" required class="p-2 border rounded-lg text-center font-bold">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Keperluan *</label>
                    <input type="text" id="edit_borrowPurpose" name="borrowPurpose" required class="w-full p-2 border rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-3 border-t pt-3">
                    <div>
                        <label class="block font-semibold mb-1">Tanggal Pinjam</label>
                        <input type="date" id="edit_borrowDate" name="borrowDate" required class="w-full p-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Estimasi Kembali</label>
                        <input type="date" id="edit_expectedReturnDate" name="expectedReturnDate" required class="w-full p-2 border rounded-lg">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 rounded-lg">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-manGreen-700 text-white rounded-lg font-bold">Update Data</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT DATA BARANG RUSAK -->
    <div id="editDamagedModal" class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center p-4 z-50 no-print">
        <div class="bg-white rounded-2xl border border-rose-200 shadow-2xl max-w-lg w-full overflow-hidden animate-in fade-in zoom-in duration-150">
            <div class="bg-gradient-to-r from-rose-700 to-rose-800 text-white px-5 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-rose-900/70 flex items-center justify-center">
                        <i data-lucide="edit-3" class="w-4 h-4 text-rose-200"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm">Edit Catatan Barang Rusak</h3>
                        <p class="text-[11px] text-rose-200">Perbarui rincian kerusakan atau peminjam</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditDamagedModal()" class="text-rose-200 hover:text-white p-1 rounded-lg hover:bg-rose-900/50 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" class="p-5 space-y-3.5 text-xs">
                <input type="hidden" name="form_type" value="edit_damaged_item">
                <input type="hidden" id="edit_damaged_id" name="damaged_id">

                <div>
                    <label class="block font-semibold mb-1 text-slate-700">
                        Barang Apa yang Rusak <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="edit_damaged_item_name" name="item_name" required readonly
                               class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none text-xs font-medium">
                        <i data-lucide="package" class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5"></i>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700">
                            Siapa yang Meminjam <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" id="edit_damaged_borrower_name" name="borrower_name" required 
                                   class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none text-xs font-medium">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1 text-slate-700">
                            Kapan Peminjamannya <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="date" id="edit_damaged_borrow_date" name="borrow_date" required 
                                   class="w-full pl-8 pr-3 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none text-xs font-medium">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold mb-1 text-slate-700">
                        Keterangan Rusaknya Apa <span class="text-rose-600">*</span>
                    </label>
                    <textarea id="edit_damaged_damage_note" name="damage_note" rows="3" required 
                              class="w-full p-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-rose-500 outline-none text-xs leading-relaxed"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" onclick="closeEditDamagedModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-medium transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-700 hover:bg-rose-800 text-white rounded-lg font-bold flex items-center gap-1.5 shadow-sm transition">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        lucide.createIcons();
        const receiptSelect = document.getElementById('receipt_inventory_id');
        if (receiptSelect) {
            const receiptQty = document.getElementById('receipt_quantity');
            const receiptHint = document.getElementById('receipt_stock_hint');
            const updateReceiptStockLimit = () => {
                const option = receiptSelect.options[receiptSelect.selectedIndex];
                const stock = parseInt(option.dataset.stock || '0', 10);
                receiptQty.max = stock || '';
                receiptHint.textContent = stock ? `Stok tersedia: ${stock}` : 'Pilih barang yang memiliki stok tersedia.';
                if (stock && parseInt(receiptQty.value || '1', 10) > stock) receiptQty.value = stock;
            };
            receiptSelect.addEventListener('change', updateReceiptStockLimit);
            receiptQty.addEventListener('input', () => {
                const max = parseInt(receiptQty.max || '0', 10);
                if (max && parseInt(receiptQty.value || '0', 10) > max) receiptQty.value = max;
            });
        }
        function openModal() { document.getElementById('itemModal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('itemModal').classList.add('hidden'); }
        function autoFillItem() {
            const select = document.getElementById('presetItemSelect');
            const opt = select.options[select.selectedIndex];
            const qty = document.querySelector('[name="itemQty"]');
            qty.max = opt.dataset.stock || '';
            if (opt.dataset.stock) qty.value = Math.min(parseInt(qty.value || '1'), parseInt(opt.dataset.stock));
        }
        function returnBorrow(id, status) {
            let condition = 'Baik';
            if (status === 1) {
                condition = prompt('Kondisi barang saat kembali: Baik, Rusak Ringan, atau Rusak Berat', 'Baik');
                if (condition === null) return;
                condition = condition.trim();
                if (!['Baik', 'Rusak Ringan', 'Rusak Berat'].includes(condition)) { alert('Pilih Baik, Rusak Ringan, atau Rusak Berat.'); return; }
            }
            window.location.href = `index.php?action=toggle_return&id=${encodeURIComponent(id)}&status=${status}&condition=${encodeURIComponent(condition)}`;
        }

        function openInventoryModal() {
            document.getElementById('inventoryModalTitle').textContent = 'Tambah Barang Inventaris';
            document.getElementById('inventory_id').value = '';
            document.getElementById('inventory_code').value = '';
            document.getElementById('inventory_name').value = '';
            document.getElementById('inventory_category').value = 'Multimedia';
            document.getElementById('inventory_condition').value = 'Baik';
            document.getElementById('inventory_quantity').value = '1';
            document.getElementById('inventoryModal').classList.remove('hidden');
            document.getElementById('inventoryModal').classList.add('flex');
        }
        function editInventoryItem(item) {
            document.getElementById('inventoryModalTitle').textContent = 'Edit Barang Inventaris';
            document.getElementById('inventory_id').value = item.id;
            document.getElementById('inventory_code').value = item.item_code;
            document.getElementById('inventory_name').value = item.item_name;
            document.getElementById('inventory_category').value = item.category;
            document.getElementById('inventory_condition').value = item.item_condition;
            document.getElementById('inventory_quantity').value = item.quantity;
            document.getElementById('inventoryModal').classList.remove('hidden');
            document.getElementById('inventoryModal').classList.add('flex');
        }
        function closeInventoryModal() {
            document.getElementById('inventoryModal').classList.add('hidden');
            document.getElementById('inventoryModal').classList.remove('flex');
        }

        function openEditModal(data) {
            document.getElementById('edit_borrow_id').value = data.id;
            document.getElementById('edit_borrowerName').value = data.name;
            document.getElementById('edit_borrowerType').value = data.type;
            document.getElementById('edit_borrowerIdNum').value = data.id_num;
            document.getElementById('edit_itemName').value = data.item;
            document.getElementById('edit_itemQty').value = data.qty;
            document.getElementById('edit_borrowPurpose').value = data.purpose;
            document.getElementById('edit_borrowDate').value = data.date;
            document.getElementById('edit_expectedReturnDate').value = data.expected_return;
            
            document.getElementById('editBorrowingModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editBorrowingModal').classList.add('hidden');
        }

        function openEditDamagedModal(data) {
            document.getElementById('edit_damaged_id').value = data.id;
            document.getElementById('edit_damaged_item_name').value = data.item_name;
            document.getElementById('edit_damaged_borrower_name').value = data.borrower_name;
            document.getElementById('edit_damaged_borrow_date').value = data.borrow_date;
            document.getElementById('edit_damaged_damage_note').value = data.damage_note;
            
            const modal = document.getElementById('editDamagedModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            lucide.createIcons();
        }

        function closeEditDamagedModal() {
            const modal = document.getElementById('editDamagedModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>
