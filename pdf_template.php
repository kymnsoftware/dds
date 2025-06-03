<?php
// views/reports/pdf_template.php
// $data, $title, $headers, $reportType değişkenleri ReportController tarafından extract edilmiştir.
// Bu şablon, PDF olarak yazdırılacak genel bir HTML tablosu oluşturur.

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
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { text-align: center; color: #333; }
        .no-print { text-align: center; margin: 20px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Yazdır</button>
        <button onclick="window.close()">Kapat</button>
    </div>
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