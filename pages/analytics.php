<?php
$user = getCurrentUser();
$links = getUserLinks($user['id']);

// Aggregate data for charts
$all_data = [];
foreach ($links as $link) {
    $data = getCapturedData($link['id']);
    foreach ($data as $row) {
        $all_data[] = $row;
    }
}

// Group by date
$daily_captures = [];
foreach ($all_data as $row) {
    $date = date('Y-m-d', strtotime($row['captured_at']));
    $daily_captures[$date] = ($daily_captures[$date] ?? 0) + 1;
}
ksort($daily_captures);

// Group by data type
$type_distribution = [];
foreach ($all_data as $row) {
    $type = $row['data_type'];
    $type_distribution[$type] = ($type_distribution[$type] ?? 0) + 1;
}

// Group by browser (simplified from UA)
$browser_distribution = [];
foreach ($all_data as $row) {
    $ua = $row['user_agent'];
    $browser = 'Other';
    if (strpos($ua, 'Chrome') !== false) $browser = 'Chrome';
    elseif (strpos($ua, 'Safari') !== false) $browser = 'Safari';
    elseif (strpos($ua, 'Firefox') !== false) $browser = 'Firefox';
    elseif (strpos($ua, 'Edge') !== false) $browser = 'Edge';
    $browser_distribution[$browser] = ($browser_distribution[$browser] ?? 0) + 1;
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8 animate-in fade-in duration-500">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Advanced Analytics</h1>
            <p class="text-slate-500 font-medium">Deep insights into your link performance and visitor data.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="bg-white text-slate-700 px-6 py-3 rounded-2xl font-bold border border-slate-200 shadow-sm hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fas fa-print"></i>
                Export PDF
            </button>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Daily Captures -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-chart-line text-indigo-600"></i>
                Capture Activity (Daily)
            </h3>
            <div class="h-64">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <!-- Data Type Distribution -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-purple-600"></i>
                Data Distribution
            </h3>
            <div class="h-64">
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        <!-- Browser Distribution -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-globe text-blue-600"></i>
                Browser Intelligence
            </h3>
            <div class="h-64">
                <canvas id="browserChart"></canvas>
            </div>
        </div>

        <!-- Recent Captures Table (Mini) -->
        <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fas fa-history text-rose-600"></i>
                Recent Intelligence
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm font-medium">
                    <thead class="text-slate-400 uppercase text-[10px] tracking-widest border-b border-slate-50">
                        <tr>
                            <th class="pb-3">Type</th>
                            <th class="pb-3">IP Address</th>
                            <th class="pb-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php 
                        $recent = array_slice($all_data, 0, 5);
                        foreach ($recent as $row): ?>
                            <tr>
                                <td class="py-4">
                                    <span class="px-2 py-1 bg-slate-50 text-slate-600 rounded-lg font-bold text-[10px] uppercase"><?= htmlspecialchars($row['data_type']) ?></span>
                                </td>
                                <td class="py-4 text-slate-900 font-bold"><?= htmlspecialchars($row['ip_address']) ?></td>
                                <td class="py-4 text-slate-500 text-xs"><?= date('H:i, d M', strtotime($row['captured_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent)): ?>
                            <tr>
                                <td colspan="3" class="py-10 text-center text-slate-400">No data available yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Daily Chart
const dailyCtx = document.getElementById('dailyChart').getContext('2d');
new Chart(dailyCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_keys($daily_captures)) ?>,
        datasets: [{
            label: 'Captures',
            data: <?= json_encode(array_values($daily_captures)) ?>,
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            fill: true,
            tension: 0.4,
            borderWidth: 3,
            pointBackgroundColor: '#6366f1',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});

// Type Chart
const typeCtx = document.getElementById('typeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($type_distribution)) ?>,
        datasets: [{
            data: <?= json_encode(array_values($type_distribution)) ?>,
            backgroundColor: ['#6366f1', '#a855f7', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'],
            borderWidth: 0,
            hoverOffset: 10
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold' } } }
        },
        cutout: '70%'
    }
});

// Browser Chart
const browserCtx = document.getElementById('browserChart').getContext('2d');
new Chart(browserCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($browser_distribution)) ?>,
        datasets: [{
            label: 'Visitors',
            data: <?= json_encode(array_values($browser_distribution)) ?>,
            backgroundColor: '#3b82f6',
            borderRadius: 12,
            barThickness: 30
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { display: false } },
            x: { grid: { display: false } }
        }
    }
});
</script>
