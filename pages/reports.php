<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'archive')) {
    header("Location: login.php");
    exit;
}

// Database Connection
require_once '../includes/db_connect.php';

// 1. Project counts by status
try {
    $status_counts_raw = $pdo->query("SELECT status, COUNT(*) as count FROM projects GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
    $status_counts = [];
    foreach($status_counts_raw as $row) {
        $status_counts[$row['status']] = $row['count'];
    }
} catch (\PDOException $e) {
    $status_counts = [];
}

// 2. Faculty distribution
try {
    $faculty_dist = $pdo->query("SELECT faculty, COUNT(*) as count FROM projects GROUP BY faculty ORDER BY count DESC")->fetchAll();
} catch (\PDOException $e) {
    $faculty_dist = [];
}

// 3. Top Rated Projects (Likes)
try {
    $top_rated = $pdo->query("SELECT p.title, COUNT(e.id) as likes 
                             FROM projects p 
                             LEFT JOIN evaluations e ON p.id = e.project_id AND e.rating_type = 'like'
                             GROUP BY p.id 
                             ORDER BY likes DESC 
                             LIMIT 5")->fetchAll();
} catch (\PDOException $e) {
    $top_rated = [];
}

// 4. Most Commented Projects
try {
    $most_commented = $pdo->query("SELECT p.title, COUNT(c.id) as comments 
                                  FROM projects p 
                                  LEFT JOIN comments c ON p.id = c.project_id 
                                  GROUP BY p.id 
                                  ORDER BY comments DESC 
                                  LIMIT 5")->fetchAll();
} catch (\PDOException $e) {
    $most_commented = [];
}

$approved_count = $status_counts['approved'] ?? 0;
$pending_count = $status_counts['pending'] ?? 0;
$rejected_count = $status_counts['rejected'] ?? 0;

// Prepare data for Chart.js
$faculty_labels = array_column($faculty_dist, 'faculty');
$faculty_data = array_column($faculty_dist, 'count');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير النظام - أرشفة المشاريع</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=1771762380" />
    <link rel="stylesheet" href="../css/admin.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Standard Dashboard Header fixes - removing manual centering */
        .page-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .page-hero h2 { font-size: 1.6rem; margin-bottom: 5px; margin-top: 0; display: flex; align-items: center; gap: 10px; }
        .page-hero p  { opacity: 0.85; font-size: 0.95rem; margin-bottom: 0; }
        .back-link {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s ease;
            white-space: nowrap;
        }
        .back-link:hover { background: rgba(255,255,255,0.35); }

        .report-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-print {
            background: #34495e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.3s;
        }
        .btn-print:hover {
            background: #2c3e50;
        }
        .report-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        .report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
        }
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 15px;
        }
        .rank-list {
            margin-top: 15px;
        }
        .rank-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .rank-item:last-child { border-bottom: none; }
        .count-badge {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .text-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 70%;
        }
        
        /* Print Styles */
        @media print {
            .fixed-header, .fixed-nav, .btn-print, .back-link, .header-wrapper {
                display: none !important;
            }
            body {
                background: white;
                padding: 0;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
            }
            .report-section {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }
            .page-hero {
                background: none !important;
                color: black !important;
                text-align: center;
                border-bottom: 2px solid #eee;
                padding-bottom: 20px;
                margin-top: 0;
                display: block !important;
            }
            .page-hero h2 { color: black !important; justify-content: center; }
            .page-hero p { color: #666 !important; }
            .container { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-wrapper">
             <header class="fixed-header">
                <div class="header-content">
                    <div class="logo-container">
                        <div class="logo-img">
                            <img src="../img/1765888818874.jpg" alt="شعار الجامعة">
                        </div>
                        <div class="logo-text">
                            <div class="university-name">الجامعة الإماراتية الدولية</div>
                            <h1>نظام أرشفة المشاريع الجامعية</h1>
                        </div>
                    </div>
                    <p><?php echo ($_SESSION['role'] === 'archive') ? 'لوحة تحكم الأرشيف' : 'لوحة تحكم المشرفين'; ?> - التقارير والإحصائيات</p>
                </div>
            </header>
        </div>

        <div class="main-content">
            <!-- Page Hero with Back Link -->
            <div class="page-hero">
                <div>
                    <h2><i class="fas fa-file-invoice"></i> إحصائيات وتقارير النظام</h2>
                    <p>نظرة شاملة ومرئية لأداء المنصة وتوزيع المشاريع</p>
                </div>
                <a href="<?php echo ($_SESSION['role'] === 'archive') ? 'archive_dashboard.php' : 'admin_dashboard.php'; ?>" class="back-link">
                    <i class="fas fa-arrow-right"></i> عودة
                </a>
            </div>
            
            <!-- Overall Stats Details & Print Box -->
             <div class="report-section" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; background: #f8f9fc; border: 1px solid #e3e6f0;">
                <div style="flex: 1; min-width: 250px;">
                    <h3 style="margin-top: 0; color: #333;"><i class="fas fa-chart-line"></i> ملخص الإحصائيات العامة</h3>
                    <p style="color: #666; margin-bottom: 0;">إجمالي المشاريع المؤرشفة في النظام: <strong><?php echo array_sum($status_counts); ?></strong> مشروع.</p>
                </div>
                <div style="margin-top: 15px;">
                    <button class="btn-print" onclick="window.print()" style="background: var(--primary); padding: 12px 25px; border-radius: 8px; font-weight: 600;">
                        <i class="fas fa-print"></i> طباعة التقرير الرسمي
                    </button>
                </div>
            </div>

            <div class="report-grid">
                <!-- Status Distribution Chart -->
                <div class="report-section">
                    <h3><i class="fas fa-chart-pie"></i> حالة المشاريع الواردة</h3>
                    <div class="chart-container">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>

                <!-- Faculty Distribution Chart -->
                <div class="report-section">
                    <h3><i class="fas fa-chart-bar"></i> التوزيع الأكاديمي (حسب الكلية)</h3>
                    <div class="chart-container">
                        <canvas id="facultyChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="report-grid">
                <!-- Top Rated -->
                <div class="report-section">
                    <h3><i class="fas fa-thumbs-up"></i> المشاريع الأكثر تفاعلاً (إعجاب)</h3>
                    <div class="rank-list">
                        <?php if(empty($top_rated)): ?>
                            <p style="text-align:center; color:#777; padding: 20px;">لا توجد بيانات حالياً</p>
                        <?php else: ?>
                            <?php foreach ($top_rated as $row): ?>
                                <div class="rank-item">
                                    <span class="text-ellipsis"><?php echo htmlspecialchars($row['title']); ?></span>
                                    <span class="count-badge"><i class="fas fa-heart"></i> <?php echo $row['likes']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Most Commented -->
                <div class="report-section">
                    <h3><i class="fas fa-comments"></i> المشاريع الأكثر نقاشاً (تعليقات)</h3>
                    <div class="rank-list">
                        <?php if(empty($most_commented)): ?>
                            <p style="text-align:center; color:#777; padding: 20px;">لا توجد بيانات حالياً</p>
                        <?php else: ?>
                            <?php foreach ($most_commented as $row): ?>
                                <div class="rank-item">
                                    <span class="text-ellipsis"><?php echo htmlspecialchars($row['title']); ?></span>
                                    <span class="count-badge" style="background: #3498db;"><i class="fas fa-comment-dots"></i> <?php echo $row['comments']; ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
             <!-- Status Distribution List -->
             <div class="report-section">
                <h3><i class="fas fa-table"></i> ملخص حالة المشاريع</h3>
                <div class="rank-list" style="display: flex; gap: 20px; flex-wrap: wrap;">
                    <div class="rank-item" style="flex: 1; min-width: 200px; background: #f8f9fc; border-radius: 8px;">
                        <span>معتمدة (المكتبة)</span> 
                        <span class="count-badge" style="background: #2ecc71;"><?php echo $approved_count; ?></span>
                    </div>
                    <div class="rank-item" style="flex: 1; min-width: 200px; background: #f8f9fc; border-radius: 8px;">
                        <span>بانتظار المراجعة</span> 
                        <span class="count-badge" style="background: #f39c12;"><?php echo $pending_count; ?></span>
                    </div>
                    <div class="rank-item" style="flex: 1; min-width: 200px; background: #f8f9fc; border-radius: 8px;">
                        <span>مرفوضة / معادة</span> 
                        <span class="count-badge" style="background: #e74c3c;"><?php echo $rejected_count; ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Chart.js default font config for better Arabic support
        Chart.defaults.font.family = "'Cairo', sans-serif";
        Chart.defaults.font.size = 14;

        // --- Status Pie Chart ---
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['معتمدة', 'قيد المراجعة', 'مرفوضة'],
                datasets: [{
                    data: [<?php echo $approved_count; ?>, <?php echo $pending_count; ?>, <?php echo $rejected_count; ?>],
                    backgroundColor: [
                        '#2ecc71', // Green for approved
                        '#f39c12', // Orange for pending
                        '#e74c3c'  // Red for rejected
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { padding: 20 }
                    }
                }
            }
        });

        // --- Faculty Bar Chart ---
        const ctxFaculty = document.getElementById('facultyChart').getContext('2d');
        const facultyLabels = <?php echo json_encode($faculty_labels); ?>;
        const facultyData = <?php echo json_encode($faculty_data); ?>;
        
        // Generate soft colors based on primary brand color
        const chartColors = facultyData.map((_, i) => `hsl(210, 60%, ${40 + (i * 10)}%)`);

        const facultyChart = new Chart(ctxFaculty, {
            type: 'bar',
            data: {
                labels: facultyLabels.length > 0 ? facultyLabels : ['لا توجد بيانات'],
                datasets: [{
                    label: 'عدد المشاريع',
                    data: facultyData.length > 0 ? facultyData : [0],
                    backgroundColor: facultyLabels.length > 0 ? chartColors : ['#ccc'],
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>
