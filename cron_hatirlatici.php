<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
$bugun = date('Y-m-d');
$sorgu = $db->prepare("SELECT * FROM satislar WHERE vade_tarihi <= ? AND durum = 'Acik'");
$sorgu->execute([$bugun]);
$borclar = $sorgu->fetchAll(PDO::FETCH_ASSOC);

foreach ($borclar as $borc) {
    $mesaj = "Sayın Yetkili, {$borc['musteri_adi']} isimli müşterinin ₺{$borc['kalan_borc']} tutarındaki veresiye ödeme vadesi gelmiştir.";
}

$giderler = $db->prepare("SELECT * FROM giderler WHERE vade_tarihi <= ?");
$giderler->execute([$bugun]);
?>
