<?php
// views/leave/processed_partial.php
// $requests değişkeni LeaveController tarafından extract edilmiştir.
?>

<?php if (count($requests) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Personel</th>
                    <th>İzin Türü</th>
                    <th>Başlangıç</th>
                    <th>Bitiş</th>
                    <th>Süre</th>
                    <th>Durum</th>
                    <th>İşlem Tarihi</th>
                    <th>Notlar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo sanitize_html($request['name']) . ' ' . sanitize_html($request['surname']); ?></td>
                        <td><span class="badge" style="background-color: <?php echo sanitize_html($request['color']); ?>; color: white;"><?php echo sanitize_html($request['leave_type_name']); ?></span></td>
                        <td><?php echo date('d.m.Y', strtotime($request['start_date'])); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($request['end_date'])); ?></td>
                        <td><?php echo sanitize_html($request['total_days']); ?> gün</td>
                        
                        <td>
                            <?php if ($request['status'] == 'approved'): ?>
                                <span class="leave-status status-approved">Onaylandı</span>
                            <?php else: ?>
                                <span class="leave-status status-rejected">Reddedildi</span>
                            <?php endif; ?>
                        </td>
                        
                        <td><?php echo date('d.m.Y H:i', strtotime($request['updated_at'])); ?></td>
                        <td><?php echo !empty($request['comment']) ? sanitize_html($request['comment']) : '-'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-1"></i> Filtrelere uygun izin talebi bulunamadı.
    </div>
<?php endif; ?>