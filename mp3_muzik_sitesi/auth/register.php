<?php
include "../config.php";
include "../layout/header.php";

$hata = "";
$basari = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $kullanici_adi = trim($_POST["kullanici_adi"]);
        $email = trim($_POST["email"]);
        $sifre = $_POST["sifre"];

        if ($kullanici_adi === "" || $email === "" || $sifre === "") {
            throw new Exception("Tüm alanlar zorunludur.");
        }

        // email daha önce kayıtlı mı
        $kontrol = $pdo->prepare("SELECT kullanici_id FROM Kullanicilar WHERE email = ?");
        $kontrol->execute([$email]);
        if ($kontrol->rowCount() > 0) {
            throw new Exception("Bu email zaten kayıtlı.");
        }

        // şifreyi hashle (çok önemli)
        $hashli_sifre = password_hash($sifre, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO Kullanicilar (kullanici_adi, email, sifre)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$kullanici_adi, $email, $hashli_sifre]);

        $basari = "Kayıt başarılı! Giriş yapabilirsin.";

    } catch (Exception $e) {
        $hata = $e->getMessage();
    }
}
?>

<div class="welcome">
    <h2>📝 Kayıt Ol</h2>
    <p>Yeni bir hesap oluştur</p>
</div>

<div class="cards">
    <div class="card">

        <?php if ($hata): ?>
            <p style="color:#f87171"><?= htmlspecialchars($hata) ?></p>
        <?php endif; ?>

        <?php if ($basari): ?>
            <p style="color:#4ade80"><?= htmlspecialchars($basari) ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="kullanici_adi" placeholder="Kullanıcı Adı" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="sifre" placeholder="Şifre" required><br><br>

            <button type="submit" class="btn-primary">Kayıt Ol</button>
        </form>

        <p style="margin-top:15px">
            Zaten hesabın var mı?
            <a href="login.php" style="color:#38bdf8">Giriş Yap</a>
        </p>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
