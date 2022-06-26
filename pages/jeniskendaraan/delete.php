<?php

include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */

if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

/**
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$id_jenis = $res['id_jenis']??'';

/**
 * Validation int value
 */
$idFilter = filter_var($id_jenis,FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($idFilter === false){
    $reply['error'] = "ID harus format INT";
    $isValidated = false;
}
if(empty($id_jenis)){
    $reply['error'] = 'ID harus diisi';
    $isValidated = false;
}
/**
 * jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * ceka apakah id jenis tersedia
 */

try{
    $queryCheck = "DELETE FROM jeniskendaraan where id_jenis = :id_jenis";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_jenis', $id_jenis);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID '.$id_jenis;
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
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM jeniskendaraan where id_jenis = :id_jenis";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_jenis', $id_jenis);
    if(!$statement->execute()){
        $reply['error'] = $statement->errorInfo();
        echo json_encode($reply);
        http_response_code(400);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
header('Content-Type: application/json');
$reply['status'] = true;
echo json_encode($reply);