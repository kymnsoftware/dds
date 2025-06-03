<?php
// views/leave/detail_partial.php
// $leave değişkeni LeaveController tarafından extract edilmiştir.
?>

<div class="text-center mb-3">
    <img src="<?php echo !empty($leave['photo_path']) ? sanitize_html($leave['photo_path']) : '/public/uploads/default-user.png'; ?>" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px;" alt="Profil">
    <h5 class="mt-2"><?php echo sanitize_html($leave['name']) . ' ' . sanitize_html($leave['surname']); ?></h5>
    <p class="text-muted"><?php echo sanitize_html($leave['department']) . ' - ' . sanitize_html($leave['position']); ?></p>
</div>
<table class="table table-bordered">
    <tr>
        <th width="40%">İzin Türü</th>
        <td>
            <span class="badge" style="background-color: <?php echo sanitize_html($leave['color']); ?>; color: white;">
                <?php echo sanitize_html($leave['leave_type_name']); ?>
            </span>
        </td>
    </tr>
    <tr>
        <th>Başlangıç Tarihi</th>
        <td><?php echo date('d.m.Y', strtotime($leave['start_date'])); ?></td>
    </tr>
    <tr>
        <th>Bitiş Tarihi</th>
        <td><?php echo date('d.m.Y', strtotime($leave['end_date'])); ?></td>
    </tr>
    <tr>
        <th>Toplam Gün</th>
        <td><?php echo sanitize_html($leave['total_days']); ?> gün</td>
    </tr>
    <tr>
        <th>Durum</th>
        <td>
            <?php 
            $statusInfo = '';
            switch ($leave['status']) {
                case 'pending':
                    $statusInfo = '<span class="badge badge-warning">Beklemede</span>';
                    break;
                case 'approved':
                    $statusInfo = '<span class="badge badge-success">Onaylandı</span>';
                    break;
                case 'rejected':
                    $statusInfo = '<span class="badge badge-danger">Reddedildi</span>';
                    break;
            }
            echo $statusInfo;
            ?>
        </td>
    </tr>
    <tr>
        <th>Talep Tarihi</th>
        <td><?php echo date('d.m.Y H:i', strtotime($leave['created_at'])); ?></td>
    </tr>
</table>
<div class="card">
    <div class="card-header">Açıklama / Sebep</div>
    <div class="card-body">
        <?php echo !empty($leave['reason']) ? nl2br(sanitize_html($leave['reason'])) : 'Belirtilmemiş'; ?>
    </div>
</div>

<?php if ($leave['status'] != 'pending' && !empty($leave['comment'])): ?>
    <div class="card mt-3">
        <div class="card-header">Yönetici Notu</div>
        <div class="card-body">
            <?php echo nl2br(sanitize_html($leave['comment'])); ?>
        </div>
    </div>
<?php endif; ?>