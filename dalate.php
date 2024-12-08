<!-- detele.php -->
<?php
include 'koneksi.php'; // Pastikan koneksi.php sudah benar

// Pastikan parameter 'id' ada di URL
if (isset($_GET['id'])) {
    $fotoID = $_GET['id']; // Ambil ID foto yang akan dihapus

    // Query untuk mengambil nama file foto berdasarkan FotoID
    $sql = "SELECT LokasiFoto FROM foto WHERE FotoID = '$fotoID'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $fotoFile = $row['LokasiFoto'];  // Nama file foto yang disimpan di database

        // Menghapus file foto dari folder 'uploads'
        if (file_exists('uploads/' . $fotoFile)) {
            unlink('uploads/' . $fotoFile);  // Hapus file foto dari server
        }

        // Query untuk menghapus data foto dari tabel 'foto' berdasarkan FotoID
        $query = "DELETE FROM foto WHERE FotoID = '$fotoID'";

        if (mysqli_query($con, $query)) {
            echo "Foto berhasil dihapus!";
            header('Location: dashboard.php');  // Redirect ke halaman dashboard setelah penghapusan
            exit();
        } else {
            echo "Gagal menghapus data foto. Error: " . mysqli_error($con);
        }
    } else {
        echo "Foto tidak ditemukan!";
    }
} else {
    echo "ID foto tidak ditemukan!";
}
?>