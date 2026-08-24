<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }
$toplam_uretim = $db->query("SELECT SUM(bardak_adedi) FROM gunluk_uretim")->fetchColumn() ?: 0;
$toplam_uretim_koli = $db->query("SELECT SUM(koli_adedi) FROM gunluk_uretim")->fetchColumn() ?: 0;
$toplam_satis = $db->query("SELECT SUM(toplam_tutar) FROM satislar")->fetchColumn() ?: 0;
$toplam_veresiye = $db->query("SELECT SUM(kalan_borc) FROM satislar WHERE durum='Acik'")->fetchColumn() ?: 0;
$toplam_gider = $db->query("SELECT SUM(tutar) FROM giderler")->fetchColumn() ?: 0;
$net_kar = $toplam_satis - $toplam_gider;
$toplam_satilan_koli = $db->query("SELECT SUM(koli_adedi) FROM satislar")->fetchColumn() ?: 0;
$toplam_satilan_bardak = $db->query("
    SELECT SUM(s.koli_adedi * COALESCE(u.koli_ici_adet, 3000)) 
    FROM satislar s 
    LEFT JOIN urunler u ON s.urun_id = u.id
")->fetchColumn() ?: 0;

$depo = $db->query("SELECT * FROM depo_stok LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$mevcut_tonaj = $depo['mevcut_tonaj'] ?? 0;
$kritik_seviye = $depo['kritik_seviye'] ?? 1;

$son_satislar = $db->query("
    SELECT s.*, m.firma_adi, u.urun_adi, u.ebat 
    FROM satislar s 
    LEFT JOIN musteriler m ON s.musteri_id = m.id 
    LEFT JOIN urunler u ON s.urun_id = u.id 
    ORDER BY s.id DESC 
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

$son_uretimler = $db->query("SELECT * FROM gunluk_uretim ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>CariFix Enterprise - Fabrika Yönetim Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 flex h-screen overflow-hidden selection:bg-indigo-600 selection:text-white">

   <?php include 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 px-10 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Kurumsal Yönetim Paneli</h2>
                <p class="text-xs text-slate-500">Fabrika genel finansal ve operasyonel performansı.</p>
            </div>
            
            <?php if($mevcut_tonaj <= $kritik_seviye): ?>
                <div class="bg-red-50 border border-red-200 px-4 py-2 rounded-xl text-red-600 text-xs font-bold flex items-center gap-2 animate-pulse">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> KRİTİK STOK: <?= number_format($mevcut_tonaj, 2) ?> Ton!
                </div>
            <?php else: ?>
                <div class="bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-xl text-emerald-700 text-xs font-semibold flex items-center gap-2">
                    <i data-lucide="check-circle2" class="w-4 h-4"></i> Depo Durumu: <?= number_format($mevcut_tonaj, 2) ?> Ton
                </div>
            <?php endif; ?>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">

            <div class="grid grid-cols-3 gap-5">
                
                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Toplam Üretim</span>
                    <h3 class="text-2xl font-black text-slate-900"><?= number_format($toplam_uretim) ?> <span class="text-sm font-semibold text-slate-400">Adet</span></h3>
                    <span class="text-xs text-indigo-600 font-semibold mt-1 block"><i data-lucide="factory" class="w-3.5 h-3.5 inline"></i> <?= number_format($toplam_uretim_koli) ?> Koli Üretildi</span>
                </div>
                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Satılan Miktar (Çıkış)</span>
                    <h3 class="text-2xl font-black text-slate-900"><?= number_format($toplam_satilan_bardak) ?> <span class="text-sm font-semibold text-slate-400">Adet</span></h3>
                    <span class="text-xs text-cyan-600 font-semibold mt-1 block"><i data-lucide="truck" class="w-3.5 h-3.5 inline"></i> <?= number_format($toplam_satilan_koli) ?> Koli Sevk Edildi</span>
                </div>
                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Toplam Ciro</span>
                    <h3 class="text-2xl font-black text-emerald-600">₺<?= number_format($toplam_satis, 2) ?></h3>
                    <span class="text-xs text-emerald-500 font-semibold mt-1 block">Brüt Satış Geliri</span>
                </div>
                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Açık Veresiye</span>
                    <h3 class="text-2xl font-black text-amber-600">₺<?= number_format($toplam_veresiye, 2) ?></h3>
                    <span class="text-xs text-amber-500 font-semibold mt-1 block">Tahsil Edilecek Tutar</span>
                </div>
                <div class="bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Gider & Vergi</span>
                    <h3 class="text-2xl font-black text-red-600">₺<?= number_format($toplam_gider, 2) ?></h3>
                    <span class="text-xs text-red-500 font-semibold mt-1 block">Toplam Masraf</span>
                </div>
                <div class="bg-gradient-to-br from-indigo-600 to-violet-700 text-white p-5 rounded-2xl shadow-md flex flex-col justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-200 block">Net Durum / Kâr</span>
                    <h3 class="text-2xl font-black">₺<?= number_format($net_kar, 2) ?></h3>
                    <span class="text-xs text-indigo-100 font-medium">Ciro - Gider</span>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-6">
                <div class="col-span-2 bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2"><i data-lucide="trending-up" class="w-4 h-4 text-indigo-600"></i> Finansal Akış ve Ciro Analizi</h3>
                    </div>
                    <div class="h-72">
                        <canvas id="finansGrafik"></canvas>
                    </div>
                </div>
                <div class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-4 flex items-center gap-2"><i data-lucide="pie-chart" class="w-4 h-4 text-indigo-600"></i> Genel Özet Oranları</h3>
                    </div>
                    <div class="h-56 flex items-center justify-center">
                        <canvas id="oranGrafik"></canvas>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-4 flex items-center gap-2"><i data-lucide="receipt" class="w-4 h-4 text-indigo-600"></i> Son Satışlar</h3>
                    <div class="space-y-3">
                        <?php if(count($son_satislar) > 0): foreach($son_satislar as $s): ?>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-100 rounded-xl hover:bg-slate-100 transition">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800"><a href="cari_detay.php?id=<?= $s['id'] ?>"><?= htmlspecialchars($s['firma_adi'] ?? 'Bilinmeyen Müşteri') ?></a></h4>
                                <span class="text-[11px] text-slate-500"><?= htmlspecialchars($s['urun_adi'] ?? 'Ürün') ?> <?= htmlspecialchars($s['ebat'] ?? '') ?> (<?= $s['koli_adedi'] ?> Koli)</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-emerald-600">₺<?= number_format($s['toplam_tutar'], 2) ?></span>
                                <span class="text-[10px] text-slate-400 block"><?= $s['tarih'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; else: ?><p class="text-xs text-slate-400 italic">Kayıt yok.</p><?php endif; ?>
                    </div>
                </div>

                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-700 mb-4 flex items-center gap-2"><i data-lucide="boxes" class="w-4 h-4 text-indigo-600"></i> Son Üretimler</h3>
                    <div class="space-y-3">
                        <?php if(count($son_uretimler) > 0): foreach($son_uretimler as $u): ?>
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-100 rounded-xl">
                            <div>
                                <h4 class="text-xs font-bold text-slate-800"><?= number_format($u['bardak_adedi']) ?> Adet Bardak</h4>
                                <span class="text-[11px] text-slate-500"><?= $u['koli_adedi'] ?> Koli Üretildi</span>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-indigo-600">Fire: <?= $u['fire_miktari'] ?></span>
                                <span class="text-[10px] text-slate-400 block"><?= $u['tarih'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; else: ?><p class="text-xs text-slate-400 italic">Kayıt yok.</p><?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
        lucide.createIcons();

        const ctx1 = document.getElementById('finansGrafik').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz'],
                datasets: [{
                    label: 'Aylık Ciro (TL)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, <?= $toplam_satis ?>],
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.05)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: '#f1f5f9' }, ticks: { font: { family: 'Plus Jakarta Sans' } } },
                    x: { grid: { display: false }, ticks: { font: { family: 'Plus Jakarta Sans' } } }
                }
            }
        });

        const ctx2 = document.getElementById('oranGrafik').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Ciro', 'Gider', 'Veresiye'],
                datasets: [{
                    data: [<?= $toplam_satis ?: 1 ?>, <?= $toplam_gider ?: 1 ?>, <?= $toplam_veresiye ?: 1 ?>],
                    backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Plus Jakarta Sans', size: 11 } } }
                }
            }
        });
    </script>
</body>
</html>
