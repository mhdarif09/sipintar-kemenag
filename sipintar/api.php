<?php
session_start();
header('Content-Type: application/json');
error_reporting(0);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'sipintar_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal: ' . $conn->connect_error]);
    exit;
}

// 0. AUTO-CREATE TABEL & STRUKTUR
$conn->query("CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150) NOT NULL,
    kode_barang VARCHAR(50) NULL UNIQUE,
    jenis_barang VARCHAR(100) NOT NULL,
    kondisi ENUM('Baik', 'Rusak Sedang', 'Rusak Berat') NOT NULL DEFAULT 'Baik',
    stok INT NOT NULL DEFAULT 0,
    satuan VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

// Migrasi aman untuk instalasi yang sudah memiliki tabel barang.
foreach ([
    'kode_barang' => "ALTER TABLE barang ADD COLUMN kode_barang VARCHAR(50) NULL UNIQUE AFTER nama_barang",
    'kondisi' => "ALTER TABLE barang ADD COLUMN kondisi ENUM('Baik', 'Rusak Sedang', 'Rusak Berat') NOT NULL DEFAULT 'Baik' AFTER jenis_barang",
    'updated_at' => "ALTER TABLE barang ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at"
] as $column => $migration) {
    $check = $conn->query("SHOW COLUMNS FROM barang LIKE '$column'");
    if ($check && $check->num_rows === 0) $conn->query($migration);
}
// Barang lama diberi kode berdasarkan ID agar tiap barang memiliki nomor inventaris.
$conn->query("UPDATE barang SET kode_barang = CONCAT('BRG-', LPAD(id, 5, '0')) WHERE kode_barang IS NULL OR kode_barang = ''");

$conn->query("CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) UNIQUE NOT NULL,
    tipe ENUM('MASUK', 'KELUAR') NOT NULL DEFAULT 'KELUAR',
    tanggal_pengambilan DATE NOT NULL,
    nama_pengambil VARCHAR(100) NOT NULL,
    jabatan_unit VARCHAR(100) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Cek dan tambahkan kolom 'tipe' jika tabel transaksi sudah ada dari database lama
$checkCol = $conn->query("SHOW COLUMNS FROM transaksi LIKE 'tipe'");
if ($checkCol && $checkCol->num_rows == 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN tipe ENUM('MASUK', 'KELUAR') NOT NULL DEFAULT 'KELUAR' AFTER kode_transaksi");
}
$checkSignature = $conn->query("SHOW COLUMNS FROM transaksi LIKE 'tanda_tangan'");
if ($checkSignature && $checkSignature->num_rows === 0) {
    $conn->query("ALTER TABLE transaksi ADD COLUMN tanda_tangan MEDIUMTEXT NULL AFTER keterangan");
}

$conn->query("CREATE TABLE IF NOT EXISTS transaksi_detail (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(50) NOT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    jumlah INT NOT NULL,
    satuan VARCHAR(50) NOT NULL,
    FOREIGN KEY (kode_transaksi) REFERENCES transaksi(kode_transaksi) ON DELETE CASCADE
)");

$action = $_GET['action'] ?? '';

// 1. LOGIN
if ($action === 'login') {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $conn->real_escape_string($input['username'] ?? '');
    $password = $conn->real_escape_string($input['password'] ?? '');

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$row['id'];
        $_SESSION['user_role'] = strtolower($row['role'] ?? 'pegawai');
        echo json_encode([
            'success'  => true,
            'id'       => (string)$row['id'],
            'nama'     => $row['nama_lengkap'],
            'username' => $row['username'],
            'role'     => strtolower($row['role'] ?? 'pegawai')
        ]);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Username atau password salah!']);
    exit;
}

if ($action === 'logout') {
    $_SESSION = [];
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Kelola akun hanya dapat digunakan oleh akun yang sudah login.
if (in_array($action, ['get_accounts', 'create_account', 'edit_account', 'delete_account', 'change_own_password'], true)) {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Silakan login kembali.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
    $sessionRole = $_SESSION['user_role'] ?? '';
    if (!in_array($sessionRole, ['admin', 'pegawai', 'pemantau'], true)
        || ($sessionRole === 'pemantau' && $action !== 'change_own_password')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Akun ini tidak memiliki akses ke fitur tersebut.']);
        exit;
    }

    if ($action === 'get_accounts') {
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Hanya admin yang dapat melihat daftar akun.']);
            exit;
        }
        $res = $conn->query("SELECT id, username, password, nama_lengkap, role FROM users ORDER BY username ASC");
        $accounts = [];
        if ($res) while ($row = $res->fetch_assoc()) $accounts[] = $row;
        echo json_encode(['success' => true, 'data' => $accounts]);
        exit;
    }

    if ($action === 'create_account') {
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Hanya admin yang dapat menambahkan akun.']);
            exit;
        }
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        $nama = trim($input['nama_lengkap'] ?? '');
        $role = strtolower($input['role'] ?? 'pegawai');
        if ($username === '' || $password === '' || $nama === '' || !in_array($role, ['admin', 'pegawai', 'pemantau'], true)) {
            echo json_encode(['success' => false, 'message' => 'Lengkapi data akun dengan benar.']);
            exit;
        }
        $stmt = $conn->prepare('INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $username, $password, $nama, $role);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Akun baru berhasil ditambahkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->errno === 1062 ? 'Username sudah digunakan.' : 'Gagal menambahkan akun.']);
        }
        exit;
    }

    if ($action === 'edit_account') {
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Hanya admin yang dapat mengedit akun.']);
            exit;
        }
        $id = (int)($input['id'] ?? 0);
        $username = trim($input['username'] ?? '');
        $nama = trim($input['nama_lengkap'] ?? '');
        $password = $input['password'] ?? '';
        $role = strtolower($input['role'] ?? '');
        if ($id < 1 || $username === '' || $nama === '' || !in_array($role, ['admin', 'pegawai', 'pemantau'], true)) {
            echo json_encode(['success' => false, 'message' => 'Data akun tidak valid.']);
            exit;
        }
        $check = $conn->prepare('SELECT role FROM users WHERE id = ?');
        $check->bind_param('i', $id);
        $check->execute();
        $target = $check->get_result()->fetch_assoc();
        if (!$target) {
            echo json_encode(['success' => false, 'message' => 'Akun tidak ditemukan.']);
            exit;
        }
        if ($target['role'] === 'admin' && $role !== 'admin') {
            $count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'")->fetch_assoc();
            if ((int)$count['total'] <= 1 || $id === (int)$_SESSION['user_id']) {
                echo json_encode(['success' => false, 'message' => 'Akun admin terakhir atau akun admin yang sedang digunakan tidak dapat diubah menjadi pegawai.']);
                exit;
            }
        }
        if ($password !== '' && strlen($password) < 4) {
            echo json_encode(['success' => false, 'message' => 'Password baru minimal 4 karakter.']);
            exit;
        }
        if ($password === '') {
            $stmt = $conn->prepare('UPDATE users SET username = ?, nama_lengkap = ?, role = ? WHERE id = ?');
            $stmt->bind_param('sssi', $username, $nama, $role, $id);
        } else {
            $stmt = $conn->prepare('UPDATE users SET username = ?, nama_lengkap = ?, role = ?, password = ? WHERE id = ?');
            $stmt->bind_param('ssssi', $username, $nama, $role, $password, $id);
        }
        if ($stmt->execute()) {
            if ($id === (int)$_SESSION['user_id']) $_SESSION['user_role'] = $role;
            echo json_encode(['success' => true, 'message' => 'Akun berhasil diperbarui.']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->errno === 1062 ? 'Username sudah digunakan.' : 'Gagal memperbarui akun.']);
        }
        exit;
    }

    if ($action === 'delete_account') {
        if (!$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Hanya admin yang dapat menghapus akun.']);
            exit;
        }
        $id = (int)($input['id'] ?? 0);
        if ($id < 1 || $id === (int)$_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Akun yang sedang digunakan tidak dapat dihapus.']);
            exit;
        }
        $check = $conn->prepare('SELECT role FROM users WHERE id = ?');
        $check->bind_param('i', $id);
        $check->execute();
        $target = $check->get_result()->fetch_assoc();
        if (!$target) {
            echo json_encode(['success' => false, 'message' => 'Akun tidak ditemukan.']);
            exit;
        }
        if ($target['role'] === 'admin') {
            $count = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'")->fetch_assoc();
            if ((int)$count['total'] <= 1) {
                echo json_encode(['success' => false, 'message' => 'Admin terakhir tidak dapat dihapus.']);
                exit;
            }
        }
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $id);
        echo json_encode($stmt->execute()
            ? ['success' => true, 'message' => 'Akun berhasil dihapus.']
            : ['success' => false, 'message' => 'Gagal menghapus akun.']);
        exit;
    }

    if ($action === 'change_own_password') {
        $current = $input['current_password'] ?? '';
        $new = $input['new_password'] ?? '';
        if ($current === '' || strlen($new) < 4) {
            echo json_encode(['success' => false, 'message' => 'Masukkan password saat ini dan password baru minimal 4 karakter.']);
            exit;
        }
        $stmt = $conn->prepare('UPDATE users SET password = ? WHERE id = ? AND password = ?');
        $userId = (int)$_SESSION['user_id'];
        $stmt->bind_param('sis', $new, $userId, $current);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Password berhasil diubah.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Password saat ini tidak sesuai atau password baru sama dengan password lama.']);
        }
        exit;
    }
}

// 2. KELOLA MASTER BARANG
if ($action === 'get_barang') {
    $res = $conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");
    $data = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

if ($action === 'save_barang') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id    = (int)($input['id'] ?? 0);
    $nama  = $conn->real_escape_string($input['nama_barang'] ?? '');
    $jenis = $conn->real_escape_string($input['jenis_barang'] ?? '');
    $kondisi = $conn->real_escape_string($input['kondisi'] ?? 'Baik');
    $stok  = (int)($input['stok'] ?? 0);
    $satuan= $conn->real_escape_string($input['satuan'] ?? '');

    if (!in_array($kondisi, ['Baik', 'Rusak Sedang', 'Rusak Berat'], true)) $kondisi = 'Baik';
    if ($id > 0) {
        $sql = "UPDATE barang SET nama_barang='$nama', jenis_barang='$jenis', kondisi='$kondisi', stok=$stok, satuan='$satuan', updated_at=CURRENT_TIMESTAMP WHERE id=$id";
        $success = $conn->query($sql);
    } else {
        // Gunakan ID barang sebagai nomor 5 digit supaya kode selalu unik.
        $success = $conn->query("INSERT INTO barang (kode_barang, nama_barang, jenis_barang, kondisi, stok, satuan) VALUES (NULL, '$nama', '$jenis', '$kondisi', $stok, '$satuan')");
        if ($success) {
            $idBaru = (int)$conn->insert_id;
            $kode = 'BRG-' . str_pad((string)$idBaru, 5, '0', STR_PAD_LEFT);
            $success = $conn->query("UPDATE barang SET kode_barang='$kode' WHERE id=$idBaru");
        }
    }

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Data barang berhasil disimpan!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan barang: ' . $conn->error]);
    }
    exit;
}

if ($action === 'save_template_barang') {
    $input = json_decode(file_get_contents('php://input'), true);
    $items = $input['items'] ?? [];

    if (empty($items)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada data template yang dikirim!']);
        exit;
    }

    $conn->begin_transaction();
    try {
        foreach ($items as $item) {
            $nama  = $conn->real_escape_string($item['nama_barang'] ?? '');
            $jenis = $conn->real_escape_string($item['jenis_barang'] ?? '');
            $stok  = (int)($item['stok'] ?? 0);
            $satuan= $conn->real_escape_string($item['satuan'] ?? '');

            $check = $conn->query("SELECT id, stok FROM barang WHERE nama_barang = '$nama'");
            if ($check && $check->num_rows > 0) {
                $row = $check->fetch_assoc();
                $newStok = $row['stok'] + $stok;
                $conn->query("UPDATE barang SET stok = $newStok, jenis_barang = '$jenis', satuan = '$satuan' WHERE id = " . $row['id']);
            } else {
                $inserted = $conn->query("INSERT INTO barang (kode_barang, nama_barang, jenis_barang, stok, satuan) VALUES (NULL, '$nama', '$jenis', $stok, '$satuan')");
                if ($inserted) {
                    $idBaru = (int)$conn->insert_id;
                    $kode = 'BRG-' . str_pad((string)$idBaru, 5, '0', STR_PAD_LEFT);
                    $conn->query("UPDATE barang SET kode_barang='$kode' WHERE id=$idBaru");
                }
            }
        }
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Template barang berhasil ditambahkan ke Master Data!']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Gagal memproses template: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'hapus_barang') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = (int)($input['id'] ?? 0);
    if ($conn->query("DELETE FROM barang WHERE id = $id")) {
        echo json_encode(['success' => true, 'message' => 'Barang berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus barang: ' . $conn->error]);
    }
    exit;
}

// 3. SIMPAN TRANSAKSI (MASUK / KELUAR)
if ($action === 'simpan_transaksi') {
    $input = json_decode(file_get_contents('php://input'), true);

    $kode     = $conn->real_escape_string($input['kode_transaksi'] ?? '');
    $tipe     = strtoupper($conn->real_escape_string($input['tipe'] ?? 'KELUAR'));
    $tanggal  = $conn->real_escape_string($input['tanggal_pengambilan'] ?? '');
    $pengambil= $conn->real_escape_string($input['nama_pengambil'] ?? '');
    $jabatan  = $conn->real_escape_string($input['jabatan_unit'] ?? '');
    $ket      = $conn->real_escape_string($input['keterangan'] ?? '');
    $signature = $conn->real_escape_string($input['tanda_tangan'] ?? '');
    $items    = $input['items'] ?? [];

    if (empty($kode) || empty($tanggal) || empty($items)) {
        echo json_encode(['success' => false, 'message' => 'Data formulir tidak lengkap!']);
        exit;
    }

    $conn->begin_transaction();

    try {
        $sqlHead = "INSERT INTO transaksi (kode_transaksi, tipe, tanggal_pengambilan, nama_pengambil, jabatan_unit, keterangan, tanda_tangan) 
                    VALUES ('$kode', '$tipe', '$tanggal', '$pengambil', '$jabatan', '$ket', '$signature')";
        if (!$conn->query($sqlHead)) {
            throw new Exception($conn->error);
        }

        foreach ($items as $item) {
            $namaBrg = $conn->real_escape_string($item['nama_barang'] ?? '');
            $jml     = (int)($item['jumlah'] ?? 1);
            $satuan  = $conn->real_escape_string($item['satuan'] ?? '');

            $sqlDet = "INSERT INTO transaksi_detail (kode_transaksi, nama_barang, jumlah, satuan) 
                       VALUES ('$kode', '$namaBrg', $jml, '$satuan')";
            if (!$conn->query($sqlDet)) {
                throw new Exception($conn->error);
            }

            // Update stok berdasarkan tipe transaksi
            if ($tipe === 'MASUK') {
                $conn->query("UPDATE barang SET stok = stok + $jml WHERE nama_barang = '$namaBrg'");
            } else {
                $conn->query("UPDATE barang SET stok = stok - $jml WHERE nama_barang = '$namaBrg'");
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Transaksi ' . strtolower($tipe) . ' berhasil disimpan!']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage()]);
    }
    exit;
}

// 3.1 EDIT TRANSAKSI
if ($action === 'update_transaksi') {
    $input = json_decode(file_get_contents('php://input'), true);

    $kode     = $conn->real_escape_string($input['kode_transaksi'] ?? '');
    $tanggal  = $conn->real_escape_string($input['tanggal_pengambilan'] ?? '');
    $pengambil= $conn->real_escape_string($input['nama_pengambil'] ?? '');
    $jabatan  = $conn->real_escape_string($input['jabatan_unit'] ?? '');
    $ket      = $conn->real_escape_string($input['keterangan'] ?? '');
    $items    = $input['items'] ?? [];

    if (empty($kode) || empty($tanggal) || empty($items)) {
        echo json_encode(['success' => false, 'message' => 'Data formulir tidak lengkap!']);
        exit;
    }

    $conn->begin_transaction();

    try {
        // Ambil data transaksi lama untuk pengembalian/koreksi stok
        $resOld = $conn->query("SELECT tipe FROM transaksi WHERE kode_transaksi = '$kode'");
        if (!$resOld || $resOld->num_rows == 0) {
            throw new Exception("Transaksi tidak ditemukan!");
        }
        $oldHead = $resOld->fetch_assoc();
        $tipe = $oldHead['tipe'];

        // Kembalikan stok dari transaksi lama
        $resOldDetails = $conn->query("SELECT nama_barang, jumlah FROM transaksi_detail WHERE kode_transaksi = '$kode'");
        if ($resOldDetails) {
            while ($oldDet = $resOldDetails->fetch_assoc()) {
                $oNama = $conn->real_escape_string($oldDet['nama_barang']);
                $oJml  = (int)$oldDet['jumlah'];
                if ($tipe === 'MASUK') {
                    $conn->query("UPDATE barang SET stok = stok - $oJml WHERE nama_barang = '$oNama'");
                } else {
                    $conn->query("UPDATE barang SET stok = stok + $oJml WHERE nama_barang = '$oNama'");
                }
            }
        }

        // Update header transaksi
        $sqlHead = "UPDATE transaksi SET tanggal_pengambilan='$tanggal', nama_pengambil='$pengambil', jabatan_unit='$jabatan', keterangan='$ket' WHERE kode_transaksi='$kode'";
        if (!$conn->query($sqlHead)) {
            throw new Exception($conn->error);
        }

        // Hapus detail lama
        $conn->query("DELETE FROM transaksi_detail WHERE kode_transaksi='$kode'");

        // Insert detail baru dan update stok
        foreach ($items as $item) {
            $namaBrg = $conn->real_escape_string($item['nama_barang'] ?? '');
            $jml     = (int)($item['jumlah'] ?? 1);
            $satuan  = $conn->real_escape_string($item['satuan'] ?? '');

            $sqlDet = "INSERT INTO transaksi_detail (kode_transaksi, nama_barang, jumlah, satuan) 
                       VALUES ('$kode', '$namaBrg', $jml, '$satuan')";
            if (!$conn->query($sqlDet)) {
                throw new Exception($conn->error);
            }

            if ($tipe === 'MASUK') {
                $conn->query("UPDATE barang SET stok = stok + $jml WHERE nama_barang = '$namaBrg'");
            } else {
                $conn->query("UPDATE barang SET stok = stok - $jml WHERE nama_barang = '$namaBrg'");
            }
        }

        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Transaksi berhasil diperbarui!']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui transaksi: ' . $e->getMessage()]);
    }
    exit;
}

// 4. GET SEMUA TRANSAKSI
if ($action === 'get_transaksi') {
    $sqlHeader = "SELECT * FROM transaksi ORDER BY tanggal_pengambilan DESC, id DESC";
    $resHeader = $conn->query($sqlHeader);

    $data = [];
    if ($resHeader) {
        while ($head = $resHeader->fetch_assoc()) {
            $kode = $conn->real_escape_string($head['kode_transaksi']);
            
            $sqlDetail = "SELECT nama_barang, jumlah, satuan FROM transaksi_detail WHERE kode_transaksi = '$kode'";
            $resDetail = $conn->query($sqlDetail);
            
            $items = [];
            if ($resDetail && $resDetail->num_rows > 0) {
                while ($det = $resDetail->fetch_assoc()) {
                    $items[] = [
                        'nama_barang' => $det['nama_barang'],
                        'jumlah'      => $det['jumlah'],
                        'satuan'      => $det['satuan']
                    ];
                }
            }
            
            $head['items'] = $items;
            $data[] = $head;
        }
    }

    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}

// 5. HAPUS TRANSAKSI
if ($action === 'hapus_transaksi') {
    $input = json_decode(file_get_contents('php://input'), true);
    $kodes = $input['kodes'] ?? [];

    if (empty($kodes)) {
        echo json_encode(['success' => false, 'message' => 'Tidak ada data yang dipilih untuk dihapus.']);
        exit;
    }

    $escapedKodes = array_map(function($k) use ($conn) {
        return "'" . $conn->real_escape_string($k) . "'";
    }, $kodes);

    $strKodes = implode(',', $escapedKodes);
    $sql = "DELETE FROM transaksi WHERE kode_transaksi IN ($strKodes)";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Data transaksi terpilih berhasil dihapus!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus transaksi: ' . $conn->error]);
    }
    exit;
}
?>
