<?php

include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */

/**
 * Validate http method
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST')  {
    http_response_code(400);
    $reply['error'] = 'POST method required';
    exit();
}

/**
 * Get input data POST
 */

$nama_user = $_POST['nama_user'] ?? '';
$username = $_POST['username'] ??'';
$password = $_POST['password'] ??'';
$no_telp = $_POST['no_telp'] ??'';
$email = $_POST['email'] ??'';
$jenis_kelamin = $_POST['jenis_kelamin'] ??'';

/**
 * validation empty fields
 */

$isValidate = true;
if(empty($nama_user)) {
    $reply['error'] = "Nama user harus diisi";
    $isValidate = false;
}
if(empty($username)) {
    $reply['error'] = "Username harus diisi";
    $isValidate = false;
}
if(empty($password)) {
    $reply['error'] = "Password harus diisi";
    $isValidate = false;
}
if(empty($no_telp)) {
    $reply['error'] = "No Telp harus diisi";
    $isValidate = false;
}
if(empty($email)) {
    $reply['error'] = "Email harus diisi";
    $isValidate = false;
}
if(empty($jenis_kelamin)) {
    $reply['error'] = "Jenis Kelamin harus diisi";
    $isValidate = false;
}
/**
 * jika filter gagal
 */

if (!$isValidate) {
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * method ok
 * validation ok
 * prepare query
 */

try{
    $query = "INSERT INTO users (nama_user, username, password, no_telp, email, jenis_kelamin) VALUES (:nama_user, :username, :password, :no_telp, :email, :jenis_kelamin)";
    $statement = $connection -> prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":nama_user", $nama_user);
    $statement->bindValue(":username", $username);
    $statement->bindValue(":password", $password);
    $statement->bindValue(":no_telp", $no_telp);
    $statement->bindValue(":email", $email);
    $statement->bindValue(":jenis_kelamin", $jenis_kelamin);
    /**
     * execute query
     */
    $isOk = $statement->execute();
} catch (Exception $exception) {
    $reply['error'] = $exception -> getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * if not ok, add error info
 * http status code 400: bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/**
 * get last data
 */
$lastId = $connection->lastInsertId();
$getResult = "SELECT * FROM users WHERE id_user = :id_user";
$stm = $connection ->prepare($getResult);
$stm->bindValue(':id_user', $lastId);
$stm->execute();
$result = $stm ->fetch(PDO::FETCH_ASSOC);

/**
 * Transform result
 */


/**
 * show output to client
 * set status info true
 */
header('Content-Type: application/json');
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);