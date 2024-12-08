<?php
include "koneksi.php";
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($con, "SELECT * FROM foto WHERE FotoID = '$id'");
    $data = mysqli_fetch_assoc($query);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['JudulFoto'];
    $deskripsi = $_POST['DeskripsiFoto'];
    $tanggal = $_POST['TanggalUnggah'];
    $album = $_POST['album'];

    // Jika foto baru diupload, simpan file dan ganti lokasi
    if (!empty($_FILES['LokasiFoto']['name'])) {
        $lokasiFoto = 'uploads/' . basename($_FILES['LokasiFoto']['name']);
        move_uploaded_file($_FILES['LokasiFoto']['tmp_name'], $lokasiFoto);
    } else {
        $lokasiFoto = $data['LokasiFoto'];
    }

    $updateQuery = "
        UPDATE foto SET
        JudulFoto = '$judul',
        DeskripsiFoto = '$deskripsi',
        TanggalUnggah = '$tanggal',
        LokasiFoto = '$lokasiFoto',
        AlbumID = '$album'
        WHERE FotoID = '$id'
    ";
    if (mysqli_query($con, $updateQuery)) {
        header("Location: dashboard.php");
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

<form action="" method="post" enctype="multipart/form-data">
    <label>Judul Foto:</label>
    <input type="text" name="JudulFoto" value="<?php echo $data['JudulFoto']; ?>"><br>

    <label>Deskripsi Foto:</label>
    <textarea name="DeskripsiFoto"><?php echo $data['DeskripsiFoto']; ?></textarea><br>

    <label>Tanggal Unggah:</label>
    <input type="date" name="TanggalUnggah" value="<?php echo $data['TanggalUnggah']; ?>"><br>

    <label>Upload Foto:</label>
    <input type="file" name="LokasiFoto"><br>
    <img src="<?php echo $data['LokasiFoto']; ?>" width="100"><br>

    <label>Album:</label>
    <select name="album">
        <?php
        $albumQuery = mysqli_query($con, "SELECT * FROM album");
        while ($album = mysqli_fetch_assoc($albumQuery)) {
            $selected = ($album['AlbumID'] == $data['AlbumID']) ? 'selected' : '';
            echo "<option value='{$album['AlbumID']}' $selected>{$album['NamaAlbum']}</option>";
        }
        ?>
    </select><br>

    <button type="submit">Update</button>
</form>z