<?php
// views/reports/excel_template.php
// $data, $title, $headers, $reportType değişkenleri ReportController tarafından extract edilmiştir.
// Bu şablon, Excel'e aktarılacak genel bir HTML tablosu oluşturur.

if (!isset($data) || !isset($title) || !isset($headers) || !isset($reportType)) {
    die("Hata: Rapor verileri eksik.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo sanitize_html($title); ?></title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .total-row { font-weight: bold; background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1><?php echo sanitize_html($title); ?></h1>
    <?php if (count($data) > 0): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach ($headers as $header): ?>
                        <th><?php echo sanitize_html($header); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Rapor tipine göre özel formatlama
                foreach ($data as $row): ?>
                    <tr>
                        <?php foreach ($row as $key => $value): ?>
                            <td>
                                <?php
                                    // Tarih formatlama
                                    if (strpos($key, 'date') !== false && $value) {
                                        echo date('d.m.Y', strtotime($value));
                                    }
                                    // Tarih ve saat formatlama (event_time, created_at gibi)
                                    elseif (strpos($key, '_time') !== false && $value) {
                                        echo date('d.m.Y H:i:s', strtotime($value));
                                    }
                                    // Durum formatlama
                                    elseif ($key === 'is_justified') {
                                        echo $value ? 'Mazeretli' : 'Mazeretsiz';
                                    }
                                    // İzin türü listesi (GROUP_CONCAT ile gelen)
                                    elseif ($key === 'absence_types_list') {
                                        echo sanitize_html($value);
                                    }
                                    // NULL veya boş değerler için tire
                                    elseif ($value === null || $value === '') {
                                        echo '-';
                                    }
                                    // Diğer tüm değerler
                                    else {
                                        echo sanitize_html($value);
                                    }
                                ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Seçilen kriterlere uygun veri bulunamadı.</p>
    <?php endif; ?>
    <div style="text-align: center; margin-top: 20px; font-size: 12px;">
        PDKS Raporu - Oluşturulma Tarihi: <?php echo date('d.m.Y H:i:s'); ?>
    </div>
</body>
</html>