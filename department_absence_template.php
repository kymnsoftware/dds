<?php
// views/reports/daily_attendance_template.php
// $data, $title, $headers değişkenleri ReportController tarafından extract edilmiştir.
?>
<h2 class="text-center mb-3"><?php echo sanitize_html($title); ?></h2>
<?php if (count($data) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Ad Soyad</th>
                    <th>Departman</th>
                    <th>Pozisyon</th>
                    <th>İlk Giriş</th>
                    <th>Son Çıkış</th>
                    <th>Toplam Süre</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo sanitize_html($row['user_id']); ?></td>
                        <td><?php echo sanitize_html($row['name']) . ' ' . sanitize_html($row['surname']); ?></td>
                        <td><?php echo sanitize_html($row['department']); ?></td>
                        <td><?php echo sanitize_html($row['position']); ?></td>
                        <td><?php echo $row['first_entry'] ? date('H:i', strtotime($row['first_entry'])) : '-'; ?></td>
                        <td><?php echo $row['last_exit'] ? date('H:i', strtotime($row['last_exit'])) : '-'; ?></td>
                        <td><?php echo sanitize_html($row['total_time'] ?: '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">Bu kriterlere uygun veri bulunamadı.</div>
<?php endif; ?>
<p class="text-muted text-right mt-3"><small>Rapor Oluşturulma Tarihi: <?php echo date('d.m.Y H:i:s'); ?></small></p>