<?php
require_once "db_connect.php";

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$success = '';
$error = '';

$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Form gönderildiyse (Soru ekleme)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['question'])) {
    $user_email = htmlspecialchars(trim($_POST['email']));
    $question_text = htmlspecialchars(trim($_POST['question']));

    if (!empty($user_email) && !empty($question_text)) {
        $stmt = $db->prepare("INSERT INTO questions (user_id, user_name, user_email, question_text, status) VALUES (?, ?, ?, ?, 'Beklemede')");
        if ($stmt->execute([$userId, $username, $user_email, $question_text])) {
            $success = "Sorunuz başarıyla gönderildi! Admin tarafından en kısa sürede cevaplandırılacaktır.";
        } else {
            $error = "Soru gönderme sırasında bir hata oluştu!";
        }
    } else {
        $error = "Lütfen tüm alanları doldurunuz.";
    }
}

// Kullanıcı sadece kendi sorusunu silebilir
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $db->prepare("DELETE FROM questions WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);
}

// Tüm soruları getir (herkes görebilir)
$stmt = $db->prepare("SELECT * FROM questions ORDER BY created_at DESC");
$stmt->execute();
$questions = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

    <div class="container">
        <div id="main-content">
            <h2>Soru Sorun</h2>
            <p>Siber güvenlik hakkında merak ettiğiniz herhangi bir soruyu aşağıdaki form aracılığıyla sorun. Sorularınız incelendikten sonra cevaplandırılacaktır.</p>

            <?php if (!empty($success)): ?>
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="ask.php">
                <div class="form-group">
                    <label for="email">E-posta Adresiniz:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="question">Sorunuz:</label>
                    <textarea id="question" name="question" rows="6" required></textarea>
                </div>

                <button type="submit" class="button_1">Soruyu Gönder</button>
            </form>

            <hr style="margin-top: 40px; border: none; border-top: 2px solid #1b7808;">
            <h3>Tüm Sorular</h3>

            <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #1b7808; border-radius: 4px;">
                <?php foreach ($questions as $q): ?>
                    <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #ddd;">
                        <h4 style="margin: 0 0 5px 0; color: #404040;">
                            <?php echo htmlspecialchars($q['user_name']); ?>
                        </h4>

                        <p style="margin: 0 0 10px 0; color: #666; font-size: 12px;">
                            <strong>Soru:</strong> <?php echo htmlspecialchars($q['question_text']); ?>
                        </p>

                        <?php if (!empty($q['answer_text'])): ?>
                            <p style="margin: 0; color: #1b7808; font-weight: bold;">
                                <strong>Cevap:</strong> <?php echo htmlspecialchars($q['answer_text']); ?>
                            </p>
                        <?php else: ?>
                            <p style="margin: 0; color: #856404; font-weight: bold;">
                                <strong>Durum:</strong> <?php echo htmlspecialchars($q['status']); ?>
                            </p>
                        <?php endif; ?>

                        <?php if ((int)$q['user_id'] === (int)$userId): ?>
                            <div style="margin-top: 8px;">
                                <a href="ask.php?delete=<?php echo (int)$q['id']; ?>" style="color: red; font-weight: bold;">Sorumu Sil</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>
