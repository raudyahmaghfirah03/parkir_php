<?php
include "../../config/koneksi.php";

/**
 * @var $connection PDO
 */
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

$dataFinal = [];
$username = $_GET ['username'] ?? '';

if(empty($username)){
    $reply['error'] = 'username tidak ditemukan';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM users WHERE username = :username";
    $statement = $connection->prepare($queryCheck);
    $statement -> bindValue(':username', $username);
    $statement->execute();
    $dataUser = $statement-> fetch(PDO::FETCH_ASSOC);

    /**
     * Ambil data kategori berdasarkan kolom user
     */
    if($dataUser) {
        $stmUser = $connection->prepare("select * from kategori where id_user = :id_user");
        $stmUser->bindValue(':id_user', $dataUser['jeniskendaraan']);
        $stmUser->execute();
        $resultUser = $stmUser->fetch(PDO::FETCH_ASSOC);
        /*
         * Default kategori 'Tidak diketahui'
         */
        $jeniskendaraan = [
            'id_user' => $dataUser['jeniskendaraan'],
            'nama_user' => 'Tidak diketahui'
        ];
        if ($resultUser) {
            $jeniskendaraan = [
                'id_user' => $resultUser['id_user'],
                'nama' => $resultUser['nama']
            ];
        }

    }
    /**
     * Transform hasil query dari table user
     */
    $dataFinal = [
        'nama_user' => $dataUser['nama_user'],
        'username' => $dataUser['username'],
        'password' => $dataUser['password'],
        'no_telp' => $dataUser['no_telp'],
        'email' => $dataUser['email'],
        'jenis_kelamin' => $dataUser['jenis_kelamin'],
    ];
} catch (Exception $exception) {
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * show response
 */

if(!$dataFinal) {
    $reply['error'] = 'Data username tidak ditemukan' .$id_user;
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * otherwise show data
 */
header('Content-Type: application/json');
$reply['status'] = true;
$reply['data'] = $dataFinal;
echo json_encode($reply);