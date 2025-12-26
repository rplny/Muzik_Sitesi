<?php
// session başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// aktif menü için
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>MP3 Yönetim Paneli</title>

<!-- CSS -->
<link rel="stylesheet" href="/mp3_muzik_sitesi/style.css">

<!-- Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

<div class="dashboard">

    <aside class="sidebar">
       <h1 class="logo-text">🎵 Pelotify</h1>

        <!-- KULLANICI BİLGİSİ -->
        <div style="margin-bottom:20px; font-size:14px; color:#94a3b8">
            <?php if (isset($_SESSION["kullanici"])): ?>
                👋 Hoşgeldin<br>
                <strong><?= htmlspecialchars($_SESSION["kullanici"]["ad"]) ?></strong><br>
                <a href="/mp3_muzik_sitesi/auth/logout.php"
                   style="color:#38bdf8; font-size:13px">
                   Çıkış Yap
                </a>
            <?php else: ?>
                👤 Misafir<br>
                <a href="/mp3_muzik_sitesi/auth/login.php"
                   style="color:#38bdf8; font-size:13px">
                   Giriş
                </a> |
                <a href="/mp3_muzik_sitesi/auth/register.php"
                   style="color:#38bdf8; font-size:13px">
                   Kayıt
                </a>
            <?php endif; ?>
        </div>

        <nav>
            <a href="/mp3_muzik_sitesi/index.php"
               class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">
               🏠 Ana Sayfa
            </a>

            <a href="/mp3_muzik_sitesi/muzikler.php"
               class="<?= $currentPage == 'muzikler.php' ? 'active' : '' ?>">
               🎧 Müzikler
            </a>

            <a href="/mp3_muzik_sitesi/muzik_ekle.php"
               class="<?= $currentPage == 'muzik_ekle.php' ? 'active' : '' ?>">
               ➕ Müzik Ekle
            </a>

            <a href="/mp3_muzik_sitesi/sanatcilar.php"
               class="<?= $currentPage == 'sanatcilar.php' ? 'active' : '' ?>">
               🎤 Sanatçılar
            </a>

            <a href="/mp3_muzik_sitesi/turler.php"
               class="<?= $currentPage == 'turler.php' ? 'active' : '' ?>">
               🏷️ Türler
            </a>

            <a href="/mp3_muzik_sitesi/kullanicilar.php"
               class="<?= $currentPage == 'kullanicilar.php' ? 'active' : '' ?>">
               👤 Kullanıcılar
            </a>
        </nav>
    </aside>

    <main class="content">
