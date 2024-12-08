<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body >
      <form action="insert.php" method="post" enctype="multipart/form-data">
        <label for="">Nama Lengkap </label>
        <input type="text" name="nama"> <br>
        <label for="">username </label>
        <input type="text" name="Username"> <br>
        <label for="">password </label>
        <input type="password" name="password" id=""> <br>
        <label for="">Email </label>
        <input type="email" name="email" placeholder="@gmail.com"> <br>
        <label for="">Alamat</label>
        <textarea name="alamat"></textarea>
        <br> <br>
        <button type="submit" value="submit">Submit</button>
</body>
</html>