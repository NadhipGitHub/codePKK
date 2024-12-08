<?php
session_start();
include 'koneksi.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['Username'])) {
    header("Location: login.php");
    exit;
}

echo "Selamat Datang " . ($_SESSION['Username']) . " | ";
echo "<a href='logout.php'>Logout</a>";

// Ambil UserID berdasarkan Username yang sedang login
$username = $_SESSION['Username'];
$stmt = $con->prepare("SELECT UserID FROM user WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$resultUserID = $stmt->get_result();
$rowUserID = $resultUserID->fetch_assoc();
$userID = $rowUserID['UserID'];

// Proses form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judulFoto = $_POST['JudulFoto'] ?? '';
    $deskripsi = $_POST['DeskripsiFoto'] ?? '';
    $tanggalUnggah = $_POST['TanggalUnggah'] ?? '';
    $albumID = $_POST['album'] ?? '';

    // Validasi input
    if (empty($judulFoto) || empty($deskripsi) || empty($tanggalUnggah) || empty($albumID)) {
        echo "Semua kolom harus diisi.";
    } else {
        $file = $_FILES['UploadFoto'] ?? null;

        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($fileExt, $allowedExts)) {
                $newFileName = uniqid('', true) . '.' . $fileExt;
                $uploadDir = 'uploads/';
                $uploadPath = $uploadDir . $newFileName;

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    $stmt = $con->prepare("
                        INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFoto, AlbumID, UserID) 
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param("ssssii", $judulFoto, $deskripsi, $tanggalUnggah, $newFileName, $albumID, $userID);

                    if ($stmt->execute()) {
                        echo " Foto berhasil diunggah dan data berhasil disimpan! <br>";
                    } else {
                        echo "Gagal menyimpan data. Error: " . $stmt->error;
                    }
                } else {
                    echo "Gagal mengunggah file.";
                }
            } else {
                echo "Ekstensi file tidak valid! Hanya diperbolehkan: jpg, jfif, jpeg, png, gif.";
            }
        } else {
            echo "Terjadi kesalahan saat mengunggah file.";
        }
    }
}
?>
<hr>
<form action="dashboard.php" method="post" enctype="multipart/form-data">
    <div>
        <label for="judul">Judul Foto:</label>
        <input type="text" id="judul" name="JudulFoto" required>
    </div>
    <div>
        <label for="deskripsi">Deskripsi Foto:</label>
        <textarea id="deskripsi" name="DeskripsiFoto" required></textarea>
    </div>
    <div>
        <label for="tanggal">Tanggal Unggah:</label>
        <input type="date" id="tanggal" name="TanggalUnggah" required>
    </div>
    <div>
        <label for="foto">Upload Foto:</label>
        <input type="file" id="foto" name="UploadFoto" accept=".jpg,.jpeg,.png,.gif" required>
    </div>
    <div>
        <label for="kategori">Album</label>
        <select name="album" class="custom-select" required>
            <option value="">Silahkan Pilih</option>
            <?php
            $stmt = $con->prepare("SELECT AlbumID, NamaAlbum FROM album");
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['AlbumID'] . "'>" . htmlspecialchars($row['NamaAlbum']) . "</option>";
            }
            ?>
        </select>
    </div>
    <div>
        <button type="submit">Submit</button>
    </div>
</form>

<hr>

<!-- Tabel Menampilkan Data Foto -->
<h3>Daftar Foto</h3>
<table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: center;">
    <thead>
        <tr>
            <th>FotoID</th>
            <th>Foto</th>
            <th>Judul Foto</th>
            <th>Deskripsi Foto</th>
            <th>Tanggal Unggah</th>
            <th>Album</th>
            <th>User</th>
            <th>Jumlah Komentar</th>
            <th>Jumlah Like</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Query untuk mengambil data dari tabel foto
        $stmt = $con->prepare("
            SELECT f.FotoID, f.LokasiFoto, f.JudulFoto, f.DeskripsiFoto, f.TanggalUnggah, 
                   a.NamaAlbum, u.Username,
                   COUNT(k.KomentarID) AS JumlahKomentar,
                   COUNT(l.LikeID) AS JumlahLike
            FROM foto AS f
            LEFT JOIN album AS a ON f.AlbumID = a.AlbumID
            LEFT JOIN user AS u ON f.UserID = u.UserID
            LEFT JOIN komentarfoto AS k ON f.FotoID = k.FotoID
            LEFT JOIN likefoto AS l ON f.FotoID = l.FotoID
            GROUP BY f.FotoID
        ");
        $stmt->execute();
        $resultFoto = $stmt->get_result();

        if ($resultFoto->num_rows > 0) {
            while ($rowFoto = $resultFoto->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $rowFoto['FotoID'] . "</td>";
                echo "<td><img src='uploads/" . ($rowFoto['LokasiFoto']) . "' alt='" . ($rowFoto['JudulFoto']) . "' width='100'></td>";
                echo "<td>" . ($rowFoto['JudulFoto']) . "</td>";
                echo "<td>" . ($rowFoto['DeskripsiFoto']) . "</td>";
                echo "<td>" . ($rowFoto['TanggalUnggah']) . "</td>";
                echo "<td>" . ($rowFoto['NamaAlbum']) . "</td>";
                echo "<td>" . ($rowFoto['Username']) . "</td>";
                echo "<td>" . $rowFoto['JumlahKomentar'] . "</td>";
                echo "<td>" . $rowFoto['JumlahLike'] . "</td>";
                echo "<td>
                        <a href='edit.php?id=" . $rowFoto['FotoID'] . "'>Edit</a> | 
                        <a href='dalate.php?id=" . $rowFoto['FotoID'] . "' onclick='return confirm(\"Yakin ingin menghapus foto ini?\")'>Delete</a>
                    </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='10'>Tidak ada data foto</td></tr>";
        }
        ?>
    </tbody>
</table>