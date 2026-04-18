<?php
session_start();
require_once '../includes/db_connect.php';

// Admin-only access (تم إلغاء التحقق بناءً على طلبك للدخول المباشر)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit;
// }
$full_name = $_SESSION['full_name'] ?? 'مدير النظام';


// --- Handle Actions ---
$action_msg = '';
$action_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? '';
    $target_id = (int)($_POST['user_id'] ?? 0);

    if ($target_id > 0) {
        try {
            if ($action === 'approve') {
                $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'student'");
                $stmt->execute([$target_id]);
                $action_msg  = 'تم قبول الطالب بنجاح.';
                $action_type = 'success';
            } elseif ($action === 'reject') {
                $stmt = $pdo->prepare("UPDATE users SET status = 'rejected' WHERE id = ? AND role = 'student'");
                $stmt->execute([$target_id]);
                $action_msg  = 'تم رفض الطالب بنجاح.';
                $action_type = 'warning';
            } elseif ($action === 'delete') {
                // Delete projects first (FK constraint)
                $stmt = $pdo->prepare("DELETE FROM projects WHERE student_id = ?");
                $stmt->execute([$target_id]);
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'student'");
                $stmt->execute([$target_id]);
                $action_msg  = 'تم حذف الطالب وجميع مشاريعه بنجاح.';
                $action_type = 'danger';
            }
        } catch (PDOException $e) {
            $action_msg  = 'حدث خطأ: ' . $e->getMessage();
            $action_type = 'danger';
        }
    }
}

// --- Filtering ---
$search      = trim($_GET['search']   ?? '');
$status_filter = $_GET['status']      ?? 'all';
$faculty_filter = $_GET['faculty']    ?? 'all';

// Build query
$where   = ["u.role = 'student'"];
$params  = [];

if ($search !== '') {
    $where[]  = "(u.full_name LIKE ? OR u.email LIKE ? OR u.username LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($status_filter !== 'all') {
    $where[]  = "u.status = ?";
    $params[] = $status_filter;
}
if ($faculty_filter !== 'all') {
    $where[]  = "u.faculty = ?";
    $params[] = $faculty_filter;
}

$where_clause = implode(' AND ', $where);

// Stats
$students         = [];
$faculties        = [];

try {
    // Summary stats calculation removed per user request
    
    // All faculties for filter
    $stmt = $pdo->query("SELECT DISTINCT faculty FROM users WHERE role='student' AND faculty IS NOT NULL AND faculty != '' ORDER BY faculty");
    $faculties = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Students with project counts
    $sql = "
        SELECT u.*,
               COUNT(p.id) as project_count,
               SUM(CASE WHEN p.status='approved' THEN 1 ELSE 0 END) as approved_count,
               SUM(CASE WHEN p.status='pending'  THEN 1 ELSE 0 END) as pending_proj_count,
               SUM(CASE WHEN p.status='rejected' THEN 1 ELSE 0 END) as rejected_count
        FROM users u
        LEFT JOIN projects p ON u.id = p.student_id
        WHERE $where_clause
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If AJAX request, return only the dynamic parts
    if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
        ob_start();
        ?>
        <!-- AJAX stats area removed -->

        <div id="ajax-table-container">
            <div class="students-table-wrap">
                <div class="table-header" style="flex-wrap: wrap; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h3><i class="fas fa-list"></i> قائمة الطلاب</h3>
                        <span class="result-count"><?php echo count($students); ?> نتيجة</span>
                    </div>
                    <a href="admin_create_user.php" class="btn btn-primary" style="padding: 10px 20px;">
                        <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                    </a>
                </div>

                <?php if (empty($students)): ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>لا يوجد طلاب مطابقون للبحث الحالي.</p>
                </div>
                <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>البريد الإلكتروني</th>
                                <th>الكلية / القسم</th>
                                <th>المشاريع</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $i => $student): ?>
                            <tr>
                                <td style="color: var(--gray); font-size: 0.82rem;"><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar-sm">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <div class="student-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                            <div class="student-username">@<?php echo htmlspecialchars($student['username']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: var(--gray);"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($student['faculty'] ?? '—'); ?></div>
                                    <small style="color: var(--gray);"><?php echo htmlspecialchars($student['department'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php if ($student['project_count'] == 0): ?>
                                        <span class="proj-dot none">لا يوجد مشاريع</span>
                                    <?php else: ?>
                                    <div class="proj-dots">
                                        <?php if ($student['approved_count'] > 0): ?>
                                            <span class="proj-dot approved"><?php echo $student['approved_count']; ?> معتمد</span>
                                        <?php endif; ?>
                                        <?php if ($student['pending_proj_count'] > 0): ?>
                                            <span class="proj-dot pending"><?php echo $student['pending_proj_count']; ?> بانتظار</span>
                                        <?php endif; ?>
                                        <?php if ($student['rejected_count'] > 0): ?>
                                            <span class="proj-dot rejected"><?php echo $student['rejected_count']; ?> مرفوض</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $st = $student['status'] ?? 'pending';
                                    $st_labels = ['active' => 'نشط', 'pending' => 'بانتظار الموافقة', 'rejected' => 'مرفوض'];
                                    $st_label  = $st_labels[$st] ?? $st;
                                    ?>
                                    <span class="status-badge <?php echo htmlspecialchars($st); ?>"><?php echo $st_label; ?></span>
                                </td>
                                <td style="color: var(--gray); white-space: nowrap;">
                                    <?php echo date('Y-m-d', strtotime($student['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <?php if ($st === 'pending' || $st === 'rejected'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action"  value="approve">
                                            <button type="submit" class="act-btn approve" title="قبول">
                                                <i class="fas fa-check"></i> قبول
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($st === 'active' || $st === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action"  value="reject">
                                            <button type="submit" class="act-btn reject" title="رفض">
                                                <i class="fas fa-ban"></i> رفض
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;"
                                              onsubmit="return confirm('تحذير: سيتم حذف الطالب وجميع مشاريعه نهائياً. هل أنت متأكد؟');">
                                            <input type="hidden" name="user_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action"  value="delete">
                                            <button type="submit" class="act-btn delete" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        echo ob_get_clean();
        exit;
    }

} catch (PDOException $e) {
    $action_msg  = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
    $action_type = 'danger';
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>إدارة الطلاب - لوحة تحكم المشرف</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=20240301-v3" />
    <link rel="stylesheet" href="../css/admin.css?v=20240303-v1" />
    <style>
        /* ===== Page Specific Styles ===== */
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
        }
        .page-hero h2 { font-size: 1.6rem; margin-bottom: 5px; }
        .page-hero p  { opacity: 0.85; font-size: 0.95rem; }
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

        /* Stats Row */
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }
        .summary-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--box-shadow);
            border-top: 4px solid var(--primary);
        }
        .summary-card.green  { border-top-color: var(--success); }
        .summary-card.yellow { border-top-color: var(--warning); }
        .summary-card.red    { border-top-color: var(--danger);  }
        .summary-card .num { font-size: 2rem; font-weight: 700; color: var(--primary); }
        .summary-card.green  .num { color: var(--success); }
        .summary-card.yellow .num { color: var(--warning); }
        .summary-card.red    .num { color: var(--danger);  }
        .summary-card p { font-size: 0.85rem; color: var(--gray); margin-top: 5px; }

        /* Filter bar */
        .filter-bar {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px 25px;
            box-shadow: var(--box-shadow);
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 160px; }
        .filter-group label { font-size: 0.82rem; color: var(--gray); font-weight: 600; }
        .filter-group input,
        .filter-group select {
            padding: 9px 14px;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: border-color 0.3s;
        }
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary);
        }
        .filter-btn {
            padding: 10px 22px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 7px;
            transition: var(--transition);
            align-self: flex-end;
        }
        .filter-btn-primary { background: var(--primary); color: white; }
        .filter-btn-primary:hover { background: var(--primary-light); }
        .filter-btn-reset { background: #f0f0f0; color: var(--gray); }
        .filter-btn-reset:hover { background: #e0e0e0; }

        /* Table */
        .students-table-wrap {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
        }
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 25px;
            border-bottom: 1px solid #eee;
        }
        .table-header h3 { color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .result-count { font-size: 0.85rem; color: var(--gray); }
        .students-table { width: 100%; border-collapse: collapse; }
        .students-table thead tr { background: #f8f9fa; }
        .students-table th {
            padding: 12px 15px;
            text-align: right;
            font-size: 0.82rem;
            color: var(--gray);
            font-weight: 600;
            border-bottom: 2px solid #eee;
            white-space: nowrap;
        }
        .students-table td {
            padding: 14px 15px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.88rem;
            vertical-align: middle;
        }
        .students-table tr:last-child td { border-bottom: none; }
        .students-table tr:hover td { background: #fafbfc; }

        /* Student name cell */
        .student-cell { display: flex; align-items: center; gap: 12px; }
        .student-avatar-sm {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.9rem; flex-shrink: 0;
        }
        .student-name { font-weight: 600; color: var(--dark); font-size: 0.9rem; }
        .student-username { font-size: 0.78rem; color: var(--gray); }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.78rem;
            font-weight: 600;
        }
        .status-badge.active   { background: rgba(39,174,96,0.12);  color: var(--success); }
        .status-badge.pending  { background: rgba(243,156,18,0.12); color: var(--warning); }
        .status-badge.rejected { background: rgba(231,76,60,0.12);  color: var(--danger);  }

        /* Project counts */
        .proj-dots { display: flex; gap: 6px; flex-wrap: wrap; }
        .proj-dot {
            font-size: 0.75rem; padding: 3px 9px; border-radius: 12px; font-weight: 600;
        }
        .proj-dot.approved { background: rgba(39,174,96,0.1);  color: var(--success); }
        .proj-dot.pending  { background: rgba(243,156,18,0.1); color: var(--warning); }
        .proj-dot.rejected { background: rgba(231,76,60,0.1);  color: var(--danger);  }
        .proj-dot.none     { background: #f0f0f0; color: #888; }

        /* Action buttons */
        .actions-cell { display: flex; gap: 6px; flex-wrap: wrap; }
        .act-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 0.78rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
            text-decoration: none;
        }
        .act-btn.approve { background: rgba(39,174,96,0.12);  color: var(--success); }
        .act-btn.approve:hover { background: var(--success); color: white; }
        .act-btn.reject  { background: rgba(243,156,18,0.12); color: var(--warning); }
        .act-btn.reject:hover  { background: var(--warning); color: white; }
        .act-btn.delete  { background: rgba(231,76,60,0.12);  color: var(--danger); }
        .act-btn.delete:hover  { background: var(--danger); color: white; }

        /* Flash message */
        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            animation: fadeIn 0.4s ease;
        }
        .flash-msg.success { background: rgba(39,174,96,0.12);  color: var(--success); border-right: 4px solid var(--success); }
        .flash-msg.warning { background: rgba(243,156,18,0.12); color: var(--warning); border-right: 4px solid var(--warning); }
        .flash-msg.danger  { background: rgba(231,76,60,0.12);  color: var(--danger);  border-right: 4px solid var(--danger);  }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--gray);
        }
        .empty-state i { font-size: 3rem; margin-bottom: 15px; opacity: 0.4; }
        .empty-state p { font-size: 1rem; }

        /* Mobile */
        @media (max-width: 768px) {
            .page-hero { flex-direction: column; align-items: flex-start; }
            .students-table th:nth-child(3),
            .students-table td:nth-child(3),
            .students-table th:nth-child(4),
            .students-table td:nth-child(4) { display: none; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header-wrapper">
        <header class="fixed-header">
            <div class="header-content">
                <div class="logo-container">
                    <div class="logo-img">
                        <img src="../img/1765888818874.jpg" alt="شعار الجامعة الإماراتية الدولية" />
                    </div>
                    <div class="logo-text">
                        <div class="university-name">الجامعة الإماراتية الدولية</div>
                        <h1>نظام أرشفة المشاريع الجامعية</h1>
                    </div>
                </div>
                <p>إدارة الطلاب - لوحة تحكم المشرف</p>
            </div>
        </header>
        <!-- تمت إزالة الـ Navigation (النافجيشن) من هنا بناءً على طلبك -->
    </div>

    <div class="container">

        <!-- Page Hero -->
        <div class="page-hero">
            <div>
                <h2><i class="fas fa-user-graduate"></i> إدارة الطلاب</h2>
                <p>عرض وإدارة جميع الطلاب المسجلين في النظام – قبول، رفض، وحذف</p>
            </div>
            <a href="admin_dashboard.php" class="back-link">
                <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
            </a>
        </div>

        <!-- Flash Message -->
        <?php if ($action_msg): ?>
        <div class="flash-msg <?php echo htmlspecialchars($action_type); ?>">
            <i class="fas fa-<?php echo $action_type === 'success' ? 'check-circle' : ($action_type === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
            <?php echo htmlspecialchars($action_msg); ?>
        </div>
        <?php endif; ?>

        <!-- Summary Stats Area Removed -->

        <!-- Filter Bar -->
        <form method="GET" action="">
            <div class="filter-bar">
                <div class="filter-group">
                    <label><i class="fas fa-search"></i> بحث</label>
                    <input type="text" name="search" placeholder="اسم الطالب أو البريد أو المعرف..."
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-filter"></i> الحالة</label>
                    <select name="status">
                        <option value="all"     <?php echo $status_filter === 'all'      ? 'selected' : ''; ?>>جميع الحالات</option>
                        <option value="approved"  <?php echo $status_filter === 'approved'   ? 'selected' : ''; ?>>نشط</option>
                        <option value="pending" <?php echo $status_filter === 'pending'  ? 'selected' : ''; ?>>بانتظار الموافقة</option>
                        <option value="rejected"<?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>مرفوض</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-university"></i> الكلية</label>
                    <select name="faculty">
                        <option value="all">جميع الكليات</option>
                        <?php foreach ($faculties as $fac): ?>
                        <option value="<?php echo htmlspecialchars($fac); ?>"
                            <?php echo $faculty_filter === $fac ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($fac); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="filter-btn filter-btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
                <a href="manage_students.php" class="filter-btn filter-btn-reset">
                    <i class="fas fa-undo"></i> إعادة تعيين
                </a>
            </div>
        </form>

        <!-- Students Table Wrapper -->
        <div id="table-area">
            <!-- Students Table -->
            <div class="students-table-wrap">
                <div class="table-header" style="flex-wrap: wrap; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <h3><i class="fas fa-list"></i> قائمة الطلاب</h3>
                        <span class="result-count"><?php echo count($students); ?> نتيجة</span>
                    </div>
                    <a href="admin_create_user.php" class="btn btn-primary" style="padding: 10px 20px;">
                        <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                    </a>
                </div>

                <?php if (empty($students)): ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <p>لا يوجد طلاب مطابقون للبحث الحالي.</p>
                </div>
                <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="students-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>الطالب</th>
                                <th>البريد الإلكتروني</th>
                                <th>الكلية / القسم</th>
                                <th>المشاريع</th>
                                <th>الحالة</th>
                                <th>تاريخ التسجيل</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $i => $student): ?>
                            <tr>
                                <td style="color: var(--gray); font-size: 0.82rem;"><?php echo $i + 1; ?></td>
                                <td>
                                    <div class="student-cell">
                                        <div class="student-avatar-sm">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <div class="student-name"><?php echo htmlspecialchars($student['full_name']); ?></div>
                                            <div class="student-username">@<?php echo htmlspecialchars($student['username']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: var(--gray);"><?php echo htmlspecialchars($student['email']); ?></td>
                                <td>
                                    <div><?php echo htmlspecialchars($student['faculty'] ?? '—'); ?></div>
                                    <small style="color: var(--gray);"><?php echo htmlspecialchars($student['department'] ?? ''); ?></small>
                                </td>
                                <td>
                                    <?php if ($student['project_count'] == 0): ?>
                                        <span class="proj-dot none">لا يوجد مشاريع</span>
                                    <?php else: ?>
                                    <div class="proj-dots">
                                        <?php if ($student['approved_count'] > 0): ?>
                                            <span class="proj-dot approved"><?php echo $student['approved_count']; ?> معتمد</span>
                                        <?php endif; ?>
                                        <?php if ($student['pending_proj_count'] > 0): ?>
                                            <span class="proj-dot pending"><?php echo $student['pending_proj_count']; ?> بانتظار</span>
                                        <?php endif; ?>
                                        <?php if ($student['rejected_count'] > 0): ?>
                                            <span class="proj-dot rejected"><?php echo $student['rejected_count']; ?> مرفوض</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $st = $student['status'] ?? 'pending';
                                    $st_labels = ['approved' => 'نشط', 'active' => 'نشط', 'pending' => 'بانتظار الموافقة', 'rejected' => 'مرفوض'];
                                    $st_label  = $st_labels[$st] ?? $st;
                                    ?>
                                    <span class="status-badge <?php echo htmlspecialchars($st); ?>"><?php echo $st_label; ?></span>
                                </td>
                                <td style="color: var(--gray); white-space: nowrap;">
                                    <?php echo date('Y-m-d', strtotime($student['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="actions-cell">
                                        <?php if ($st === 'pending' || $st === 'rejected'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action"  value="approve">
                                            <button type="submit" class="act-btn approve" title="قبول">
                                                <i class="fas fa-check"></i> قبول
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($st === 'active' || $st === 'approved' || $st === 'pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action"  value="reject">
                                            <button type="submit" class="act-btn reject" title="رفض">
                                                <i class="fas fa-ban"></i> رفض
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display:inline;"
                                              onsubmit="return confirm('تحذير: سيتم حذف الطالب وجميع مشاريعه نهائياً. هل أنت متأكد؟');">
                                            <input type="hidden" name="user_id" value="<?php echo $student['id']; ?>">
                                            <input type="hidden" name="action"  value="delete">
                                            <button type="submit" class="act-btn delete" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    $footer_about_title = "إدارة الطلاب";
    $footer_about_text  = "نظام إدارة مشاريع التخرج للمشرفين والطلاب";
    $footer_about_desc  = "";
    include '../includes/footer.php';
    ?>

    </div><!-- /container -->

    

    <!-- Back to top -->
    <div class="back-to-top"><i class="fas fa-arrow-up"></i></div>

    <script src="../js/script.js?v=20240301-v3"></script>
    <script>
        // Auto-hide flash message after 4 seconds
        const flash = document.querySelector('.flash-msg');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.5s ease';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }, 4000);
        }

        // AJAX Search & Filter
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.querySelector('.filter-bar')?.parentElement;
            const tableArea = document.getElementById('table-area');
            
            if (!filterForm || !tableArea) return;

            function updateContent() {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData);
                params.set('ajax', '1');

                // Update URL for bookmarking
                const cleanParams = new URLSearchParams(formData);
                const newUrl = window.location.pathname + '?' + cleanParams.toString();
                window.history.pushState({ path: newUrl }, '', newUrl);

                // Fetch data
                tableArea.style.opacity = '0.5';
                
                fetch('manage_students.php?' + params.toString())
                    .then(response => response.text())
                    .then(html => {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = html;
                        
                        // Update Table
                        const newTable = tempDiv.querySelector('#ajax-table-container');
                        if (newTable) {
                            tableArea.innerHTML = newTable.innerHTML;
                        }
                        
                        tableArea.style.opacity = '1';
                    })
                    .catch(err => {
                        console.error('Error fetching data:', err);
                        tableArea.style.opacity = '1';
                    });
            }

            // Trigger ONLY on form submit (e.g. clicking the Search button)
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                updateContent();
            });
        });
    </script>
</body>
</html>
