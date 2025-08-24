<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "soft";
$password = "mysql";
$dbname = "mimarlıkkk";
$usertable = "basvurdb";

// Hata ayıklama modunu açalım
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanına bağlan
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if (!$conn) {
    die(json_encode(array(
        'status' => 'error',
        'message' => 'Veritabanı bağlantı hatası: ' . mysqli_connect_error()
    )));
}

// JSON formatında gelen POST verisini al
$data = json_decode(file_get_contents("php://input"));

// POST verilerinin doğruluğunu kontrol et
if (!$data || !isset($data->fullname) || !isset($data->email) || !isset($data->phone)) {
    die(json_encode(array(
        'status' => 'error',
        'message' => 'Geçersiz veri! Lütfen tüm alanları doldurun.'
    )));
}

// Veritabanına ekleme sorgusu
$query = "INSERT INTO $usertable (ad_soyad, mail, telefon) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die(json_encode(array(
        'status' => 'error',
        'message' => 'Sorgu hazırlama hatası: ' . mysqli_error($conn)
    )));
}

// Parametreleri bağla ve sorguyu çalıştır
mysqli_stmt_bind_param($stmt, "sss", $data->fullname, $data->email, $data->phone);

if (mysqli_stmt_execute($stmt)) {
    // Başarılı mesaj
    echo json_encode(array(
        'status' => 'success',
        'message' => 'Başvurunuz başarıyla kaydedildi.'
    ));
} else {
    // Hata mesajı
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Başvuru sırasında bir hata oluştu: ' . mysqli_error($conn)
    ));
}

// Bağlantıyı kapat
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>