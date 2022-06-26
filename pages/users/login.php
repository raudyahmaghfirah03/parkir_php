<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: *");
$dbserver = "localhost";
$dbname = "web_sepatu";
$dbuser = "root";
$dbpassword = "";
$dsn = "mysql:host = {$dbserver};dbname={$dbname}";

$connection = null;
try {
    $connection = new PDO($dsn, $dbuser, $dbpassword);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Koneksi Gagal: " . $e->getMessage());
}

if($_POST){
    //data
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $response = []; //Data Response

    //Cek Username dalam database
    $userQuery = $connection->prepare("SELECT * FROM users where username = ?");
    $userQuery->execute(array($username));
    $query = $userQuery->fetch();

    if($userQuery->rowCount() == 0){
        $response['status'] = false;
        $response['message'] = "Username tidak ditemukan";
    } else {
        // Ambil password di database

        $passwordDB = $query['password'];

        if(strcmp(md5($password), $passwordDB) === 0){
            $response['status'] = true;
            $response['message'] = "Login Berhasil";
            $response['data'] = [
                'id_user' => $query['id_user'],
                'username' => $query['username'],
                'nama_lengkap' => $query['nama_lengkap'],
                'nomor_hp' => $query['nomor_hp'],
                'alamat' => $query['alamat'],
                'email' => $query['email'],
                'jenis_kelamin' => $query['jenis_kelamin']
            ];
        } else {
            $response['status'] = false;
            $response['message'] = "Password salah";
        }
    }


    //jadikan data JSON
    $json = json_encode($response, JSON_PRETTY_PRINT);

    //Print JSON
    echo $json;
}
