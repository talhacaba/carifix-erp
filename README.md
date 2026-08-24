# CariFix ERP - Fabrika Yönetim Sistemi

CariFix, üretim tesisleri ve B2B operasyonları için özel olarak geliştirilmiş, yüksek performanslı ve güvenli bir ERP (Kurumsal Kaynak Planlama) sistemidir. Fabrika yönetimini, üretim süreçlerini ve cari takibini tek bir çatı altında toplar.

<img width="1900" height="943" alt="Screenshot_1" src="https://github.com/user-attachments/assets/7e4e73ee-84c4-4e69-bcdf-30a21bf35ac5" />


## 🚀 Özellikler

*   **Üretim Takibi:** Karton bardak üretim süreçlerinin, vardiyaların ve stokların uçtan uca yönetimi.
*   **Cari ve Ön Muhasebe:** Müşteri ve tedarikçi bakiye takibi, sipariş yönetimi.
*   **Modern ve Hızlı Arayüz:** Tailwind CSS ile geliştirilmiş, tamamen responsive ve kullanıcı dostu tasarım.
*   **Güvenli Altyapı:** PDO tabanlı güvenli veritabanı mimarisi, token tabanlı şifrelenmiş "Beni Hatırla" sistemi.

## 🛠️ Teknolojiler

*   **Backend:** PHP 8+, PDO (MySQL)
*   **Frontend:** HTML5, Tailwind CSS, Lucide Icons

## 📥 Kurulum

1.  Projeyi sunucunuza (XAMPP htdocs veya canlı sunucunuzdaki public_html dizinine) aktarın.
2.  `ayar.php` dosyasını açarak kendi veritabanı bilgilerinizi girin:
    ```php
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'veritabani_adiniz');
    define('DB_USER', 'kullanici_adiniz');
    define('DB_PASS', 'sifreniz');
    ```
3.  Tarayıcınızdan sitenize giriş yapın. Veritabanı tabloları ve varsayılan yönetici hesabı, ilk girişte sistem tarafından otomatik olarak oluşturulacaktır (Dahili migration yapısı).
4.  **Varsayılan Giriş Bilgileri:**
    *   **Kullanıcı Adı:** `admin`
    *   **Şifre:** `123456`
    *(Sisteme giriş yaptıktan sonra şifrenizi değiştirmeniz önerilir.)*

## 🛡️ Güvenlik ve Mimari

Sistemin çekirdek dosyası (`db.php`), izinsiz düzenlenip satılmayı ve üzerinden telif/imza hakkı silinmesini engellemek amacıyla özel matematiksel şifreleme ve binary seviyesinde şifrelenmiştir. 

Sistem arayüzü açık kaynaklıdır; HTML, CSS ve Tailwind kodları kullanıcılar tarafından özgürce kişiselleştirilebilir.

## 👨‍💻 Geliştirici

**Talha Caba**
*   **Web:** [talha.gen.tr](https://talha.gen.tr/)
*   **Proje:** CariFix ERP
