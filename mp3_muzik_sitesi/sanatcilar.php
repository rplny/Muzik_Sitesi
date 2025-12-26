<?php
include "config.php";
include "layout/header.php";

$hata = "";
$basari = "";

/* ===== SANATÇI EKLE ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sanatci_adi"])) {
    try {
        $sanatci_adi = trim($_POST["sanatci_adi"]);

        if ($sanatci_adi === "") {
            throw new Exception("Sanatçı adı boş olamaz.");
        }

        // Aynı sanatçı var mı?
        $kontrol = $pdo->prepare("SELECT sanatci_id FROM Sanatcilar WHERE sanatci_adi = ?");
        $kontrol->execute([$sanatci_adi]);

        if ($kontrol->rowCount() > 0) {
            throw new Exception("Bu sanatçı zaten mevcut.");
        }

        // INSERT
        $stmt = $pdo->prepare("INSERT INTO Sanatcilar (sanatci_adi) VALUES (?)");
        $stmt->execute([$sanatci_adi]);

        $basari = "🎤 Sanatçı başarıyla eklendi.";

    } catch (Exception $e) {
        $hata = "❌ " . $e->getMessage();
    }
}

/* ===== SANATÇI SİL ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sanatci_sil"])) {
    try {
        $sanatci_id = (int)$_POST["sanatci_id"];

        // Bu sanatçıya ait müzik var mı?
        $kontrol = $pdo->prepare("SELECT COUNT(*) FROM Muzikler WHERE sanatci_id = ?");
        $kontrol->execute([$sanatci_id]);
        $muzik_sayisi = $kontrol->fetchColumn();

        if ($muzik_sayisi > 0) {
            throw new Exception("Bu sanatçıya ait müzikler var. Önce müzikleri sil.");
        }

        // DELETE
        $sil = $pdo->prepare("DELETE FROM Sanatcilar WHERE sanatci_id = ?");
        $sil->execute([$sanatci_id]);

        $basari = "🗑️ Sanatçı silindi.";

    } catch (Exception $e) {
        $hata = "❌ " . $e->getMessage();
    }
}
?>

<div class="welcome">
    <h2>🎤 Sanatçılar</h2>
    <p>Sanatçıları buradan yönetebilirsin</p>
</div>

<div class="cards">

    <!-- SANATÇI EKLE -->
    <div class="card">
        <span>➕</span>
        <h3>Sanatçı Ekle</h3>

        <?php if ($hata): ?>
            <p style="color:#f87171"><?= $hata ?></p>
        <?php endif; ?>

        <?php if ($basari): ?>
            <p style="color:#4ade80"><?= $basari ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="sanatci_adi" placeholder="Sanatçı Adı" required><br><br>
            <button type="submit" class="btn-primary">Sanatçı Ekle</button>
        </form>
    </div>

    <!-- SANATÇI LİSTESİ -->
    <?php
    $q = $pdo->query("
        SELECT 
            s.sanatci_id,
            s.sanatci_adi,
            COUNT(m.muzik_id) AS muzik_sayisi
        FROM Sanatcilar s
        LEFT JOIN Muzikler m ON s.sanatci_id = m.sanatci_id
        GROUP BY s.sanatci_id
        ORDER BY s.sanatci_adi
    ");

    while ($row = $q->fetch(PDO::FETCH_ASSOC)):
    ?>

        <div class="card">
            <span>🎤</span>
            <h3><?= htmlspecialchars($row["sanatci_adi"]) ?></h3>
            <p><?= $row["muzik_sayisi"] ?> müzik</p>

            <!-- SİL BUTONU -->
            <form method="POST" style="margin-top:10px">
                <input type="hidden" name="sanatci_id" value="<?= $row["sanatci_id"] ?>">
                <button type="submit"
                        name="sanatci_sil"
                        class="btn-secondary"
                        onclick="return confirm('Bu sanatçı silinsin mi?')">
                    🗑️ Sil
                </button>
            </form>
        </div>

    <?php endwhile; ?>

</div>

<?php include "layout/footer.php"; ?>
