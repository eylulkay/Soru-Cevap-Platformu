<?php
require_once "db_connect.php";

// Giriş yapılmamışsa login sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<?php include 'header.php'; ?>

    <div class="container">
        <div id="main-content">
            <h2>Sherlock'a Hoş Geldiniz</h2>
            <p>Sherlock, siber güvenlik alanında bilgi edinmek isteyen herkes için tasarlanmış, ücretsiz bir soru-cevap platformudur.</p>

            <h3>Biz Kimiz?</h3>
            <p>Siber güvenlik dünyası karmaşık ve hızla değişen bir alandır. Sherlock, bu alanda bilgi sahibi olmak isteyen kişilere yardımcı olmak için oluşturulmuştur. Platformumuzda, siber güvenlik konusunda sık sorulan sorulara cevaplar bulabilir ve kendi sorularınızı sorabilirsiniz.</p>

            <h3>Neler Yapabilirsiniz?</h3>
            <ul>
                <li><strong>Sık Sorulan Sorular (S.S.S):</strong> Siber güvenlik hakkında en sık sorulan soruların cevaplarını bulun.</li>
                <li><strong>Soru Sorun:</strong> Siber güvenlik hakkında merak ettiğiniz herhangi bir soruyu sorun ve uzmanlardan cevap alın.</li>
                <li><strong>Bilgi Edinin:</strong> Malware, phishing, şifreleme, ağ güvenliği ve daha pek çok konuda bilgi edinin.</li>
            </ul>

            <h3>Siber Güvenlik Nedir?</h3>
            <p>Siber güvenlik, bilgisayar ağları, sunucuları, mobil cihazları, elektronik sistemleri, verileri ve bilgileri yetkisiz erişim, değiştirilme veya yok edilmeden koruma işlemidir. Dijital çağda, siber güvenlik giderek daha önemli hale gelmektedir.</p>

            <h3>Başlayın</h3>
            <p>Hemen <a href="faq.php" style="color: #1b7808; font-weight: bold;">S.S.S sayfasını</a> ziyaret ederek sık sorulan soruların cevaplarını öğrenin veya <a href="ask.php" style="color: #1b7808; font-weight: bold;">soru sorma sayfasında</a> kendi sorularınızı sorun.</p>
        </div>
    </div>

<?php include 'footer.php'; ?>
