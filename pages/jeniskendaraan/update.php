<?php
include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */

if($_SERVER['REQUEST_METHOD'] !== 'PATCH'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'PATCH method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$id_jenis = $formData['id_jenis'] ?? '';
$jenis_kendaraan = $formData['jenis_kendaraan'] ?? '';
$bayar = $formData['bayar'] ?? 0;
$blok = $formData['blok'] ?? '';

/**
 * Validation int value
 */
$id_jenisFilter = filter_var($id_jenis,FILTER_VALIDATE_INT);
$bayarFilter = filter_var($bayar, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($id_jenisFilter ==- false) {
    $reply['error'] = "id jenis harus di isi";
    $isValidated = false;
}
if($bayarFilter === false){
    $reply['error'] = "Jumlah bayar harus format INT";
    $isValidated = false;
}
if(empty($jenis_kendaraan)){
    $reply['error'] = 'Jenis Kendaraan harus diisi';
    $isValidate = false;
}
if(empty($blok)){
    $reply['error'] = 'Blok harus diisi';
    $isValidate = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode(400);
    exit(0);
}
/**
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM jeniskendaraan where id_jenis = :id_jenis";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_jenis', $id_jenisFilter);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan id' .$id_jenisFilter.' tidak ditemukan ';
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE jeniskendaraan SET jenis_kendaraan = :jenis_kendaraan,  bayar = :bayar,  blok = :blok WHERE id_jenis = :id_jenis";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_jenis", $id_jenis);
    $statement->bindValue(":jenis_kendaraan", $jenis_kendaraan);
    $statement->bindValue(":bayar", $bayar, PDO::PARAM_INT);
    $statement->bindValue(":blok", $blok);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/**
 * Show output to client
 */
header('Content-Type: application/json');
$reply['status'] = $isOk;
echo json_encode($reply);