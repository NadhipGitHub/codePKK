<?php 
include "koneksi.php";
$username = $_POST ['Username'];
$password = md5($_POST ['Password']);
$query =  mysqli_query($con, "SELECT * FROM user WHERE Username='$username'
AND Password ='$password' ");
$hasilquery = mysqli_num_rows($query);
if ($hasilquery == 1){
    session_start();
    while($row= mysqli_fetch_assoc($query)){
        $_SESSION['Username']=$row['Username'];
        header("Location: dashboard.php");
    }
}else{
header("Location: login.php");
    }

?>