<?php
include "config.php";
include "layout/header.php";
?>

<div class="welcome">
    <h2>👤 Kullanıcılar</h2>
    <p>Sisteme kayıtlı tüm kullanıcılar</p>
</div>

<div class="cards">

<?php
$sorgu = $pdo->query("
    SELECT kullanici_adi, email, kayit_tarihi
    FROM Kullanicilar
    ORDER BY kullanici_id DESC
");

while ($k = $sorgu->fetch(PDO::FETCH_ASSOC)):
?>

    <div class="card">
        <span>👤</span>
        <h3><?= htmlspecialchars($k["kullanici_adi"]) ?></h3>
        <p><?= htmlspecialchars($k["email"]) ?></p>
        <p style="font-size:13px;color:#94a3b8">
            Kayıt: <?= $k["kayit_tarihi"] ?>
        </p>
    </div>

<?php endwhile; ?>

</div>

<?php include "layout/footer.php"; ?>
