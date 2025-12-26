<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST["muzik_id"])) {
    die("Geçersiz istek");
}

$muzik_id = (int)$_POST["muzik_id"];

try {
    
    $stmt = $pdo->prepare("SELECT muzik_dosya FROM Muzikler WHERE muzik_id = ?");
    $stmt->execute([$muzik_id]);
    $muzik = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$muzik) {
        throw new Exception("Müzik bulunamadı.");
    }


    $pdo->prepare("DELETE FROM Begeni WHERE muzik_id = ?")
        ->execute([$muzik_id]);

  
    $pdo->prepare("DELETE FROM Muzikler WHERE muzik_id = ?")
        ->execute([$muzik_id]);

    
    $dosya_yolu = "uploads/" . $muzik["muzik_dosya"];
    if (file_exists($dosya_yolu)) {
        unlink($dosya_yolu);
    }

} catch (Exception $e) {
    die("Silme hatası: " . $e->getMessage());
}

header("Location: muzikler.php");
exit;
