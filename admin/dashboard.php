<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/admin_header.php';

// 获取统计信息
$stats = [
    'total_images' => count(get_data('images')),
    'today_uploads' => count(array_filter(
        get_data('images'),
        fn($img) => date('Y-m-d', $img['upload_time']) == date('Y-m-d')
    )),
    'user_count' => count(get_data('users')),
    'storage_usage' => round(array_sum(array_map(
        fn($img) => @filesize(UPLOAD_DIR . $img['filename']),
        get_data('images')
    )) / 1024 / 1024, 2)
];

// 生成图表数据（示例）
$chart_data = [
    'labels' => ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
    'data' => [12, 19, 3, 5, 2, 3, 15]
];
?>
<!-- 内容区域 -->
<div class="dashboard-content" style="padding:2rem;">
    <!-- 统计卡片 -->
    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(250px,1fr));gap:1.5rem;">
        <?php foreach([
            ['icon'=>'image', 'title'=>'总图片','value'=>$stats['total_images']],
            ['icon'=>'upload','title'=>'今日上传','value'=>$stats['today_uploads']],
            ['icon'=>'users', 'title'=>'用户数', 'value'=>$stats['user_count']],
            ['icon'=>'database','title'=>'存储用量','value'=>$stats['storage_usage'].' MB']
        ] as $stat): ?>
        <div class="stat-card" style="background:#fff;padding:1.5rem;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
            <i class="fas fa-<?=$stat['icon']?>" style="color:var(--admin-primary);font-size:1.8rem;"></i>
            <h3 style="margin:0.5rem 0;"><?=$stat['title']?></h3>
            <p style="font-size:2rem;margin:0;"><?=$stat['value']?></p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 上传趋势图 -->
    <div class="chart-container" style="margin-top:2rem;background:#fff;padding:2rem;border-radius:12px;">
        <h3>上传趋势</h3>
        <canvas id="chart" style="height:400px;"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// 图表初始化
new Chart(document.getElementById('chart'), {
    type: 'line',
    data: {
        labels: <?=json_encode($chart_data['labels'])?>,
        datasets: [{
            label: '每日上传量',
            data: <?=json_encode($chart_data['data'])?>,
            borderColor: '#ff7e9d',
            tension: 0.3
        }]
    }
});
</script>
</main>
</div>
</body>
</html>