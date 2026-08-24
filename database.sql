-- --------------------------------------------------------
-- Sunucu:                       127.0.0.1
-- Sunucu sürümü:                10.4.32-MariaDB - mariadb.org binary distribution
-- Sunucu İşletim Sistemi:       Win64
-- HeidiSQL Sürüm:               12.14.0.7165
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- bardak_fabrika için veritabanı yapısı dökülüyor
CREATE DATABASE IF NOT EXISTS `bardak_fabrika` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `bardak_fabrika`;

-- tablo yapısı dökülüyor bardak_fabrika.ajanda_notlari
CREATE TABLE IF NOT EXISTS `ajanda_notlari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarih` date NOT NULL,
  `baslik` varchar(150) NOT NULL,
  `kategori` enum('Vergi','Veresiye','Toplanti','Diger') DEFAULT 'Diger',
  `durum` enum('Bekliyor','Tamamlandi') DEFAULT 'Bekliyor',
  `aciklama` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.ajanda_notlari: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor bardak_fabrika.cari_tahsilatlar
CREATE TABLE IF NOT EXISTS `cari_tahsilatlar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `satis_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `alinan_tutar` decimal(10,2) NOT NULL,
  `aciklama` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `satis_id` (`satis_id`),
  CONSTRAINT `cari_tahsilatlar_ibfk_1` FOREIGN KEY (`satis_id`) REFERENCES `satislar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.cari_tahsilatlar: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor bardak_fabrika.depo_stok
CREATE TABLE IF NOT EXISTS `depo_stok` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hammaddde_adi` varchar(150) NOT NULL,
  `mevcut_tonaj` decimal(10,2) NOT NULL DEFAULT 0.00,
  `kritik_seviye` decimal(10,2) NOT NULL DEFAULT 0.50,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.depo_stok: ~1 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `depo_stok` (`id`, `hammaddde_adi`, `mevcut_tonaj`, `kritik_seviye`) VALUES
	(1, 'Ana Karton Bobin Stoğu', 5.66, 1.00);

-- tablo yapısı dökülüyor bardak_fabrika.giderler
CREATE TABLE IF NOT EXISTS `giderler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarih` date NOT NULL,
  `gider_turu` varchar(100) NOT NULL,
  `tutar` decimal(12,2) NOT NULL,
  `vade_tarihi` date DEFAULT NULL,
  `tekrar_tipi` enum('Birinci','Aylik','Yillik') DEFAULT 'Birinci',
  `aciklama` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.giderler: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor bardak_fabrika.gunluk_uretim
CREATE TABLE IF NOT EXISTS `gunluk_uretim` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarih` date NOT NULL,
  `koli_adedi` int(11) NOT NULL,
  `bardak_adedi` int(11) NOT NULL,
  `fire_miktari` int(11) DEFAULT 0,
  `notlar` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.gunluk_uretim: ~1 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `gunluk_uretim` (`id`, `tarih`, `koli_adedi`, `bardak_adedi`, `fire_miktari`, `notlar`) VALUES
	(1, '2026-07-25', 14, 42000, 12, '');

-- tablo yapısı dökülüyor bardak_fabrika.hammadde_girisleri
CREATE TABLE IF NOT EXISTS `hammadde_girisleri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarih` date NOT NULL,
  `tedarikci_adi` varchar(150) NOT NULL,
  `tonaj` decimal(10,2) NOT NULL,
  `birim_fiyat` decimal(10,2) NOT NULL,
  `toplam_tutar` decimal(12,2) NOT NULL,
  `fatura_dosya` varchar(255) DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.hammadde_girisleri: ~4 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `hammadde_girisleri` (`id`, `tarih`, `tedarikci_adi`, `tonaj`, `birim_fiyat`, `toplam_tutar`, `fatura_dosya`, `aciklama`) VALUES
	(1, '2026-07-25', 'Test Kağıtçılık Limited Şirketi', 2.00, 63912.00, 127824.00, 'uploads/faturalar/6a6509553954e.pdf', 'test'),
	(2, '2026-07-25', 'Test Kağıtçılık Limited Şirketi', 2.00, 63912.00, 127824.00, 'uploads/faturalar/6a6509980528c.pdf', 'test'),
	(3, '2026-07-25', 'Test Kağıtçılık Limited Şirketi', 2.00, 63912.00, 127824.00, 'uploads/faturalar/6a65099bd4b99.pdf', 'test'),
	(4, '2026-07-25', 'Test Kağıt Baskı A.Ş', 0.50, 65000.00, 32500.00, 'uploads/faturalar/6a6509e39f9a9.pdf', 'ss');

-- tablo yapısı dökülüyor bardak_fabrika.kullanicilar
CREATE TABLE IF NOT EXISTS `kullanicilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(50) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `ad_soyad` varchar(100) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.kullanicilar: ~1 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `sifre`, `ad_soyad`, `remember_token`) VALUES
	(1, 'admin', '123456', 'CABA', NULL);

-- tablo yapısı dökülüyor bardak_fabrika.makine_bakim
CREATE TABLE IF NOT EXISTS `makine_bakim` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `makine_adi` varchar(150) NOT NULL,
  `islem_tarihi` date NOT NULL,
  `islem_tipi` varchar(50) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `degisen_parcalar` text DEFAULT NULL,
  `maliyet` decimal(10,2) DEFAULT 0.00,
  `durum` varchar(50) DEFAULT 'Tamamlandi',
  `sonraki_bakim_tarihi` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.makine_bakim: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor bardak_fabrika.musteriler
CREATE TABLE IF NOT EXISTS `musteriler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firma_adi` varchar(150) NOT NULL,
  `yetkili_kisi` varchar(100) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `adres` text DEFAULT NULL,
  `kayit_tarihi` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.musteriler: ~1 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `musteriler` (`id`, `firma_adi`, `yetkili_kisi`, `telefon`, `adres`, `kayit_tarihi`) VALUES
	(1, 'Bakkalcı mehmet', 'mehmet', '05555555', 'TEST', '2026-07-25');

-- tablo yapısı dökülüyor bardak_fabrika.personel
CREATE TABLE IF NOT EXISTS `personel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_soyad` varchar(150) NOT NULL,
  `gorev` varchar(100) NOT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `maas` decimal(10,2) NOT NULL,
  `ise_baslama_tarihi` date NOT NULL,
  `durum` enum('Aktif','Ayrildi') DEFAULT 'Aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.personel: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor bardak_fabrika.personel_avanslari
CREATE TABLE IF NOT EXISTS `personel_avanslari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `personel_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `tutar` decimal(10,2) NOT NULL,
  `aciklama` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personel_id` (`personel_id`),
  CONSTRAINT `personel_avanslari_ibfk_1` FOREIGN KEY (`personel_id`) REFERENCES `personel` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.personel_avanslari: ~0 rows (yaklaşık) tablosu için veriler indiriliyor

-- tablo yapısı dökülüyor bardak_fabrika.satislar
CREATE TABLE IF NOT EXISTS `satislar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarih` date NOT NULL,
  `musteri_id` int(11) NOT NULL,
  `urun_id` int(11) NOT NULL,
  `koli_adedi` int(11) NOT NULL,
  `toplam_tutar` decimal(12,2) NOT NULL,
  `odenen_tutar` decimal(12,2) NOT NULL,
  `kalan_borc` decimal(12,2) NOT NULL,
  `vade_tarihi` date DEFAULT NULL,
  `durum` enum('Kapandi','Acik') DEFAULT 'Acik',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.satislar: ~3 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `satislar` (`id`, `tarih`, `musteri_id`, `urun_id`, `koli_adedi`, `toplam_tutar`, `odenen_tutar`, `kalan_borc`, `vade_tarihi`, `durum`) VALUES
	(1, '2026-07-25', 0, 0, 12, 15000.00, 0.00, 15000.00, '2026-07-25', 'Acik'),
	(2, '2026-07-25', 0, 0, 12, 15000.00, 0.00, 15000.00, '2026-07-25', 'Acik'),
	(3, '2026-07-25', 1, 1, 1, 1250.00, 1250.00, 0.00, '2026-07-25', 'Kapandi');

-- tablo yapısı dökülüyor bardak_fabrika.urunler
CREATE TABLE IF NOT EXISTS `urunler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `urun_adi` varchar(150) NOT NULL,
  `ebat` varchar(50) NOT NULL,
  `koli_ici_adet` int(11) NOT NULL DEFAULT 3000,
  `varsayilan_fiyat` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- bardak_fabrika.urunler: ~1 rows (yaklaşık) tablosu için veriler indiriliyor
INSERT INTO `urunler` (`id`, `urun_adi`, `ebat`, `koli_ici_adet`, `varsayilan_fiyat`) VALUES
	(1, 'Baskılı Karton Bardak', '6.5 Oz', 3000, 1250.00);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
