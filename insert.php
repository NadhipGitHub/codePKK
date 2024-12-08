<?php
include "koneksi.php";

$Username = $_POST['Username']; 
$Password = $_POST['Password'];
$Email = $_POST['Email'];
$NamaLengkap = $_POST['NamaLengkap'];
$Alamat = $_POST['Alamat'];

$HashedPassword = md5($Password);

$query = mysqli_query($con, "INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat)
    VALUES ('$Username', '$HashedPassword', '$Email', '$NamaLengkap', '$Alamat')");

header("Location: login.php");
exit();
?>