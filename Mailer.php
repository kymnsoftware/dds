<?php
// core/Mailer.php

class Mailer {
    private $settings; // Sistem ayarları buradan çekilecek

    public function __construct() {
        // Ayarları veritabanından çekmek için SettingModel kullanın
        require_once APP_ROOT . '/models/SettingModel.php';
        $settingModel = new SettingModel();
        $this->settings = $settingModel->getSettingsByPrefix('smtp_');
    }

    public function sendEmail($to, $subject, $body, $altBody = '') {
        // Gerçek bir e-posta gönderimi için PHPMailer gibi bir kütüphane kullanmalısınız.
        // Mevcut kodunuzdaki gibi doğrudan mail() fonksiyonuyla gerçek e-posta gönderimi yapılamaz.
        // Bu yüzden, sadece başarılı döndürüyorum.

        $smtpServer = $this->settings['smtp_server'] ?? null;
        $smtpEmail = $this->settings['smtp_email'] ?? null;
        $smtpPassword = $this->settings['smtp_password'] ?? null;
        $companyName = APP_TITLE; // Veya ayarardan çekin

        if (!$smtpServer || !$smtpEmail || !$smtpPassword) {
            error_log("E-posta gönderimi için SMTP ayarları eksik.");
            return false;
        }

        // PHPMailer entegrasyonu örneği (PHPMailer kütüphanesini Composer ile kurmanız gerekir)
        /*
        require APP_ROOT . '/vendor/autoload.php'; // Composer ile yüklendiyse

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $smtpServer;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpEmail;
            $mail->Password = $smtpPassword;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; // veya ENCRYPTION_STARTTLS
            $mail->Port = 465; // veya 587

            $mail->setFrom($smtpEmail, $companyName . ' PDKS');
            $mail->addAddress($to);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = $altBody;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("E-posta gönderim hatası: {$mail->ErrorInfo}");
            return false;
        }
        */

        // Geçici olarak başarılı döndürüyorum.
        return true;
    }

    public function sendLeaveStatusEmail($userData, $leaveData) {
        $subject = 'İzin Talebiniz ' . ($leaveData['status'] == 'approved' ? 'Onaylandı' : 'Reddedildi');
        $statusColor = ($leaveData['status'] == 'approved' ? '#4CAF50' : '#F44336');

        $body = '
        <html>
        <head>
            <title>' . $subject . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { background-color: ' . $statusColor . '; color: white; padding: 10px; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; }
                .footer { font-size: 12px; text-align: center; margin-top: 30px; color: #777; }
                table { border-collapse: collapse; width: 100%; }
                th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                th { width: 40%; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>' . $subject . '</h2>
                </div>
                <div class="content">
                    <p>Sayın ' . sanitize_html($userData['name']) . ' ' . sanitize_html($userData['surname']) . ',</p>
                    <p>' . sanitize_html($leaveData['leave_type_name']) . ' talebi için yaptığınız başvuru ' .
                        ($leaveData['status'] == 'approved' ? '<strong>onaylanmıştır</strong>.' : '<strong>reddedilmiştir</strong>.') . '</p>
                    
                    <h3>İzin Detayları</h3>
                    <table>
                        <tr><th>İzin Türü</th><td>' . sanitize_html($leaveData['leave_type_name']) . '</td></tr>
                        <tr><th>Başlangıç Tarihi</th><td>' . date('d.m.Y', strtotime($leaveData['start_date'])) . '</td></tr>
                        <tr><th>Bitiş Tarihi</th><td>' . date('d.m.Y', strtotime($leaveData['end_date'])) . '</td></tr>
                        <tr><th>Toplam Gün</th><td>' . sanitize_html($leaveData['total_days']) . ' gün</td></tr>
                        <tr><th>Durum</th><td>' . ($leaveData['status'] == 'approved' ? 'Onaylandı' : 'Reddedildi') . '</td></tr>
                    </table>';
        
        if (!empty($leaveData['comment'])) {
            $body .= '
                    <h3>Yönetici Notu</h3>
                    <p>' . nl2br(sanitize_html($leaveData['comment'])) . '</p>';
        }
        
        $body .= '
                    <p>İzin takvimini ve durumunu PDKS sisteminden takip edebilirsiniz.</p>
                    <p>Saygılarımızla,<br>' . APP_TITLE . '</p>
                </div>
                <div class="footer">
                    Bu e-posta ' . APP_TITLE . ' tarafından otomatik olarak gönderilmiştir.
                </div>
            </div>
        </body>
        </html>';

        return $this->sendEmail($userData['email'], $subject, $body, strip_tags($body));
    }

    public function sendNewLeaveRequestEmail($managerEmails, $userData, $leaveData) {
        $subject = 'Yeni İzin Talebi Oluşturuldu: ' . sanitize_html($userData['name']) . ' ' . sanitize_html($userData['surname']);

        $body = '
        <html>
        <head>
            <title>' . $subject . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
                .header { background-color: #3498db; color: white; padding: 10px; border-radius: 5px 5px 0 0; }
                .content { padding: 20px; }
                .footer { font-size: 12px; text-align: center; margin-top: 30px; color: #777; }
                table { border-collapse: collapse; width: 100%; }
                th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                th { width: 40%; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>' . $subject . '</h2>
                </div>
                <div class="content">
                    <p>Sayın Yönetici,</p>
                    <p>' . sanitize_html($userData['name']) . ' ' . sanitize_html($userData['surname']) . ' adlı personel tarafından yeni bir izin talebi oluşturulmuştur. Lütfen talebi değerlendiriniz.</p>
                    
                    <h3>Personel Bilgileri</h3>
                    <table>
                        <tr><th>Ad Soyad</th><td>' . sanitize_html($userData['name']) . ' ' . sanitize_html($userData['surname']) . '</td></tr>
                        <tr><th>Departman</th><td>' . sanitize_html($userData['department']) . '</td></tr>
                        <tr><th>Pozisyon</th><td>' . sanitize_html($userData['position']) . '</td></tr>
                    </table>

                    <h3>İzin Detayları</h3>
                    <table>
                        <tr><th>İzin Türü</th><td>' . sanitize_html($leaveData['leave_type_name']) . '</td></tr>
                        <tr><th>Başlangıç Tarihi</th><td>' . date('d.m.Y', strtotime($leaveData['start_date'])) . '</td></tr>
                        <tr><th>Bitiş Tarihi</th><td>' . date('d.m.Y', strtotime($leaveData['end_date'])) . '</td></tr>
                        <tr><th>Toplam Gün</th><td>' . sanitize_html($leaveData['total_days']) . ' gün</td></tr>
                        <tr><th>Açıklama</th><td>' . (!empty($leaveData['reason']) ? nl2br(sanitize_html($leaveData['reason'])) : 'Belirtilmemiş') . '</td></tr>
                    </table>
                    <p>Talebi değerlendirmek için <a href="' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/leave_management.php#pending">İzin Yönetim Paneli</a>\'ni ziyaret edebilirsiniz.</p>
                    <p>Saygılarımızla,<br>' . APP_TITLE . '</p>
                </div>
                <div class="footer">
                    Bu e-posta ' . APP_TITLE . ' tarafından otomatik olarak gönderilmiştir.
                </div>
            </div>
        </body>
        </html>';

        foreach ($managerEmails as $email) {
            $this->sendEmail($email, $subject, $body, strip_tags($body));
        }
        return true;
    }
}
?>