<?php
require "config.php";

$kullanici_id = 1; // şimdilik sabit kullanıcı
$muzik_id = isset($_GET["muzik_id"]) ? (int)$_GET["muzik_id"] : 0;

if ($muzik_id > 0) {

    // Daha önce beğenilmiş mi?
    $kontrol = $pdo->prepare(
        "SELECT 1 FROM Begeni WHERE kullanici_id = ? AND muzik_id = ?"
    );
    $kontrol->execute([$kullanici_id, $muzik_id]);

    if ($kontrol->fetch()) {
        // VARSA → GERİ AL (DELETE)
        $sil = $pdo->prepare(
            "DELETE FROM Begeni WHERE kullanici_id = ? AND muzik_id = ?"
        );
        $sil->execute([$kullanici_id, $muzik_id]);
    } else {
        // YOKSA → BEĞEN (INSERT)
        $ekle = $pdo->prepare(
            "INSERT INTO Begeni (kullanici_id, muzik_id) VALUES (?, ?)"
        );
        $ekle->execute([$kullanici_id, $muzik_id]);
    }
}

header("Location: muzikler.php");
exit;
