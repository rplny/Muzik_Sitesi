<?php
include "config.php";
include "layout/header.php";

$hata = "";
$basari = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $muzik_adi  = trim($_POST["muzik_adi"]);
        $sanatci_id = (int)$_POST["sanatci_id"];
        $tur_id     = (int)$_POST["tur_id"];

        if ($muzik_adi === "") {
            throw new Exception("Müzik adı boş olamaz.");
        }

        if (!isset($_FILES["muzik_dosya"])) {
            throw new Exception("Dosya seçilmedi.");
        }

        $dosya = $_FILES["muzik_dosya"];

        if ($dosya["error"] !== 0) {
            throw new Exception("Dosya yüklenemedi.");
        }

        $uzanti = strtolower(pathinfo($dosya["name"], PATHINFO_EXTENSION));
        if ($uzanti !== "mp3") {
            throw new Exception("Sadece MP3 dosyaları kabul edilir.");
        }

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        $yeni_ad = uniqid("muzik_") . ".mp3";
        $hedef = "uploads/" . $yeni_ad;

        if (!move_uploaded_file($dosya["tmp_name"], $hedef)) {
            throw new Exception("Dosya sunucuya taşınamadı.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO Muzikler 
            (muzik_adi, muzik_dosya, sanatci_id, tur_id, sure_saniye)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $muzik_adi,
            $yeni_ad,
            $sanatci_id,
            $tur_id,
            1
        ]);

        $basari = "🎵 Müzik başarıyla eklendi.";

    } catch (Exception $e) {
        $hata = "❌ " . $e->getMessage();
    }
}
?>

<div class="welcome">
    <h2>➕ Müzik Ekle</h2>
    <p>MP3 formatında yeni müzik ekle</p>
</div>

<div class="cards">
    <div class="card">

        <?php if ($hata): ?>
            <p style="color:#f87171"><?= $hata ?></p>
        <?php endif; ?>

        <?php if ($basari): ?>
            <p style="color:#4ade80"><?= $basari ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="muzik_adi" placeholder="Müzik Adı" required><br><br>

            <select name="sanatci_id" required>
                <option value="">Sanatçı Seç</option>
                <?php
                $s = $pdo->query("SELECT sanatci_id, sanatci_adi FROM Sanatcilar");
                while ($row = $s->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['sanatci_id']}'>{$row['sanatci_adi']}</option>";
                }
                ?>
            </select><br><br>

            <select name="tur_id" required>
                <option value="">Tür Seç</option>
                <?php
                $t = $pdo->query("SELECT tur_id, tur_adi FROM Turler");
                while ($row = $t->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['tur_id']}'>{$row['tur_adi']}</option>";
                }
                ?>
            </select><br><br>

            <input type="file" name="muzik_dosya" accept=".mp3" required><br><br>

            <!-- ASIL İSTEDİĞİN KISIM -->
            <button type="submit" class="btn-primary">🎶 Müzik Ekle</button>

        </form>

    </div>
</div>

<?php include "layout/footer.php"; ?>
