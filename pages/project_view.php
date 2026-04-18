<?php
session_start();

// Database Connection
$host = 'localhost';
$db   = 'university_archive';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
try {
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (\PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: projects.php");
    exit;
}

// Fetch Project
$stmt = $pdo->prepare("SELECT p.*, u.full_name as student_name FROM projects p JOIN users u ON p.student_id = u.id WHERE p.id = ? AND p.status = 'approved'");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    die("المشروع غير موجود أو لم يتم اعتماده بعد.");
}

// Fetch Comments
$stmt = $pdo->prepare("SELECT c.*, u.full_name FROM comments c JOIN users u ON c.user_id = u.id WHERE c.project_id = ? ORDER BY c.created_at ASC");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

// Fetch Evaluation Counts
$stmt = $pdo->prepare("SELECT rating_type, COUNT(*) as count FROM evaluations WHERE project_id = ? GROUP BY rating_type");
$stmt->execute([$id]);
$evals = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$likes = $evals['like'] ?? 0;
$dislikes = $evals['dislike'] ?? 0;

// Guest Identification (Simple session-based)
if (!isset($_SESSION['guest_id'])) {
    $_SESSION['guest_id'] = uniqid('guest_');
}
$viewer_id = $_SESSION['user_id'] ?? null;
$session_id = $_SESSION['guest_id'];

?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project['title']); ?> - نظام الأرشفة</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css?v=1771762380" />
    <link rel="stylesheet" href="../css/projects.css" />

    <script src="../js/script.js" defer></script>
    <!-- <style>
        .project-detail-container {
            max-width: 1000px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        .project-info-header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .project-info-header h2 {
            color: var(--primary);
            
            margin-bottom: 15px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }
        .meta-item i {
            color: var(--primary);
            margin-left: 8px;
        }
        .abstract-section {
            line-height: 1.8;
            color: #444;
            margin: 30px 0;
            padding: 20px;
            border-right: 4px solid var(--secondary);
            background: #fffcf5;
        }
        .interaction-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin: 30px 0;
        }
        .rating-buttons {
            display: flex;
            gap: 15px;
        }
        .rating-btn {
            background: none;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            
        }
        .rating-btn:hover {
            background: #f0f0f0;
        }
        .rating-btn.active.like { background: #2ecc71; color: white; border-color: #2ecc71; }
        .rating-btn.active.dislike { background: #e74c3c; color: white; border-color: #e74c3c; }
        
        .comment-section h3 {
            margin-bottom: 20px;
            color: var(--primary);
        }
        .comment-item {
            background: #fff;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .comment-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            
        }
        .comment-author {  color: var(--primary); }
        .comment-date { color: #888; }
        
        .comment-form textarea {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5eb;
            border-radius: 10px;
            margin-bottom: 15px;
            resize: vertical;
        }
    </style> -->
</head>
<body>
    
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

                    <div class="back-button-wrapper">
                    <a href="projects.php" class="back-btn">
                        عودة <i class="fas fa-arrow-left"></i>
                    </a>
                    </div>
                </div>
            </header>
            
        </div>

        <div class="container">
            <div class="project-detail-container">
                <div class="project-info-header">
                    <h2><?php echo htmlspecialchars($project['title']); ?></h2>
                    <div class="meta-grid">
                        <div class="meta-item"><i class="fas fa-user-graduate"></i> الطالب: <?php echo htmlspecialchars($project['student_name']); ?></div>
                        <div class="meta-item"><i class="fas fa-chalkboard-teacher"></i> المشرف: <?php echo htmlspecialchars($project['supervisor']); ?></div>
                        <div class="meta-item"><i class="fas fa-university"></i> الكلية: <?php echo htmlspecialchars($project['faculty']); ?></div>
                        <div class="meta-item"><i class="fas fa-calendar-alt"></i> العام: <?php echo htmlspecialchars($project['academic_year']); ?></div>
                    </div>
                </div>

                <div class="team-section">
                    <h3><i class="fas fa-users"></i> فريق العمل المشارك:</h3>
                    <p style="margin: 10px 0; color: #555;"><?php echo nl2br(htmlspecialchars($project['team_members'])); ?></p>
                </div>

                <div class="abstract-section">
                    <h3>ملخص المشروع:</h3>
                    <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                </div>

                <div class="file-view">
                    <a href="../uploads/projects/<?php echo $project['file_path']; ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-file-<?php echo ($project['file_type'] == 'pdf' ? 'pdf' : 'word'); ?>"></i> عرض الملف الكامل (<?php echo strtoupper($project['file_type']); ?>)
                    </a>
                </div>

                <div class="interaction-bar">
                    <div class="rating-buttons">
                        <button class="rating-btn" onclick="evaluateProject('like')" id="likeBtn">
                            <i class="fas fa-thumbs-up"></i> <span id="likeCount"><?php echo $likes; ?></span> إعجاب
                        </button>
                        <button class="rating-btn" onclick="evaluateProject('dislike')" id="dislikeBtn">
                            <i class="fas fa-thumbs-down"></i> <span id="dislikeCount"><?php echo $dislikes; ?></span> لم يعجبني
                        </button>
                    </div>
                </div>

                <div class="comment-section">
                    <h3><i class="fas fa-comments"></i> التعليقات والتبادل المعرفي:</h3>
                    <div id="commentsList">
                        <?php if (empty($comments)): ?>
                            <p id="noComments">لا توجد تعليقات بعد. كن أول من يشارك!</p>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-header">
                                        <span class="comment-author"><?php echo htmlspecialchars($comment['full_name']); ?></span>
                                        <span class="comment-date"><?php echo date('Y/m/d H:i', strtotime($comment['created_at'])); ?></span>
                                    </div>
                                    <div class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="comment-form" style="margin-top: 30px;">
                            <textarea id="commentText" rows="3" placeholder="أضف تعليقك هنا..."></textarea>
                            <button class="btn btn-primary" onclick="submitComment()">إرسال التعليق</button>
                        </div>
                    <?php else: ?>
                        <p style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px;">
                            <i class="fas fa-info-circle"></i> يجب عليك <a href="login.php" style="color: var(--primary); font-weight: bold;">تسجيل الدخول</a> لتتمكن من إضافة تعليق.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
     <!-- زر العودة للأعلى -->
    <div class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </div>

    <script>
        const projectId = <?php echo $id; ?>;

        function evaluateProject(type) {
            fetch('evaluate_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ project_id: projectId, rating_type: type })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('likeCount').textContent = data.likes;
                    document.getElementById('dislikeCount').textContent = data.dislikes;
                    
                    document.getElementById('likeBtn').classList.remove('active');
                    document.getElementById('dislikeBtn').classList.remove('active');
                    if(data.user_rating === 'like') document.getElementById('likeBtn').classList.add('active');
                    if(data.user_rating === 'dislike') document.getElementById('dislikeBtn').classList.add('active');
                } else {
                    alert(data.message);
                }
            });
        }

        function submitComment() {
            const text = document.getElementById('commentText').value;
            if (!text.trim()) return;

            fetch('add_comment_process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ project_id: projectId, comment: text })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    </script>
</body>
</html>
