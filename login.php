<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once "db_connect.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    // === GİRİŞ ===
    if ($action === 'login') {

        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // HASH YOK
        if ($user && $password === $user['password']) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: " . ($user['role'] === 'admin' ? 'admin.php' : 'index.php'));
            exit;

        } else {
            $error = "Kullanıcı adı veya şifre hatalı!";
        }
    }

    // === KAYIT ===
    if ($action === 'register') {

        if ($_POST['password'] !== $_POST['password_confirm']) {
            $error = "Şifreler eşleşmiyor!";
        } else {

            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$_POST['username']]);

            if ($stmt->fetchColumn() > 0) {
                $error = "Bu kullanıcı adı zaten var!";
            } else {
                $db->prepare("
                    INSERT INTO users (username, password, role)
                    VALUES (?, ?, 'user')
                ")->execute([$_POST['username'], $_POST['password']]);

                $success = "Kayıt başarılı! Giriş yapabilirsiniz.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Sherlock | Giriş Yap</title>
<link rel="stylesheet" href="./css/style.css">
<style>
.login-container{
    width:360px;
    margin:80px auto;
    background:#fff;
    padding:30px;
    border-radius:8px;
    box-shadow:0 4px 10px rgba(0,0,0,.1)
}
.login-header{text-align:center}
.login-header h1{color:#1b7808;margin:0}
.login-tabs{display:flex;margin:20px 0}
.login-tab{
    flex:1;
    padding:10px;
    border:none;
    cursor:pointer;
    font-weight:bold;
    background:#eee
}
.login-tab.active{
    background:#1b7808;
    color:#fff
}
.login-form{display:none}
.login-form.active{display:block}
.form-group{margin-bottom:15px}
.form-group label{
    display:block;
    font-size:12px;
    font-weight:bold;
    margin-bottom:5px
}
.form-group input{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:4px
}
.button_1{
    width:100%;
    background:#1b7808;
    color:#fff;
    border:none;
    padding:12px;
    border-radius:4px;
    font-weight:bold;
    cursor:pointer
}
.error,.success{
    padding:10px;
    margin-bottom:15px;
    border-radius:4px;
    font-size:13px
}
.error{background:#f8d7da;color:#721c24}
.success{background:#d4edda;color:#155724}
</style>
</head>

<body>
<div class="login-container">

<div class="login-header">
    <h1>Sherlock</h1>
    <p>Siber Güvenlik Soru & Cevap Platformu</p>
</div>

<?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
<?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

<div class="login-tabs">
    <button class="login-tab active" onclick="showTab('login')">Giriş Yap</button>
    <button class="login-tab" onclick="showTab('register')">Kayıt Ol</button>
</div>

<form method="POST" class="login-form active" id="login">
<input type="hidden" name="action" value="login">
<div class="form-group">
<label>KULLANICI ADI:</label>
<input type="text" name="username" required>
</div>
<div class="form-group">
<label>ŞİFRE:</label>
<input type="password" name="password" required>
</div>
<button class="button_1">GİRİŞ YAP</button>
<p style="text-align:center;font-size:12px;margin-top:10px">
Demo Admin Hesabı:<br>
Kullanıcı: <b>admin</b><br>
Şifre: <b>admin123</b>
</p>
</form>

<form method="POST" class="login-form" id="register">
<input type="hidden" name="action" value="register">
<div class="form-group">
<label>KULLANICI ADI:</label>
<input type="text" name="username" required>
</div>
<div class="form-group">
<label>ŞİFRE:</label>
<input type="password" name="password" required>
</div>
<div class="form-group">
<label>ŞİFRE TEKRAR:</label>
<input type="password" name="password_confirm" required>
</div>
<button class="button_1">KAYIT OL</button>
</form>

</div>

<script>
function showTab(tab){
document.querySelectorAll('.login-form').forEach(f=>f.classList.remove('active'));
document.querySelectorAll('.login-tab').forEach(t=>t.classList.remove('active'));
document.getElementById(tab).classList.add('active');
event.target.classList.add('active');
}
</script>
</body>
</html>
