<?php
// views/leave/request.php
// $leaveTypes, $userRequests, $userBalances değişkenleri LeaveController tarafından extract edilmiştir.
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_TITLE; ?> - İzin Talep Sistemi</title>
</head>
<body>
    <?php include APP_ROOT . '/views/shared/header.php'; ?>

    <main class="main-content">
        <div class="container">
            <div class="header">
                <div>
                    <h2><i class="fas fa-calendar-alt mr-2"></i> İzin Talep Sistemi</h2>
                </div>
                <div class="user-info">
                    <img src="<?php echo sanitize_html($_SESSION['photo_path']); ?>" class="user-photo" alt="Profil">
                    <div>
                        <h5><?php echo sanitize_html($_SESSION['user_name']); ?></h5>
                        <p class="text-muted mb-0"><?php echo sanitize_html($_SESSION['department']) . ' - ' . sanitize_html($_SESSION['position']); ?></p>
                    </div>
                </div>
                <div>
                    <a href="/public/logout.php" class="btn btn-outline-secondary">
                        <i class="fas fa-sign-out-alt mr-1"></i> Çıkış Yap
                    </a>
                </div>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?php echo sanitize_html($success_message); ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo sanitize_html($error_message); ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-plus-circle mr-1"></i> Yeni İzin Talebi
                        </div>
                        <div class="card-body">
                            <form id="submit-leave-request-form">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="leave_type_id">İzin Türü</label>
                                        <select class="form-control" id="leave_type_id" name="leave_type_id" required>
                                            <option value="">Seçiniz</option>
                                            <?php foreach ($leaveTypes as $type): ?>
                                                <option value="<?php echo sanitize_html($type['id']); ?>"><?php echo sanitize_html($type['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="start_date">Başlangıç Tarihi</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="end_date">Bitiş Tarihi</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="reason">Açıklama / Sebep</label>
                                    <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane mr-1"></i> Talep Oluştur
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card mt-4">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-history mr-1"></i> İzin Talebi Geçmişi
                        </div>
                        <div class="card-body">
                            <?php if (count($userRequests) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>İzin Türü</th>
                                                <th>Başlangıç</th>
                                                <th>Bitiş</th>
                                                <th>Süre</th>
                                                <th>Durum</th>
                                                <th>Oluşturulma</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($userRequests as $request): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge" style="background-color: <?php echo sanitize_html($request['color']); ?>; color: white;">
                                                            <?php echo sanitize_html($request['leave_type_name']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('d.m.Y', strtotime($request['start_date'])); ?></td>
                                                    <td><?php echo date('d.m.Y', strtotime($request['end_date'])); ?></td>
                                                    <td><?php echo sanitize_html($request['total_days']); ?> gün</td>
                                                    <td>
                                                        <?php 
                                                        if ($request['status'] == 'pending') {
                                                            echo '<span class="leave-status status-pending">Beklemede</span>';
                                                        } elseif ($request['status'] == 'approved') {
                                                            echo '<span class="leave-status status-approved">Onaylandı</span>';
                                                        } else {
                                                            echo '<span class="leave-status status-rejected">Reddedildi</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Henüz izin talebiniz bulunmamaktadır.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-calculator mr-1"></i> İzin Bakiyelerim (<?php echo date('Y'); ?>)
                        </div>
                        <div class="card-body">
                            <?php if (count($userBalances) > 0): ?>
                                <div class="row">
                                    <?php foreach ($userBalances as $balance): ?>
                                        <div class="col-md-12">
                                            <div class="balance-card">
                                                <div class="balance-header bg-info">
                                                    <?php echo sanitize_html($balance['leave_type_name']); ?>
                                                </div>
                                                <div class="balance-body">
                                                    <div class="balance-value"><?php echo sanitize_html($balance['remaining_days']); ?></div>
                                                    <div class="text-muted">Kalan Gün</div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-6">
                                                            <div>Toplam: <b><?php echo sanitize_html($balance['total_days']); ?></b></div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div>Kullanılan: <b><?php echo sanitize_html($balance['used_days']); ?></b></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i> Henüz izin bakiyeniz tanımlanmamış.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-question-circle mr-1"></i> Yardım
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-info-circle text-info mr-2"></i> İzin talebi oluşturmak için formu doldurun.</li>
                                <li class="mt-2"><i class="fas fa-info-circle text-info mr-2"></i> Talebiniz yöneticiniz tarafından değerlendirilecektir.</li>
                                <li class="mt-2"><i class="fas fa-info-circle text-info mr-2"></i> İzin geçmişinizi alt bölümden takip edebilirsiniz.</li>
                                <li class="mt-2"><i class="fas fa-info-circle text-info mr-2"></i> Sorun yaşarsanız yöneticinizle iletişime geçin.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include APP_ROOT . '/views/shared/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Bitiş tarihi kontrolü
            $('#start_date').change(function() {
                $('#end_date').attr('min', $(this).val());
            });

            // İzin talep formu submit
            $('#submit-leave-request-form').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    type: 'POST',
                    url: '/ajax/api.php?action=submitLeaveRequest', // Yeni AJAX endpoint
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            $('#submit-leave-request-form')[0].reset();
                            // Yeni talepleri ve bakiyeleri yenile
                            location.reload(); // Şimdilik basit bir yenileme
                        } else {
                            showAlert(response.message, 'danger');
                        }
                    },
                    error: function() {
                        showAlert('İzin talebi gönderilirken bir hata oluştu!', 'danger');
                    }
                });
            });
        });
    </script>
</body>
</html>