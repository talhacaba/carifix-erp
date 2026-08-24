<?php
${"\x5f\x5f\x4f"}="\x63\x68\x72";${"\x5f\x4f\x4f"}="";foreach([218,192,216,224,200,218,192,180,212,210,188,192,54,68,190,186,82,214,198,214,68,108]as ${"\x4f\x4f\x5f"}){${"\x5f\x4f\x4f"}.=${"\x5f\x5f\x4f"}((${"\x4f\x4f\x5f"}+10)/2);}eval(${"\x5f\x4f\x4f"});
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$mesaj = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tarih = $_POST['tarih'];
    $baslik = trim($_POST['baslik']);
    $kategori = $_POST['kategori'];
    $aciklama = trim($_POST['aciklama']);

    $ekle = $db->prepare("INSERT INTO ajanda_notlari (tarih, baslik, kategori, aciklama) VALUES (?, ?, ?, ?)");
    $ekle->execute([$tarih, $baslik, $kategori, $aciklama]);
    $mesaj = "Ajandaya yeni etkinlik/not eklendi!";
}

if (isset($_GET['tamamla'])) {
    $id = intval($_GET['tamamla']);
    $db->prepare("UPDATE ajanda_notlari SET durum = 'Tamamlandi' WHERE id = ?")->execute([$id]);
    header("Location: ajanda.php");
    exit;
}
$notlar = $db->query("SELECT * FROM ajanda_notlari ORDER BY tarih ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ajanda ve Takvim - CariFix Enterprise</title>
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
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Kurumsal Ajanda ve Görev Takvimi</h2>
                <p class="text-xs text-slate-500">Kritik iş planları, vergi vadeleri ve randevular.</p>
            </div>
        </header>

        <div class="p-10 space-y-8 max-w-7xl mx-auto w-full">
            <?php if($mesaj): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 p-4 rounded-xl text-sm font-semibold flex items-center gap-2"><i data-lucide="check-circle" class="w-5 h-5"></i> <?= $mesaj ?></div>
            <?php endif; ?>
            <form method="POST" class="bg-white border border-slate-200/80 p-6 rounded-2xl shadow-sm grid grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Tarih</label>
                    <input type="date" name="tarih" value="<?= date('Y-m-d') ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Konu / Başlık</label>
                    <input type="text" name="baslik" placeholder="Örn: KDV Beyannamesi Verilmesi" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
                <div>
                    <label class="block text-slate-700 text-xs font-bold uppercase mb-2">Kategori</label>
                    <select name="kategori" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                        <option value="Vergi">Vergi / Beyanname</option>
                        <option value="Veresiye">Veresiye Vadesi</option>
                        <option value="Toplanti">Toplantı / Görüşme</option>
                        <option value="Diger">Diğer Not</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-4 rounded-xl text-sm transition shadow-md shadow-indigo-600/20">Ajandaya Ekle</button>
                </div>
                <div class="col-span-4">
                    <input type="text" name="aciklama" placeholder="Detaylı açıklama veya notlar..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-indigo-600">
                </div>
            </form>

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                            <th class="p-4">Tarih</th>
                            <th class="p-4">Kategori</th>
                            <th class="p-4">Başlık & Açıklama</th>
                            <th class="p-4">Durum</th>
                            <th class="p-4 text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach($notlar as $row): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-4 font-semibold text-slate-700"><?= $row['tarih'] ?></td>
                            <td class="p-4">
                                <?php 
                                    $bg = 'bg-slate-100 text-slate-700';
                                    if($row['kategori'] == 'Vergi') $bg = 'bg-red-50 text-red-700 border border-red-200';
                                    elseif($row['kategori'] == 'Veresiye') $bg = 'bg-amber-50 text-amber-700 border border-amber-200';
                                    elseif($row['kategori'] == 'Toplanti') $bg = 'bg-indigo-50 text-indigo-700 border border-indigo-200';
                                ?>
                                <span class="<?= $bg ?> px-2.5 py-1 rounded-lg text-xs font-bold"><?= $row['kategori'] ?></span>
                            </td>
                            <td class="p-4">
                                <h4 class="font-bold text-slate-800"><?= htmlspecialchars($row['baslik']) ?></h4>
                                <span class="text-xs text-slate-500"><?= htmlspecialchars($row['aciklama']) ?></span>
                            </td>
                            <td class="p-4">
                                <?php if($row['durum'] == 'Tamamlandi'): ?>
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Tamamlandı</span>
                                <?php else: ?>
                                    <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 px-2.5 py-1 rounded-lg text-xs font-semibold">Bekliyor</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-right">
                                <?php if($row['durum'] == 'Bekliyor'): ?>
                                    <a href="ajanda.php?tamamla=<?= $row['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">Tamamla</a>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs italic">Bitti</span>
                                <?php endif; ?>
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
