<div class="card">
    <div class="card-header">
        <h2 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            Suivi du poids
        </h2>
        <button class="btn btn-info btn-sm" onclick="showModal('addWeightModal')">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Ajouter
        </button>
    </div>

    <?php if (empty($weightRecords)): ?>
        <div class="empty-inline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/>
                <line x1="6" y1="20" x2="6" y2="14"/>
            </svg>
            <span>Aucune pesée enregistrée</span>
            <button class="btn btn-ghost btn-sm" onclick="showModal('addWeightModal')">Enregistrer le premier poids</button>
        </div>
    <?php else: ?>
        <?php
            $weightLabels = [];
            $weightData   = [];
            foreach ($weightRecords as $r) {
                $weightLabels[] = date('d/m', strtotime($r['date']));
                $weightData[]   = (float)$r['weight'];
            }
        ?>

        <div class="weight-chart-wrapper">
            <canvas id="weight-chart"></canvas>
        </div>

        <div style="margin-top: 16px; display: flex; flex-direction: column; gap: 8px;">
            <?php foreach (array_reverse($weightRecords) as $record): ?>
                <div class="item blue">
                    <div class="item-dot"></div>
                    <div class="item-body">
                        <div class="item-title"><?= $record['weight'] ?> kg</div>
                        <div class="item-text"><?= date('d/m/Y', strtotime($record['date'])) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
        <script>
        (function() {
            const isDark = document.documentElement.dataset.theme === 'dark' ||
                (document.documentElement.dataset.theme !== 'light' &&
                 window.matchMedia('(prefers-color-scheme: dark)').matches);

            const gridColor  = isDark ? 'rgba(240,238,255,0.08)' : 'rgba(26,22,37,0.06)';
            const labelColor = isDark ? '#9e9cb4' : '#7c7a8e';

            new Chart(document.getElementById('weight-chart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($weightLabels) ?>,
                    datasets: [{
                        data: <?= json_encode($weightData) ?>,
                        borderColor: '#6c5ce7',
                        borderWidth: 2.5,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: '#6c5ce7',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        fill: true,
                        backgroundColor: function(context) {
                            const chart = context.chart;
                            const { ctx, chartArea } = chart;
                            if (!chartArea) return 'rgba(108,92,231,0.08)';
                            const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                            gradient.addColorStop(0, 'rgba(108,92,231,0.18)');
                            gradient.addColorStop(1, 'rgba(108,92,231,0.00)');
                            return gradient;
                        }
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: isDark ? '#1e1b2e' : '#ffffff',
                            titleColor: isDark ? '#f0eeff' : '#1a1625',
                            bodyColor: isDark ? '#9e9cb4' : '#7c7a8e',
                            borderColor: isDark ? '#2d2a3e' : '#e8e6f0',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: ctx => ' ' + ctx.parsed.y + ' kg'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: gridColor },
                            ticks: { color: labelColor, font: { size: 12 } }
                        },
                        y: {
                            beginAtZero: false,
                            grid: { color: gridColor },
                            ticks: {
                                color: labelColor,
                                font: { size: 12 },
                                callback: function(v) { return v + ' kg'; }
                            }
                        }
                    }
                }
            });
        })();
        </script>
    <?php endif; ?>
</div>
