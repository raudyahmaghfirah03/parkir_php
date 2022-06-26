<?php

include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */

try{
    /**
     * prepare query jeniskendaraan
     */
    $statement = $connection ->prepare("SELECT *FROM jeniskendaraan");
    $isOk = $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $reply['data'] = $results;
} catch (Exception $exception) {
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);

    exit(0);
}

if(!$isOk) {
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
header('Content-Type: application/json');
$reply['status'] = true;
echo json_encode($reply);
