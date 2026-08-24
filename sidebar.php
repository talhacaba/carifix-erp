<?php 
$aktif_sayfa = basename($_SERVER['PHP_SELF']); 
$menuler = [
    'anasayfa.php'   => ['ikon' => 'layout-dashboard', 'baslik' => 'Özet Panel'],
    'uretim.php'     => ['ikon' => 'factory', 'baslik' => 'Üretim Takibi'],
    'makine_bakim.php'=>['ikon' => 'wrench', 'baslik' => 'Makine & Arıza'],
    'hammadde.php'   => ['ikon' => 'package-plus', 'baslik' => 'Hammadde & Fatura'],
    'urunler.php'    => ['ikon' => 'package', 'baslik' => 'Ürün Katalog'],
    'musteriler.php' => ['ikon' => 'users', 'baslik' => 'Müşteriler (Cari)'],
    'satislar.php'   => ['ikon' => 'receipt-text', 'baslik' => 'Satış & Veresiye'],
    'giderler.php'   => ['ikon' => 'wallet-cards', 'baslik' => 'Giderler & Vergi'],
    'personel.php'   => ['ikon' => 'user-cog', 'baslik' => 'Personel & Maaş'],
    'ajanda.php'     => ['ikon' => 'calendar-days', 'baslik' => 'Ajanda & Takvim'],
    'raporlar.php'   => ['ikon' => 'file-text', 'baslik' => 'PDF Raporlar']
];
?>

<aside class="w-72 bg-white border-r border-slate-200 flex flex-col justify-between z-20 shadow-sm no-print">
    <div class="p-6">
        <div class="flex items-center gap-3.5 mb-10 px-2">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-600 flex items-center justify-center text-white shadow-md shadow-indigo-600/20">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-lg tracking-wide text-slate-900 leading-tight">CariFix</h1>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-indigo-600">Enterprise Fabrika</span>
            </div>
        </div>
        
        <nav class="space-y-1.5">
            <?php foreach($menuler as $url => $menu): ?>
                <?php 
                    if($aktif_sayfa == $url) {
                        $class = "bg-indigo-600 text-white rounded-xl font-semibold shadow-md shadow-indigo-600/20";
                    } else {
                        $class = "text-slate-600 hover:text-indigo-600 hover:bg-slate-50 rounded-xl font-medium";
                    }
                ?>
                <a href="<?= $url ?>" class="flex items-center gap-3 py-3 px-4 text-sm transition-all <?= $class ?>">
                    <i data-lucide="<?= $menu['ikon'] ?>" class="w-4 h-4"></i> <?= $menu['baslik'] ?>
                </a>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="p-6 border-t border-slate-100 bg-slate-50/50">
        <a href="cikis.php" class="flex items-center justify-center gap-2 w-full py-2.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-bold transition border border-red-200">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Güvenli Çıkış
        </a>
    </div>
</aside>
