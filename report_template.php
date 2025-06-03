<?php
// views/salary/report_template.php
// $result, $title, $format değişkenleri SalaryController tarafından extract edilmiştir.
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
            <div class="subtitle"><?php echo date('d.m.Y', strtotime($result['period']['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($result['period']['end_date'])); ?></div>
        </div>
        
        <div class="section-title">1. Personel ve Dönem Bilgileri</div>
        <table>
            <tr><th width="25%">Personel Adı</th><td><?php echo sanitize_html($result['employee']['name']); ?></td></tr>
            <tr><th>Departman</th><td><?php echo sanitize_html($result['employee']['department'] ?: '-'); ?></td></tr>
            <tr><th>Pozisyon</th><td><?php echo sanitize_html($result['employee']['position'] ?: '-'); ?></td></tr>
            <tr><th>Dönem Başlangıç</th><td><?php echo date('d.m.Y', strtotime($result['period']['start_date'])); ?></td></tr>
            <tr><th>Dönem Bitiş</th><td><?php echo date('d.m.Y', strtotime($result['period']['end_date'])); ?></td></tr>
            <tr><th>Toplam Takvim Günü</th><td><?php echo sanitize_html($result['period']['calendar_days']); ?></td></tr>
            <tr><th>Dönem İş Günü</th><td><?php echo sanitize_html($result['period']['total_work_days']); ?></td></tr>
            <tr><th>Gerekli Minimum Çalışma Günü</th><td><?php echo sanitize_html($result['period']['required_work_days']); ?></td></tr>
        </table>
        
        <div class="section-title">2. Çalışma ve Maaş Özeti</div>
        <table>
            <tr><th width="25%">Çalışılan Gün</th><td><?php echo sanitize_html($result['attendance']['worked_days']); ?> gün</td></tr>
            <tr><th>Çalışılan Toplam Saat</th><td><?php echo sanitize_html($result['attendance']['worked_hours']); ?> saat</td></tr>
            <tr><th>Normal Çalışma Süresi</th><td><?php echo sanitize_html($result['attendance']['normal_hours']); ?> saat</td></tr>
            <tr><th>Fazla Mesai Süresi</th><td><?php echo sanitize_html($result['attendance']['overtime_hours']); ?> saat</td></tr>
            <tr><th>Onaylı İzin Günü</th><td><?php echo sanitize_html($result['attendance']['approved_leave_days']); ?> gün</td></tr>
            <tr><th>Toplam Devam (Çalışma+İzin)</th><td><?php echo sanitize_html($result['attendance']['total_attended_days']); ?> gün</td></tr>
            <tr><th>Devam Oranı</th><td><?php echo sanitize_html($result['attendance']['attendance_rate']); ?>%</td></tr>
            <tr><th>Normal Ücret</th><td><?php echo number_format($result['salary']['regular_salary'], 2, ',', '.') . ' ₺'; ?></td></tr>
            <tr><th>Fazla Mesai Ücreti</th><td><?php echo number_format($result['salary']['overtime_salary'], 2, ',', '.') . ' ₺'; ?></td></tr>
            <?php if ($result['salary']['leave_salary'] > 0): ?>
            <tr><th>İzin Ücreti</th><td><?php echo number_format($result['salary']['leave_salary'], 2, ',', '.') . ' ₺'; ?></td></tr>
            <?php endif; ?>
            <?php if ($result['salary']['deduction_amount'] > 0): ?>
            <tr><th>Kesinti Miktarı</th><td>-<?php echo number_format($result['salary']['deduction_amount'], 2, ',', '.') . ' ₺'; ?></td></tr>
            <?php endif; ?>
            <tr class="total-row"><th>Net Maaş</th><td><?php echo number_format($result['salary']['net_salary'], 2, ',', '.') . ' ₺'; ?></td></tr>
        </table>
        
        <div class="section-title">3. Maaş Parametreleri</div>
        <table>
            <tr><th width="25%">Maaş Türü</th><td><?php echo ($result['salary']['salary_type'] === 'fixed' ? 'Sabit Maaş' : 'Saatlik Ücret'); ?></td></tr>
            <?php if ($result['salary']['salary_type'] === 'fixed'): ?>
            <tr><th>Aylık Sabit Maaş</th><td><?php echo number_format($result['salary']['base_salary'], 2, ',', '.') . ' ₺'; ?></td></tr>
            <?php else: ?>
            <tr><th>Saatlik Ücret</th><td><?php echo number_format($result['salary']['hourly_rate'], 2, ',', '.') . ' ₺/saat'; ?></td></tr>
            <?php endif; ?>
            <tr><th>Günlük Çalışma Saati</th><td><?php echo sanitize_html($result['salary']['daily_work_hours']); ?> saat</td></tr>
            <tr><th>Fazla Mesai Çarpanı</th><td><?php echo sanitize_html($result['salary']['overtime_rate']); ?>x</td></tr>
            <tr><th>Aylık Çalışma Günü (Beklenen)</th><td><?php echo sanitize_html($result['salary']['monthly_work_days']); ?> gün</td></tr>
        </table>
        
        <?php if (!empty($result['daily_details'])): ?>
        <div class="section-title">4. Günlük Çalışma Detayları</div>
        <table>
            <thead>
                <tr>
                    <th>Tarih</th>
                    <th>Giriş</th>
                    <th>Çıkış</th>
                    <th>Normal Çalışma</th>
                    <th>Fazla Mesai</th>
                    <th>Toplam Çalışma</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['daily_details'] as $day): ?>
                <tr>
                    <td><?php echo date('d.m.Y', strtotime($day['date'])); ?></td>
                    <td><?php echo sanitize_html($day['entry']); ?></td>
                    <td><?php echo sanitize_html($day['exit']); ?></td>
                    <td><?php echo sanitize_html($day['normal_hours']); ?> saat</td>
                    <td><?php echo sanitize_html($day['overtime_hours']); ?> saat</td>
                    <td><?php echo sanitize_html($day['work_hours']); ?> saat</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($result['details']['leave_details'])): ?>
        <div class="section-title">5. İzin Detayları</div>
        <table>
            <thead>
                <tr>
                    <th>Başlangıç</th>
                    <th>Bitiş</th>
                    <th>Gün</th>
                    <th>İzin Türü</th>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['details']['leave_details'] as $leave): ?>
                <tr>
                    <td><?php echo date('d.m.Y', strtotime($leave['start_date'])); ?></td>
                    <td><?php echo date('d.m.Y', strtotime($leave['end_date'])); ?></td>
                    <td><?php echo sanitize_html($leave['total_days']); ?></td>
                    <td><?php echo sanitize_html($leave['leave_type_name']); ?></td>
                    <td><?php echo sanitize_html($leave['reason'] ?: '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($result['details']['absence_details'])): ?>
        <div class="section-title">6. Devamsızlık Detayları</div>
        <table>
            <thead>
                <tr>
                    <th>Başlangıç</th>
                    <th>Bitiş</th>
                    <th>Gün</th>
                    <th>Durum</th>
                    <th>Devamsızlık Türü</th>
                    <th>Açıklama</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result['details']['absence_details'] as $absence): ?>
                <tr>
                    <td><?php echo date('d.m.Y', strtotime($absence['start_date'])); ?></td>
                    <td><?php echo date('d.m.Y', strtotime($absence['end_date'])); ?></td>
                    <td><?php echo sanitize_html($absence['total_days']); ?></td>
                    <td><?php echo $absence['is_justified'] ? 'Mazeretli' : 'Mazeretsiz'; ?></td>
                    <td><?php echo sanitize_html($absence['absence_type_name']); ?></td>
                    <td><?php echo sanitize_html($absence['reason'] ?: '-'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
        
        <div class="footer">
            Bu rapor <?php echo APP_TITLE; ?> tarafından otomatik olarak oluşturulmuştur.<br>
            Oluşturulma Tarihi: <?php echo date('d.m.Y H:i:s'); ?>
        </div>
    </div>
</body>
</html>