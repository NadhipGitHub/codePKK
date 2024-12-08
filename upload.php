<?php
include "koneksi.php";
$judul = $_POST['Judul'];
$deskripsi = $_POST['deskripsi'];
$tanggal = $_POST['tanggal'];
$tanggal = $_POST['tanggal'];
$album = $_POST['album'];


$query = mysqli_query($con,"INSERT INTO foto
    (Judul,deskripsi,foto,album)
    VALUES ('$judul','$deskripsi','$tanggal','$album','$Ndiskon',NOW())");
header("Location: laporan.php");
?>