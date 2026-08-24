<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bakim_ekle'])) {
    $makine_adi = trim($_POST['makine_adi']);
    $islem_tarihi = $_POST['islem_tarihi'];
    $islem_tipi = $_POST['islem_tipi'];
    $aciklama = trim($_POST['aciklama']);
    $degisen_parcalar = trim($_POST['degisen_parcalar']);
    $maliyet = floatval($_POST['maliyet']);
    $durum = $_POST['durum'];
    $sonraki_bakim_tarihi = !empty($_POST['sonraki_bakim_tarihi']) ? $_POST['sonraki_bakim_tarihi'] : NULL;
    $ekle = $db->prepare("INSERT INTO makine_bakim (makine_adi, islem_tarihi, islem_tipi, aciklama, degisen_parcalar, maliyet, durum, sonraki_bakim_tarihi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $ekle->execute([$makine_adi, $islem_tarihi, $islem_tipi, $aciklama, $degisen_parcalar, $maliyet, $durum, $sonraki_bakim_tarihi]);

    if ($maliyet > 0 && $durum == 'Tamamlandi') {
        $gider_ekle = $db->prepare("INSERT INTO giderler (gider_turu, tutar, tarih, aciklama) VALUES (?, ?, ?, ?)");
        $gider_ekle->execute(['Makine Bakım/Onarım', $maliyet, $islem_tarihi, $makine_adi . ' - ' . $islem_tipi]);
    }

    header("Location: makine_bakim.php?durum=basarili");
    exit;
}

$mesaj = (isset($_GET['durum']) && $_GET['durum'] == 'basarili') ? "Makine servis/arıza kaydı başarıyla eklendi!" : '';
$liste = $db->query("SELECT * FROM makine_bakim ORDER BY islem_tarihi DESC, id DESC")->fetchAll(PDO::FETCH_ASSOC);
$toplam_masraf = $db->query("SELECT SUM(maliyet) FROM makine_bakim")->fetchColumn() ?: 0;
$devam_edenler = $db->query("SELECT COUNT(*) FROM makine_bakim WHERE durum = 'Devam_Ediyor'")->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Makine & Arıza Yönetimi - CariFix Enterprise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 flex h-screen overflow-hidden">
    
    <?php include 'sidebar.php'; ?>

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-slate-200 px-10 flex items-center justify-between sticky top-0 z-10">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Makine Bakım ve Arıza Kayıtları</h2>
                <p class="text-xs text-slate-500">Üretim parkuru teknik servis, yedek parça ve onarım takibi.</p>
            </div>
            <div class="flex gap-4">
                <div class="bg-amber-50 border border-amber-200 px-4 py-2 rounded-xl text-amber-700 text-xs font-bold flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> Aktif Arızalar: <?= $devam_edenler ?>
                </div>
                <div class="bg-red-50 border border-red-200 px-4 py-2 rounded-xl text-red-700 text-xs font-bold flex items-center gap-2">
                    <i data-lucide="tool" class="w-4 h-4"></i> Toplam Maliyet: ₺<?= number_format($toplam_masraf, 2) ?>
                </div>
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div>
            <?php endif; ?>
            <form method="POST" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-4 gap-4 items-end">
                <input type="hidden" name="bakim_ekle" value="1">
                
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Makine Adı / Kodu</label>
                    <input type="text" name="makine_adi" placeholder="Örn: Şekillendirme Makinesi 1" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">İşlem Tarihi</label>
                    <input type="date" name="islem_tarihi" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Kayıt Tipi</label>
                    <select name="islem_tipi" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="Ariza">Acil Arıza / Duruş</option>
                        <option value="Periyodik_Bakim">Periyodik Bakım (Yağ/Filtre)</option>
                        <option value="Revizyon">Revizyon / Parça İyileştirme</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Güncel Durum</label>
                    <select name="durum" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="Tamamlandi">Tamamlandı (Aktif Çalışıyor)</option>
                        <option value="Devam_Ediyor">Devam Ediyor (Makine Duruyor!)</option>
                        <option value="Parca_Bekliyor">Yedek Parça Bekliyor</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Değişen Parçalar / Yapılan İşlem</label>
                    <input type="text" name="degisen_parcalar" placeholder="Örn: 2 adet sensör, 1 kayış değişti..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Maliyet (TL)</label>
                    <input type="number" step="0.01" name="maliyet" placeholder="0.00" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Sonraki Bakım Hedef Tarihi</label>
                    <input type="date" name="sonraki_bakim_tarihi" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>

                <div class="col-span-3">
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Detaylı Arıza Şikayeti / Açıklama</label>
                    <input type="text" name="aciklama" placeholder="Makineden gelen ses, yaşanan sorun veya usta notları..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md shadow-indigo-600/20 flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Kaydı Sisteme İşle
                    </button>
                </div>
            </form>
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tarih</th>
                            <th class="p-4">Makine</th>
                            <th class="p-4">Kayıt Tipi</th>
                            <th class="p-4">Yapılan İşlem / Değişen</th>
                            <th class="p-4">Maliyet</th>
                            <th class="p-4">Durum</th>
                            <th class="p-4">Sonraki Bakım</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($liste as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 text-slate-600 font-medium"><?= date('d.m.Y', strtotime($row['islem_tarihi'])) ?></td>
                            <td class="p-4 font-extrabold text-slate-800"><?= htmlspecialchars($row['makine_adi']) ?></td>
                            <td class="p-4">
                                <?php if($row['islem_tipi'] == 'Ariza'): ?>
                                    <span class="text-red-600 bg-red-50 px-2 py-1 rounded-md text-xs font-bold flex inline-flex items-center gap-1"><i data-lucide="zap" class="w-3 h-3"></i> Arıza</span>
                                <?php else: ?>
                                    <span class="text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md text-xs font-bold flex inline-flex items-center gap-1"><i data-lucide="settings" class="w-3 h-3"></i> Bakım</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <span class="text-slate-700 block"><?= htmlspecialchars($row['degisen_parcalar']) ?></span>
                                <span class="text-[11px] text-slate-400 block truncate max-w-xs"><?= htmlspecialchars($row['aciklama']) ?></span>
                            </td>
                            <td class="p-4 font-bold text-slate-900">₺<?= number_format($row['maliyet'], 2) ?></td>
                            <td class="p-4">
                                <?php if($row['durum'] == 'Tamamlandi'): ?>
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Aktif (Tamam)</span>
                                <?php elseif($row['durum'] == 'Parca_Bekliyor'): ?>
                                    <span class="bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Parça Bekliyor</span>
                                <?php else: ?>
                                    <span class="bg-red-50 text-red-700 border border-red-200 px-2.5 py-1 rounded-lg text-xs font-semibold animate-pulse">Makine Duruyor!</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-slate-500 font-medium text-xs">
                                <?php 
                                    if($row['sonraki_bakim_tarihi']) {
                                        echo date('d.m.Y', strtotime($row['sonraki_bakim_tarihi']));
                                    } else {
                                        echo "-";
                                    }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <script>lucide.createIcons();</script>
</body>
</html>
