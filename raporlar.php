<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$toplam_uretim = $db->query("SELECT SUM(bardak_adedi) FROM gunluk_uretim")->fetchColumn() ?: 0;
$toplam_koli = $db->query("SELECT SUM(koli_adedi) FROM gunluk_uretim")->fetchColumn() ?: 0;
$toplam_satis = $db->query("SELECT SUM(toplam_tutar) FROM satislar")->fetchColumn() ?: 0;
$toplam_veresiye = $db->query("SELECT SUM(kalan_borc) FROM satislar WHERE durum='Acik'")->fetchColumn() ?: 0;
$toplam_gider = $db->query("SELECT SUM(tutar) FROM giderler")->fetchColumn() ?: 0;
$toplam_hammadde_maliyet = $db->query("SELECT SUM(toplam_tutar) FROM hammadde_girisleri")->fetchColumn() ?: 0;
$toplam_hammadde_tonaj = $db->query("SELECT SUM(tonaj) FROM hammadde_girisleri")->fetchColumn() ?: 0;
$net_kar = $toplam_satis - ($toplam_gider + $toplam_hammadde_maliyet);
$uretilenler = $db->query("SELECT * FROM gunluk_uretim ORDER BY tarih DESC")->fetchAll(PDO::FETCH_ASSOC);
$satislar = $db->query("SELECT s.*, m.firma_adi, u.urun_adi, u.ebat 
                        FROM satislar s 
                        LEFT JOIN musteriler m ON s.musteri_id = m.id 
                        LEFT JOIN urunler u ON s.urun_id = u.id 
                        ORDER BY s.tarih DESC")->fetchAll(PDO::FETCH_ASSOC);

$giderler = $db->query("SELECT * FROM giderler ORDER BY tarih DESC")->fetchAll(PDO::FETCH_ASSOC);
$hammadde_listesi = $db->query("SELECT * FROM hammadde_girisleri ORDER BY tarih DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Resmi Mali Bilanço ve Fabrika Raporu - CariFix Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media print {
            body { background: white !important; color: black !important; font-size: 11px; }
            aside, header button, .no-print { display: none !important; }
            main { padding: 0 !important; overflow: visible !important; width: 100% !important; }
            .bg-white { border: none !important; box-shadow: none !important; padding: 0 !important; }
            table { font-size: 10px !important; }
            .print-page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 flex h-screen overflow-hidden">

    <?php include 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 px-10 flex items-center justify-between sticky top-0 z-10 no-print">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Resmi Mali Bilanço ve Rapor Merkezi</h2>
                <p class="text-xs text-slate-500">Limited Şirket entegre denetim ve maliyet dökümleri.</p>
            </div>
            <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl text-sm transition shadow-md shadow-indigo-600/20 flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i> Resmi PDF Rapor Çıkart / Yazdır
            </button>
        </header>

        <div class="p-10 space-y-10 max-w-6xl mx-auto w-full bg-slate-50">
            
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-6">
                <div class="flex justify-between items-start border-b border-slate-100 pb-6">
                    <div>
                        <h1 class="text-2xl font-black text-slate-900">BİLANÇO VE MALİ PERFORMANS RAPORU</h1>
                        <p class="text-xs text-slate-500 mt-1">CariFix Enterprise Üretim ve Ticaret Limited Şirketi</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-slate-400 block">Rapor Tarihi</span>
                        <span class="text-sm font-bold text-slate-800"><?= date('d.m.Y H:i') ?></span>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-4">
                    <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl">
                        <span class="text-[11px] font-bold text-slate-400 uppercase block">Toplam Brüt Ciro</span>
                        <span class="text-lg font-black text-emerald-600">₺<?= number_format($toplam_satis, 2) ?></span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl">
                        <span class="text-[11px] font-bold text-slate-400 uppercase block">Hammadde Maliyeti</span>
                        <span class="text-lg font-black text-red-600">₺<?= number_format($toplam_hammadde_maliyet, 2) ?></span>
                    </div>
                    <div class="bg-slate-50 border border-slate-200/60 p-4 rounded-xl">
                        <span class="text-[11px] font-bold text-slate-400 uppercase block">Genel Gider & Vergi</span>
                        <span class="text-lg font-black text-red-600">₺<?= number_format($toplam_gider, 2) ?></span>
                    </div>
                    <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-xl">
                        <span class="text-[11px] font-bold text-indigo-700 uppercase block">Net Şirket Kârı</span>
                        <span class="text-lg font-black text-indigo-900">₺<?= number_format($net_kar, 2) ?></span>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-4">
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="factory" class="w-4 h-4 text-indigo-600"></i> 1. Üretim ve Hammadde Tüketim Dökümü
                </h3>
                <div class="grid grid-cols-2 gap-4 text-xs font-medium text-slate-600 mb-4">
                    <div>Toplam Üretilen Koli: <strong class="text-slate-900"><?= number_format($toplam_koli) ?> Koli</strong></div>
                    <div>Toplam Üretilen Bardak: <strong class="text-indigo-600"><?= number_format($toplam_uretim) ?> Adet</strong></div>
                    <div>Alınan Toplam Hammadde: <strong class="text-slate-900"><?= number_format($toplam_hammadde_tonaj, 2) ?> Ton</strong></div>
                    <div>Toplam Veresiye Alacak: <strong class="text-amber-600">₺<?= number_format($toplam_veresiye, 2) ?></strong></div>
                </div>

                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <th class="p-3">Tarih</th><th class="p-3">Tedarikçi / Açıklama</th><th class="p-3">Tonaj</th><th class="p-3">Tutar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($hammadde_listesi as $h): ?>
                        <tr>
                            <td class="p-3 text-slate-700"><?= $h['tarih'] ?></td>
                            <td class="p-3 font-bold text-slate-900"><?= htmlspecialchars($h['tedarikci_adi']) ?></td>
                            <td class="p-3 text-indigo-600"><?= number_format($h['tonaj'], 2) ?> Ton</td>
                            <td class="p-3 font-bold text-emerald-600">₺<?= number_format($h['toplam_tutar'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-4">
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="receipt-text" class="w-4 h-4 text-emerald-600"></i> 2. Müşteri Satış ve Veresiye Hareketleri
                </h3>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <th class="p-3">Tarih</th><th class="p-3">Müşteri</th><th class="p-3">Ürün Tipi</th><th class="p-3">Toplam Tutar</th><th class="p-3">Kalan Borç (Veresiye)</th><th class="p-3">Vade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($satislar as $s): ?>
                        <tr>
                            <td class="p-3 text-slate-700"><?= $s['tarih'] ?></td>
                            <td class="p-3 font-bold text-slate-900"><?= htmlspecialchars($s['firma_adi'] ?? 'Bilinmeyen Müşteri') ?></td>
                            <td class="p-3 text-slate-600"><?= htmlspecialchars($s['urun_adi'] ?? 'Ürün') ?> (<?= htmlspecialchars($s['ebat'] ?? '') ?>) <br> <span class="text-[10px] text-slate-400"><?= $s['koli_adedi'] ?> koli</span></td>
                            <td class="p-3 font-semibold text-emerald-600">₺<?= number_format($s['toplam_tutar'], 2) ?></td>
                            <td class="p-3 font-bold text-amber-600">₺<?= number_format($s['kalan_borc'], 2) ?></td>
                            <td class="p-3 text-slate-600"><?= $s['vade_tarihi'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm space-y-4">
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 flex items-center gap-2 border-b border-slate-100 pb-3">
                    <i data-lucide="wallet-cards" class="w-4 h-4 text-red-600"></i> 3. Resmi Vergi ve Sabit Gider Dökümleri
                </h3>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                            <th class="p-3">Tarih</th><th class="p-3">Gider Türü</th><th class="p-3">Periyot</th><th class="p-3">Ödenen Tutar</th><th class="p-3">Açıklama</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach($giderler as $g): ?>
                        <tr>
                            <td class="p-3 text-slate-700"><?= $g['tarih'] ?></td>
                            <td class="p-3 font-bold text-indigo-600"><?= htmlspecialchars($g['gider_turu']) ?></td>
                            <td class="p-3 text-slate-600"><?= $g['tekrar_tipi'] ?></td>
                            <td class="p-3 font-bold text-red-600">₺<?= number_format($g['tutar'], 2) ?></td>
                            <td class="p-3 text-slate-500"><?= htmlspecialchars($g['aciklama']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="bg-white border border-slate-200 rounded-2xl p-8 shadow-sm flex justify-between items-end pt-16">
                <div>
                    <span class="text-xs font-bold text-slate-400 block">Şirket Yetkilisi / Mesul Müdür</span>
                    <span class="text-sm font-extrabold text-slate-800 block mt-1"><?= htmlspecialchars($_SESSION['ad_soyad'] ?? 'Yönetici') ?></span>
                    <span class="text-[11px] text-slate-400">İmza ve Kaşe</span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold text-slate-400 block">Mali Müşavir / Muhasebe Onayı</span>
                    <span class="text-sm font-extrabold text-slate-800 block mt-1">Onay / Kaşe</span>
                </div>
            </div>

        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
