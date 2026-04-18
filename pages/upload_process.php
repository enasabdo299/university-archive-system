<?php
session_start();


// Simple DB connection if config.php doesn't exist or isn't structured as expected
$host = 'localhost';
$db   = 'university_archive';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && in_array($_SESSION['role'], ['student', 'admin'])) {
    $title = htmlspecialchars($_POST['title']);
    $abstract = htmlspecialchars($_POST['abstract']);
    $team_members = htmlspecialchars($_POST['team_members']);
    $supervisor = htmlspecialchars($_POST['supervisor']);
    $faculty = htmlspecialchars($_POST['faculty']);
    $academic_year = htmlspecialchars($_POST['academic_year']);
    $student_id = $_SESSION['user_id'];

    if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['project_file']['tmp_name'];
        $fileName = $_FILES['project_file']['name'];
        $fileSize = $_FILES['project_file']['size'];
        $fileType = $_FILES['project_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('pdf', 'docx');

        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Upload directory
            $uploadFileDir = '../uploads/projects/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Insert into database
                $sql = "INSERT INTO projects (title, description, student_id, supervisor, academic_year, faculty, file_path, status, team_members, file_type) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$title, $abstract, $student_id, $supervisor, $academic_year, $faculty, $newFileName, $team_members, $fileExtension]);

                $_SESSION['message'] = "تم رفع المشروع بنجاح وهو الآن قيد المراجعة.";
                $redirect = ($_SESSION['role'] === 'admin') ? "admin_dashboard.php" : "student_dashboard.php";
                header("Location: $redirect");
                exit;
            } else {
                echo "حدث خطأ أثناء نقل الملف المرفوع.";
            }
        } else {
            echo "امتداد الملف غير مسموح به. يرجى رفع ملف بصيغة PDF أو DOCX.";
        }
    } else {
        echo "يرجى اختيار ملف لرفعه.";
    }
} else {
    header("Location: login.php");
    exit;
}
?>
