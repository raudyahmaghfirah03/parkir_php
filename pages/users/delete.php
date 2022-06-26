<?php
include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */

/**
 * Validate http method
 */

if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}
/**
 * get input data from raw data
 */

$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$username = $res['username'] ?? '';

/**
 * cek apakah username tersedia
 */

try{
    $queryCheck = "SELECT * FROM users WHERE username = :username";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data username tidak ditemukan '.$username;
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
 * hapus data
 *
 */
try{
    $queryCheck = "DELETE FROM users where username = :username";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':username', $username);
    $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * send output
 */
header('Content-Type: application/json');
$reply['status'] = true;
echo json_encode($reply);
