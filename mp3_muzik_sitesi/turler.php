<?php
include "config.php";
include "layout/header.php";

$hata = "";
$basari = "";

/* ===== TÜR EKLE ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tur_adi"])) {
    try {
        $tur_adi = trim($_POST["tur_adi"]);

        if ($tur_adi === "") {
            throw new Exception("Tür adı boş olamaz.");
        }

        $kontrol = $pdo->prepare("SELECT tur_id FROM Turler WHERE tur_adi = ?");
        $kontrol->execute([$tur_adi]);

        if ($kontrol->rowCount() > 0) {
            throw new Exception("Bu tür zaten mevcut.");
        }

        $stmt = $pdo->prepare("INSERT INTO Turler (tur_adi) VALUES (?)");
        $stmt->execute([$tur_adi]);

        $basari = "🏷️ Tür başarıyla eklendi.";

    } catch (Exception $e) {
        $hata = "❌ " . $e->getMessage();
    }
}

/* ===== TÜR SİL ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["tur_sil"])) {
    try {
        $tur_id = (int)$_POST["tur_id"];

        // Bu türe ait müzik var mı?
        $kontrol = $pdo->prepare("SELECT COUNT(*) FROM Muzikler WHERE tur_id = ?");
        $kontrol->execute([$tur_id]);
        $muzik_sayisi = $kontrol->fetchColumn();

        if ($muzik_sayisi > 0) {
            throw new Exception("Bu türe ait müzikler var. Önce müzikleri sil.");
        }

        $sil = $pdo->prepare("DELETE FROM Turler WHERE tur_id = ?");
        $sil->execute([$tur_id]);

        $basari = "🗑️ Tür silindi.";

    } catch (Exception $e) {
        $hata = "❌ " . $e->getMessage();
    }
}
?>

<div class="welcome">
    <h2>🏷️ Türler</h2>
    <p>Müzik türlerini buradan yönetebilirsin</p>
</div>

<div class="cards">

    <!-- TÜR EKLE -->
    <div class="card">
        <span>➕</span>
        <h3>Tür Ekle</h3>

        <?php if ($hata): ?>
            <p style="color:#f87171"><?= $hata ?></p>
        <?php endif; ?>

        <?php if ($basari): ?>
            <p style="color:#4ade80"><?= $basari ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="tur_adi" placeholder="Tür Adı (Pop, Rock...)" required><br><br>
            <button type="submit" class="btn-primary">Tür Ekle</button>
        </form>
    </div>

    <!-- TÜRLER LİSTESİ -->
    <?php
    $q = $pdo->query("
        SELECT 
            t.tur_id,
            t.tur_adi,
            COUNT(m.muzik_id) AS muzik_sayisi
        FROM Turler t
        LEFT JOIN Muzikler m ON t.tur_id = m.tur_id
        GROUP BY t.tur_id
        ORDER BY t.tur_adi
    ");

    while ($row = $q->fetch(PDO::FETCH_ASSOC)):
    ?>

        <div class="card">
            <span>🎶</span>
            <h3><?= htmlspecialchars($row["tur_adi"]) ?></h3>
            <p><?= $row["muzik_sayisi"] ?> müzik</p>

            <!-- SİL BUTONU -->
            <form method="POST" style="margin-top:10px">
                <input type="hidden" name="tur_id" value="<?= $row["tur_id"] ?>">
                <button type="submit" name="tur_sil"
                        class="btn-secondary"
                        onclick="return confirm('Bu tür silinsin mi?')">
                    🗑️ Sil
                </button>
            </form>
        </div>

    <?php endwhile; ?>

</div>

<?php include "layout/footer.php"; ?>
