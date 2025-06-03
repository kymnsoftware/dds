<?php
// views/salary/bulk_report_template.php
// $results, $title, $startDate, $endDate, $department, $format değişkenleri SalaryController tarafından extract edilmiştir.
// Lütfen bu dosyanın doğrudan çağrılmadığından, sadece bir PHP include ile kullanıldığından emin olun.
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo sanitize_html($title); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { width: 100%; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 18px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section-title { font-size: 16px; font-weight: bold; margin: 20px 0 10px 0; }
        .total-row { font-weight: bold; background-color: #f2f2f2; }
        .footer { text-align: center; font-size: 12px; margin-top: 30px; color: #777; }
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <?php if ($format === 'pdf'): // Sadece PDF için yazdır butonlarını göster ?>
    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()">Yazdır</button>
        <button onclick="window.close()">Kapat</button>
    </div>
    <?php endif; ?>
    <div class="container">
        <div class="header">
            <div class="title"><?php echo sanitize_html($title); ?></div>
            <div class="subtitle"><?php echo date('d.m.Y', strtotime($startDate)); ?> - <?php echo date('d.m.Y', strtotime($endDate)); ?></div>
            <?php if (!empty($department)): ?>
            <div class="subtitle">Departman: <?php echo sanitize_html($department); ?></div>
            <?php endif; ?>
        </div>
        
        <div class="section-title">Personel Bazlı Maaş Detayları</div>
        <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <th>Personel</th>
                    <th>Departman</th>
                    <th>Çalışılan Gün</th>
                    <th>İzinli Gün</th>
                    <th>Toplam Devam</th>
                    <th>Gerekli Min.</th>
                    <th>Durum</th>
                    <th>Net Maaş</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalNetSalary = 0;
                $totalMeeting = 0;
                $totalNotMeeting = 0;
                foreach ($results as $result):
                    $badgeClass = $result['salary']['meets_minimum_requirement'] ? 'background-color: #28a745; color: white;' : 'background-color: #dc3545; color: white;';
                    $statusText = $result['salary']['meets_minimum_requirement'] ? 'Tamam' : 'Eksik';
                    $totalNetSalary += $result['salary']['net_salary'];
                    if ($result['salary']['meets_minimum_requirement']) {
                        $totalMeeting++;
                    } else {
                        $totalNotMeeting++;
                    }
                ?>
                <tr>
                    <td><?php echo sanitize_html($result['employee']['name']); ?></td>
                    <td><?php echo sanitize_html($result['employee']['department']); ?></td>
                    <td><?php echo sanitize_html($result['attendance']['worked_days']); ?></td>
                    <td><?php echo sanitize_html($result['attendance']['approved_leave_days']); ?></td>
                    <td><?php echo sanitize_html($result['attendance']['total_attended_days']); ?></td>
                    <td><?php echo sanitize_html($result['period']['required_work_days']); ?></td>
                    <td><span style="padding: 3px 8px; border-radius: 12px; <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span></td>
                    <td><strong><?php echo number_format($result['salary']['net_salary'], 2, ',', '.') . ' ₺'; ?></strong></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="7" style="text-align: right;"><strong>TOPLAM NET MAAŞ:</strong></td>
                    <td><strong><?php echo number_format($totalNetSalary, 2, ',', '.') . ' ₺'; ?></strong></td>
                </tr>
            </tbody>
        </table>

        <div class="section-title">Genel Özet</div>
        <table>
            <tr><th>Toplam Personel</th><td><?php echo count($results); ?></td></tr>
            <tr><th>Şartı Karşılayan</th><td><?php echo sanitize_html($totalMeeting); ?></td></tr>
            <tr><th>Şartı Karşılamayan</th><td><?php echo sanitize_html($totalNotMeeting); ?></td></tr>
            <tr><th>Toplam Ödenecek Net Maaş</th><td><strong><?php echo number_format($totalNetSalary, 2, ',', '.') . ' ₺'; ?></strong></td></tr>
        </table>

        <?php else: ?>
            <p>Seçilen kriterlere uygun veri bulunamadı.</p>
        <?php endif; ?>
        
        <div class="footer">
            Bu rapor <?php echo APP_TITLE; ?> tarafından otomatik olarak oluşturulmuştur.<br>
            Oluşturulma Tarihi: <?php echo date('d.m.Y H:i:s'); ?>
        </div>
    </div>
</body>
</html>