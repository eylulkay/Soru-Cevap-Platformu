<?php
require_once "db_connect.php";

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// FAQ verilerini getir
$stmt = $db->prepare("SELECT * FROM faq ORDER BY id");
$stmt->execute();
$faqs = $stmt->fetchAll();
?>

<?php include 'header.php'; ?>

    <div class="container">
        <div id="main-content">
            <h2>Sık Sorulan Sorular (S.S.S)</h2>
            <p>Siber güvenlik hakkında en sık sorulan soruların cevaplarını burada bulabilirsiniz.</p>

            <?php if (count($faqs) > 0): ?>
                <div class="faq-container">
                    <?php foreach ($faqs as $faq): ?>
                        <div class="faq-item">
                            <h3><?php echo htmlspecialchars($faq['question']); ?></h3>
                            <p><?php echo htmlspecialchars($faq['answer']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Henüz FAQ bulunmamaktadır.</p>
            <?php endif; ?>
        </div>
    </div>

<?php include 'footer.php'; ?>
