<?php
include "config.php";
include "layout/header.php";

$kullanici_id = 1; // şimdilik sabit kullanıcı
?>

<div class="welcome">
    <h2>🎧 Müzikler</h2>
    <p>Sistemde kayıtlı tüm müzikler</p>
</div>

<div class="cards">

<?php
$q = $pdo->prepare("
    SELECT 
        m.muzik_id,
        m.muzik_adi,
        m.muzik_dosya,
        s.sanatci_adi,
        t.tur_adi,
        COUNT(b.begeni_id) AS begeni_sayisi,
        MAX(CASE WHEN b.kullanici_id = ? THEN 1 ELSE 0 END) AS kullanici_begendi
    FROM Muzikler m
    LEFT JOIN Sanatcilar s ON m.sanatci_id = s.sanatci_id
    LEFT JOIN Turler t ON m.tur_id = t.tur_id
    LEFT JOIN Begeni b ON b.muzik_id = m.muzik_id
    GROUP BY m.muzik_id
    ORDER BY m.muzik_id DESC
");
$q->execute([$kullanici_id]);

while ($row = $q->fetch(PDO::FETCH_ASSOC)):
?>

<div class="card">
    <span>🎵</span>

    <h3><?= htmlspecialchars($row["muzik_adi"]) ?></h3>

    <p>
        <?= htmlspecialchars($row["sanatci_adi"] ?? "Sanatçı Yok") ?>
        •
        <?= htmlspecialchars($row["tur_adi"] ?? "Tür Yok") ?>
    </p>

    <!-- AKSİYONLAR -->
    <div class="card-actions">

        <!-- OYNAT -->
        <button class="btn-primary"
            onclick="playSong(
                '<?= htmlspecialchars($row['muzik_adi'], ENT_QUOTES) ?>',
                '<?= htmlspecialchars($row['sanatci_adi'] ?? 'Bilinmeyen', ENT_QUOTES) ?>',
                'muzikler/<?= htmlspecialchars($row['muzik_dosya'], ENT_QUOTES) ?>'
            )">
            ▶ Oynat
        </button>

        <!-- BEĞEN / GERİ AL -->
        <a href="begen.php?muzik_id=<?= (int)$row['muzik_id'] ?>"
           class="btn-like">
            <?= $row['kullanici_begendi'] ? '💔 Beğeniyi Geri Al' : '❤️ Beğen' ?>
        </a>

        <!-- BEĞENİ SAYISI -->
        <div class="like-count">
            ❤️ <?= (int)$row['begeni_sayisi'] ?> beğeni
        </div>

        <!-- SİL -->
        <form method="POST" action="muzik_sil.php">
            <input type="hidden" name="muzik_id" value="<?= (int)$row["muzik_id"] ?>">
            <button type="submit"
                    class="btn-secondary"
                    onclick="return confirm('Bu müzik silinsin mi?')">
                🗑️ Müzik Sil
            </button>
        </form>

    </div>
</div>

<?php endwhile; ?>

</div>

<?php include "layout/footer.php"; ?>

<!-- MİNİ PLAYER -->
<div id="miniPlayer" class="mini-player hidden">
    <div class="mini-info">
        <div id="miniTitle" class="mini-title"></div>
        <div id="miniArtist" class="mini-artist"></div>
    </div>
    <audio id="globalAudio" controls></audio>
</div>

<!-- MİNİ PLAYER SCRIPT -->
<script>
const miniPlayer = document.getElementById("miniPlayer");
const audio = document.getElementById("globalAudio");
const titleEl = document.getElementById("miniTitle");
const artistEl = document.getElementById("miniArtist");

function playSong(title, artist, src) {
    titleEl.textContent = title;
    artistEl.textContent = artist;
    audio.src = src;
    miniPlayer.classList.remove("hidden");
    audio.play();
}
</script>
