DROP DATABASE IF EXISTS mp3_muzik_sitesi;
CREATE DATABASE mp3_muzik_sitesi;
USE mp3_muzik_sitesi;


CREATE TABLE Kullanicilar (
    kullanici_id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    sifre VARCHAR(100) NOT NULL,
    kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Sanatcilar (
    sanatci_id INT AUTO_INCREMENT PRIMARY KEY,
    sanatci_adi VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE Turler (
    tur_id INT AUTO_INCREMENT PRIMARY KEY,
    tur_adi VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Muzikler (
    muzik_id INT AUTO_INCREMENT PRIMARY KEY,
    muzik_adi VARCHAR(100) NOT NULL,
    muzik_dosya VARCHAR(255) NOT NULL,
    sanatci_id INT NOT NULL,
    tur_id INT NOT NULL,
    sure_saniye INT NOT NULL CHECK (sure_saniye > 0),
    eklenme_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sanatci_id) REFERENCES Sanatcilar(sanatci_id),
    FOREIGN KEY (tur_id) REFERENCES Turler(tur_id)
);

CREATE TABLE Begeni (
    begeni_id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    muzik_id INT NOT NULL,
    begeni_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kullanici_id) REFERENCES Kullanicilar(kullanici_id),
    FOREIGN KEY (muzik_id) REFERENCES Muzikler(muzik_id) ON DELETE CASCADE,
    UNIQUE (kullanici_id, muzik_id)
);

CREATE TABLE LogKayitlari (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    tablo_adi VARCHAR(50),
    islem_turu VARCHAR(20),
    kayit_id INT,
    islem_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO Kullanicilar (kullanici_adi, email, sifre)
VALUES ('pelombappe', 'pelin@mail.com', '1881');

INSERT INTO Sanatcilar (sanatci_adi) VALUES
('Sabrina Carpenter'),
('Dua Lipa'),
('Hakan Peker'),
('The Weeknd'),
('Manifest'),
('Ceza');

INSERT INTO Turler (tur_adi) VALUES
('Rap'),
('Pop'),
('Cover'),
('Rock'),
('Metal');

INSERT INTO Muzikler (muzik_adi, muzik_dosya, sanatci_id, tur_id, sure_saniye) VALUES
('Man Child',     'manchild.mp3',   1, 2, 240),
('Bir Efsaneydi', 'birefsane.mp3',  3, 2, 240),
('Espresso',      'espresso.mp3',   1, 2, 240),
('Amateur',       'amateur.mp3',    5, 2, 240),
('Levitating',    'levitating.mp3', 2, 2, 240),
('Die For You',   'dieforyou.mp3',  4, 2, 240);

INSERT INTO Begeni (kullanici_id, muzik_id) VALUES (1,1);

SELECT m.muzik_adi, s.sanatci_adi
FROM Muzikler m JOIN Sanatcilar s ON m.sanatci_id = s.sanatci_id;

SELECT t.tur_adi, COUNT(m.muzik_id) AS muzik_sayisi
FROM Turler t LEFT JOIN Muzikler m ON t.tur_id = m.tur_id
GROUP BY t.tur_adi;

SELECT muzik_adi FROM Muzikler
WHERE muzik_id NOT IN (SELECT muzik_id FROM Begeni);

SELECT k.kullanici_adi, COUNT(*) AS begeni_sayisi
FROM Kullanicilar k JOIN Begeni b ON k.kullanici_id=b.kullanici_id
GROUP BY k.kullanici_adi HAVING COUNT(*) > 0;

SELECT * FROM Muzikler ORDER BY eklenme_tarihi DESC;

SELECT s.sanatci_adi, COUNT(m.muzik_id)
FROM Sanatcilar s JOIN Muzikler m ON s.sanatci_id=m.sanatci_id
GROUP BY s.sanatci_adi;
/*
SELECT * FROM Muzikler
WHERE sure_saniye > (SELECT AVG(sure_saniye) FROM Muzikler);
*/
SELECT k.kullanici_adi, m.muzik_adi
FROM Begeni b
JOIN Kullanicilar k ON b.kullanici_id=k.kullanici_id
JOIN Muzikler m ON b.muzik_id=m.muzik_id;

SELECT tur_id, COUNT(*) FROM Muzikler GROUP BY tur_id;

SELECT * FROM Kullanicilar
WHERE kullanici_id IN (SELECT kullanici_id FROM Begeni);


DELIMITER //

CREATE PROCEDURE MuzikEkle (
    IN p_adi VARCHAR(100),
    IN p_dosya VARCHAR(255),
    IN p_sanatci INT,
    IN p_tur INT,
    IN p_sure INT
)
BEGIN
    INSERT INTO Muzikler (muzik_adi, muzik_dosya, sanatci_id, tur_id, sure_saniye)
    VALUES (p_adi, p_dosya, p_sanatci, p_tur, p_sure);
END;
//

CREATE PROCEDURE MuzikSil (IN p_id INT)
BEGIN
    DELETE FROM Muzikler WHERE muzik_id = p_id;
END;
//

CREATE PROCEDURE KullaniciBegenileri (IN p_kullanici INT)
BEGIN
    SELECT m.muzik_adi
    FROM Begeni b JOIN Muzikler m ON b.muzik_id=m.muzik_id
    WHERE b.kullanici_id = p_kullanici;
END;
//
DELIMITER ;

CREATE VIEW vw_MuzikSanatci AS
SELECT m.muzik_adi, s.sanatci_adi
FROM Muzikler m JOIN Sanatcilar s ON m.sanatci_id=s.sanatci_id;

CREATE VIEW vw_TurMuzik AS
SELECT t.tur_adi, m.muzik_adi
FROM Turler t JOIN Muzikler m ON t.tur_id=m.tur_id;

CREATE VIEW vw_KullaniciBegenileri AS
SELECT k.kullanici_adi, m.muzik_adi
FROM Begeni b
JOIN Kullanicilar k ON b.kullanici_id=k.kullanici_id
JOIN Muzikler m ON b.muzik_id=m.muzik_id;

START TRANSACTION;
INSERT INTO Sanatcilar (sanatci_adi) VALUES ('Twenty One Pilots');
COMMIT;

START TRANSACTION;
INSERT INTO Turler (tur_adi) VALUES ('Jazz');
ROLLBACK;

START TRANSACTION;
INSERT INTO Muzikler (muzik_adi, muzik_dosya, sanatci_id, tur_id, sure_saniye)
VALUES ('Türk Marşı', 'turk_marsi.mp3', 6, 1, 210);
COMMIT;

DELIMITER //

CREATE TRIGGER trg_muzik_insert
AFTER INSERT ON Muzikler
FOR EACH ROW
BEGIN
    INSERT INTO LogKayitlari (tablo_adi,islem_turu,kayit_id)
    VALUES ('Muzikler','INSERT',NEW.muzik_id);
END;
//

CREATE TRIGGER trg_muzik_update
AFTER UPDATE ON Muzikler
FOR EACH ROW
BEGIN
    INSERT INTO LogKayitlari (tablo_adi,islem_turu,kayit_id)
    VALUES ('Muzikler','UPDATE',NEW.muzik_id);
END;
//

CREATE TRIGGER trg_muzik_delete
AFTER DELETE ON Muzikler
FOR EACH ROW
BEGIN
    INSERT INTO LogKayitlari (tablo_adi,islem_turu,kayit_id)
    VALUES ('Muzikler','DELETE',OLD.muzik_id);
END;
//
DELIMITER ;

SELECT * FROM Muzikler;
SELECT * FROM LogKayitlari;