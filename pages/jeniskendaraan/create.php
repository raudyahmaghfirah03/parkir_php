<?php
include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */

/**
 * validate http mehtod
 */

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    $reply['error'] = 'Post method required';
    echo json_encode($reply);
    exit();
}
/**
 * get input data POST
 */
$jenis_kendaraan = $_POST['jenis_kendaraan'] ?? '';
$bayar = $_POST['bayar'] ?? 0;
$blok = $_POST['blok'] ?? '';

/**
 * validation int value
 */

$bayarFilter = filter_var($bayar,FILTER_VALIDATE_INT);
/**
 * validation empty false
 */
$isValidate = true;
if(empty($jenis_kendaraan)){
    $reply['error'] = 'Jenis Kendaraan harus diisi';
    $isValidate = false;
}
if(empty($bayar)){
    $reply['error'] = 'Jumlah bayar harus diisi';
    $isValidate = false;
}
if(empty($blok)){
    $reply['error'] = 'Blok harus diisi';
    $isValidate = false;
}
if(!$isValidate){
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
    $query = "INSERT INTO jeniskendaraan (jenis_kendaraan, bayar, blok) values (:jenis_kendaraan, :bayar, :blok)";
    $statement = $connection->prepare($query);
    /**
     * bind params
     */
    $statement->bindValue(":jenis_kendaraan", $jenis_kendaraan);
    $statement->bindValue(":bayar", $bayar, PDO::PARAM_INT);
    $statement->bindValue(":blok", $blok);
    /**
     * execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception) {
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * if not ok, add error info
 * http status code 400:bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */

if(!$isOk) {
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/**
 * get last data
 */
$lastId = $connection->lastInsertId();
$getResult = "SELECT *FROM jeniskendaraan WHERE id_jenis = :id_jenis";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_jenis', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/**
 * show output to client
 * set status info true
 */

header('Content-Type: application/json');
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);