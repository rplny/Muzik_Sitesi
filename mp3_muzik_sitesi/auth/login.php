<?php
include "../config.php";
include "../layout/header.php";

$hata = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $sifre = $_POST["sifre"];

    $stmt = $pdo->prepare("SELECT * FROM Kullanicilar WHERE email = ?");
    $stmt->execute([$email]);
    $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($kullanici && password_verify($sifre, $kullanici["sifre"])) {
        $_SESSION["kullanici"] = [
            "id" => $kullanici["kullanici_id"],
            "ad" => $kullanici["kullanici_adi"]
        ];
        header("Location: ../index.php");
        exit;
    } else {
        $hata = "Email veya şifre hatalı.";
    }
}
?>

<div class="welcome">
    <h2>🔐 Giriş Yap</h2>
    <p>Hesabınla giriş yap</p>
</div>

<div class="cards">
    <div class="card">

        <?php if ($hata): ?>
            <p style="color:#f87171"><?= $hata ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="password" name="sifre" placeholder="Şifre" required><br><br>
            <button class="btn-primary">Giriş Yap</button>
        </form>

        <p style="margin-top:15px">
            Hesabın yok mu?
            <a href="register.php" style="color:#38bdf8">Kayıt Ol</a>
        </p>

    </div>
</div>

<?php include "../layout/footer.php"; ?>
