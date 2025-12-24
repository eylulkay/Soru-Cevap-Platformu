<?php
require_once "db_connect.php";

// Giriş kontrolü
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

// Cevap gönderme işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'answer') {
    $question_id = intval($_POST['question_id']);
    $answer = htmlspecialchars(trim($_POST['answer']));

    if (!empty($answer)) {
        $stmt = $db->prepare("UPDATE questions SET answer_text = ?, status = 'Cevaplandı' WHERE id = ?");
        if ($stmt->execute([$answer, $question_id])) {
            $success = "Cevap başarıyla gönderildi!";
        } else {
            $error = "Cevap gönderme sırasında bir hata oluştu!";
        }
    } else {
        $error = "Lütfen bir cevap yazınız!";
    }
}

// Admin soru silme
if (isset($_GET['delete'])) {
    $question_id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
    $stmt->execute([$question_id]);
}

// Soruları getir
$stmt = $db->prepare("SELECT * FROM questions ORDER BY created_at DESC");
$stmt->execute();
$questions = $stmt->fetchAll();

// İstatistikler
$stmt = $db->prepare("SELECT COUNT(*) as total FROM questions");
$stmt->execute();
$total_questions = $stmt->fetch()['total'];

$stmt = $db->prepare("SELECT COUNT(*) as answered FROM questions WHERE status = 'Cevaplandı'");
$stmt->execute();
$answered_questions = $stmt->fetch()['answered'];

$pending_questions = $total_questions - $answered_questions;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Sherlock | Admin Paneli</title>
    <link rel="stylesheet" href="./css/style.css">
    <style>
        .admin-header {
            background: #1b7808;
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 4px;
        }

        .admin-header h2 { margin: 0; }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stat-box h3 {
            margin: 0 0 10px 0;
            color: #404040;
            font-size: 14px;
            text-transform: uppercase;
        }

        .stat-box .number {
            font-size: 36px;
            font-weight: bold;
            color: #1b7808;
        }

        .questions-list {
            background: white;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .question-item {
            border-bottom: 1px solid #eee;
            padding: 20px;
            transition: background 0.3s ease;
        }

        .question-item:hover { background: #f9f9f9; }
        .question-item:last-child { border-bottom: none; }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .question-header h4 {
            margin: 0;
            color: #404040;
            flex: 1;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-pending { background: #fff3cd; color: #856404; }
        .status-answered { background: #d4edda; color: #155724; }

        .question-meta {
            color: #666;
            font-size: 12px;
            margin-bottom: 10px;
        }

        .question-text {
            background: #f9f9f9;
            padding: 10px;
            border-left: 3px solid #1b7808;
            margin-bottom: 10px;
            border-radius: 2px;
        }

        .answer-section { margin-top: 15px; }
        .answer-section h5 { margin: 0 0 10px 0; color: #404040; }

        .answer-form { display: flex; gap: 10px; }

        .answer-form textarea {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: Arial, sans-serif;
            resize: vertical;
            min-height: 80px;
        }

        .answer-form button {
            background: #1b7808;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
            align-self: flex-start;
        }

        .answer-form button:hover { background: #145a06; }

        .existing-answer {
            background: #d4edda;
            padding: 10px;
            border-left: 3px solid #28a745;
            border-radius: 2px;
            margin-top: 10px;
        }

        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .delete-link {
            color: #c00;
            font-weight: bold;
            text-decoration: none;
        }
        .delete-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Sherlock</span> Admin Paneli</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Ana Sayfa</a></li>
                    <li><a href="logout.php">Çıkış Yap</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="admin-header">
            <div>
                <h2>Hoş Geldiniz, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
                <p>Kullanıcı sorularını yönetin ve cevaplandırın</p>
            </div>
        </div>

        <?php if (!empty($success)): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-box">
                <h3>Toplam Sorular</h3>
                <div class="number"><?php echo (int)$total_questions; ?></div>
            </div>
            <div class="stat-box">
                <h3>Cevaplanan</h3>
                <div class="number"><?php echo (int)$answered_questions; ?></div>
            </div>
            <div class="stat-box">
                <h3>Beklemede</h3>
                <div class="number"><?php echo (int)$pending_questions; ?></div>
            </div>
        </div>

        <h3 style="margin-top: 30px; color: #404040;">Kullanıcı Soruları</h3>

        <?php if (count($questions) > 0): ?>
            <div class="questions-list">
                <?php foreach ($questions as $q): ?>
                    <div class="question-item">
                        <div class="question-header">
                            <h4><?php echo htmlspecialchars($q['user_name']); ?></h4>
                            <span class="status-badge <?php echo $q['status'] == 'Cevaplandı' ? 'status-answered' : 'status-pending'; ?>">
                                <?php echo htmlspecialchars($q['status']); ?>
                            </span>
                        </div>

                        <div class="question-meta">
                            <strong>E-posta:</strong> <?php echo htmlspecialchars($q['user_email']); ?><br>
                            <strong>Tarih:</strong> <?php echo date('d.m.Y H:i', strtotime($q['created_at'])); ?>
                            <span style="float:right;">
                                <a class="delete-link" href="admin.php?delete=<?php echo (int)$q['id']; ?>" onclick="return confirm('Bu soruyu silmek istediğinize emin misiniz?')">Soruyu Sil</a>
                            </span>
                        </div>

                        <div class="question-text">
                            <strong>Soru:</strong> <?php echo htmlspecialchars($q['question_text']); ?>
                        </div>

                        <?php if (!empty($q['answer_text'])): ?>
                            <div class="existing-answer">
                                <strong>Cevap:</strong><br>
                                <?php echo htmlspecialchars($q['answer_text']); ?>
                            </div>
                        <?php else: ?>
                            <div class="answer-section">
                                <h5>Cevap Yazın:</h5>
                                <form method="POST" action="admin.php" class="answer-form">
                                    <input type="hidden" name="action" value="answer">
                                    <input type="hidden" name="question_id" value="<?php echo (int)$q['id']; ?>">
                                    <textarea name="answer" required></textarea>
                                    <button type="submit">Cevapla</button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 30px; text-align: center; border-radius: 4px;">
                <p style="color: #666;">Henüz soru bulunmamaktadır.</p>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>Sherlock Siber Güvenlik Soru & Cevap Platformu, Telif Hakkı &copy; 2025</p>
    </footer>
</body>
</html>
