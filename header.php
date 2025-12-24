<?php $page = basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Sherlock | Siber Güvenlik</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><span class="highlight">Sherlock</span> Soru & Cevap</h1>
            </div>
            <nav>
                <ul>
                    <li class="<?php if($page == 'index.php'){echo 'current';} ?>"><a href="index.php">Ana Sayfa</a></li>
                    <li class="<?php if($page == 'faq.php'){echo 'current';} ?>"><a href="faq.php">S.S.S</a></li>
                    <li class="<?php if($page == 'ask.php'){echo 'current';} ?>"><a href="ask.php">Soru Sor</a></li>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li class="<?php if($page == 'admin.php'){echo 'current';} ?>"><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <?php if(isset($_SESSION['username'])): ?>
                        <li style="color:white; font-weight:bold; padding: 0 20px;">👤 <?php echo htmlspecialchars($_SESSION['username']); ?></li>
                        <li><a href="logout.php">Çıkış Yap</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
