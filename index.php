<?php
// views/settings/index.php
// $systemSettings, $users değişkenleri SystemController tarafından extract edilmiştir.
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_TITLE; ?> - Sistem Ayarları</title>
</head>
<body>
    <?php include APP_ROOT . '/views/shared/header.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1 class="page-title">Sistem Ayarları</h1>
            <p class="page-subtitle">Sistem yönetimi ve konfigürasyon ayarları</p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-id-card mr-2"></i> Kart İşlemleri
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item list-group-item-action" id="new-card-btn">
                                <i class="fas fa-plus-circle mr-3 text-success"></i>
                                <strong>Yeni Kart Kaydı</strong>
                                <small class="text-muted d-block">Yeni personel kartı ekle</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" id="scan-card-btn">
                                <i class="fas fa-id-badge mr-3 text-primary"></i>
                                <strong>Kart Okut</strong>
                                <small class="text-muted d-block">Yeni kart okutma işlemi</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action" id="sync-devices-btn">
                                <i class="fas fa-sync mr-3 text-warning"></i>
                                <strong>Cihazları Senkronize Et</strong>
                                <small class="text-muted d-block">Kartları cihaza aktar</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-user-times mr-2"></i> Kullanıcı Silme İşlemleri
                    </div>
                    <div class="card-body">
                        <div id="delete-message-area"></div>
                        
                        <form id="single-delete-form" class="mb-4">
                            <h6 class="text-danger">Tek Kullanıcı Silme</h6>
                            <div class="form-group">
                                <label class="form-label">Kullanıcı</label>
                                <select class="form-control" id="user_id_to_delete" name="user_id" required>
                                    <option value="">Kullanıcı Seçin</option>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?php echo sanitize_html($user['user_id']); ?>"><?php echo sanitize_html($user['user_id']) . ' - ' . sanitize_html($user['name']) . ' ' . sanitize_html($user['surname']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="delete_type" id="delete_db_only" value="db_only" checked>
                                    <label class="custom-control-label" for="delete_db_only">Sadece Veritabanından Sil</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="delete_type" id="delete_both" value="both">
                                    <label class="custom-control-label" for="delete_both">Hem Veritabanından Hem Cihazdan Sil</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-user-minus mr-1"></i> Kullanıcıyı Sil
                            </button>
                        </form>

                        <h6 class="text-danger mt-4">Sadece Cihazdan Silme</h6>
                        <div class="form-group">
                            <label class="form-label">Kullanıcı</label>
                            <select class="form-control" id="device_only_user_id" name="device_only_user_id" required>
                                <option value="">Kullanıcı Seçin</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo sanitize_html($user['user_id']); ?>"><?php echo sanitize_html($user['user_id']) . ' - ' . sanitize_html($user['name']) . ' ' . sanitize_html($user['surname']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="button" id="delete-device-only" class="btn btn-warning">
                            <i class="fas fa-trash-alt mr-1"></i> Sadece Cihazdan Sil
                        </button>

                        <h6 class="text-danger mt-4">Tüm Kullanıcıları Sil (DİKKAT!)</h6>
                        <form id="delete-all-users-form">
                            <div class="alert alert-warning">
                                Tüm kullanıcıları silmek için aşağıdaki metni yazın: <br>
                                <strong>TÜM KULLANICILARI SİL</strong>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" id="confirm_text_delete_all" name="confirm_text" placeholder="Onay metnini buraya yazın">
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="delete_all_type" id="delete_all_db_only" value="db_only" checked>
                                    <label class="custom-control-label" for="delete_all_db_only">Sadece Veritabanından Sil</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="delete_all_type" id="delete_all_both" value="both">
                                    <label class="custom-control-label" for="delete_all_both">Hem Veritabanından Hem Cihazdan Sil</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Tüm Kullanıcıları Sil
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-cogs mr-2"></i> Genel Sistem Ayarları
            </div>
            <div class="card-body">
                <form id="system-settings-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Şirket Adı</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo sanitize_html($systemSettings['company_name'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Sistem Başlığı</label>
                                <input type="text" class="form-control" id="system_title" name="system_title" value="<?php echo sanitize_html($systemSettings['system_title'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Otomatik Senkronizasyon</label>
                                <select class="form-control" id="auto_sync" name="auto_sync">
                                    <option value="enabled" <?php echo ($systemSettings['auto_sync'] ?? 'enabled') == 'enabled' ? 'selected' : ''; ?>>Etkin</option>
                                    <option value="disabled" <?php echo ($systemSettings['auto_sync'] ?? 'enabled') == 'disabled' ? 'selected' : ''; ?>>Devre Dışı</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">SMTP Sunucu</label>
                                <input type="text" class="form-control" id="smtp_server" name="smtp_server" placeholder="smtp.example.com" value="<?php echo sanitize_html($systemSettings['smtp_server'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">SMTP E-posta</label>
                                <input type="email" class="form-control" id="smtp_email" name="smtp_email" placeholder="info@example.com" value="<?php echo sanitize_html($systemSettings['smtp_email'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">SMTP Şifre</label>
                                <input type="password" class="form-control" id="smtp_password" name="smtp_password" placeholder="******">
                                <small class="form-text text-muted">Sadece değiştirmek istiyorsanız doldurun.</small>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i> Ayarları Kaydet
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <i class="fas fa-info-circle mr-2"></i> Sistem Bilgisi
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Versiyon:</strong></td>
                                        <td>2.0.0</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Son Güncelleme:</strong></td>
                                        <td><?php echo date('d.m.Y'); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>PHP Versiyonu:</strong></td>
                                        <td><?php echo phpversion(); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>MySQL Versiyonu:</strong></td>
                                        <td><?php echo Database::connect()->getAttribute(PDO::ATTR_SERVER_VERSION); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Veritabanı Boyutu:</strong></td>
                                        <td><?php
                                            try {
                                                $size = Database::fetchColumn("SELECT SUM(data_length + index_length) / 1024 / 1024 'size' FROM information_schema.TABLES WHERE table_schema = :dbname", [':dbname' => DB_NAME]);
                                                echo round($size, 2) . ' MB';
                                            } catch(Exception $e) {
                                                echo "Hesaplanamadı";
                                            }
                                        ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Toplam Kart Sayısı:</strong></td>
                                        <td><?php
                                            try {
                                                echo (new CardModel())->getTotalPersonnelCount();
                                            } catch(Exception $e) {
                                                echo "0";
                                            }
                                        ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include APP_ROOT . '/views/shared/footer.php'; ?>

    <script>
        $(document).ready(function() {
            // Tek kullanıcı silme formu
            $('#single-delete-form').submit(function(e) {
                e.preventDefault();
                var userId = $('#user_id_to_delete').val();
                if (userId === '') {
                    $('#delete-message-area').html('<div class="alert alert-warning">Lütfen silinecek kullanıcıyı seçin.</div>');
                    return;
                }
                if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/api.php?action=deleteUser',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert(response.message, 'success');
                                $('#single-delete-form')[0].reset();
                                // Dropdown'ları ve kart listesini yenile
                                refreshUserDropdown();
                                updateCardsTable();
                            } else {
                                showAlert(response.message, 'danger');
                            }
                        },
                        error: function() {
                            showAlert('Silme işlemi sırasında bir hata oluştu!', 'danger');
                        }
                    });
                }
            });

            // Sadece cihazdan silme
            $('#delete-device-only').click(function() {
                var userId = $('#device_only_user_id').val();
                if (!userId) {
                    showAlert('Lütfen bir kullanıcı seçin.', 'warning');
                    return;
                }
                if (confirm('Bu kullanıcıyı sadece cihazdan silmek istediğinizden emin misiniz?')) {
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/api.php?action=deleteDeviceOnly',
                        data: { user_id: userId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert(response.message, 'success');
                                $('#device_only_user_id').val('');
                            } else {
                                showAlert(response.message, 'danger');
                            }
                        },
                        error: function() {
                            showAlert('Silme işlemi sırasında bir hata oluştu!', 'danger');
                        }
                    });
                }
            });

            // Tüm kullanıcıları silme formu
            $('#delete-all-users-form').submit(function(e) {
                e.preventDefault();
                var confirmText = $('#confirm_text_delete_all').val();
                if (confirmText !== 'TÜM KULLANICILARI SİL') {
                    showAlert('Onay metni doğru değil!', 'danger');
                    return;
                }
                if (confirm('DİKKAT: Tüm kullanıcıları veritabanından ve/veya cihazdan silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/api.php?action=deleteAllUsers',
                        data: $(this).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showAlert(response.message, 'success');
                                $('#delete-all-users-form')[0].reset();
                                // Dropdown'ları ve kart listesini yenile
                                refreshUserDropdown();
                                updateCardsTable();
                            } else {
                                showAlert(response.message, 'danger');
                            }
                        },
                        error: function() {
                            showAlert('Tüm kullanıcılar silinirken bir hata oluştu!', 'danger');
                        }
                    });
                }
            });

            // Sistem ayarlarını kaydet
            $('#system-settings-form').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '/ajax/api.php?action=saveSystemSettings',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showAlert(response.message, 'success');
                            // Güncellenen ayarları yansıtmak için gerekirse dashboard özetini yenile
                            // loadSalarySettingsSummary();
                        } else {
                            showAlert(response.message, 'danger');
                        }
                    },
                    error: function() {
                        showAlert('Sistem ayarları kaydedilirken bir hata oluştu!', 'danger');
                    }
                });
            });

            // Kullanıcı dropdownlarını yenileme fonksiyonu (main.js'den erişilebilir olmalı)
            // refreshUserDropdown(); // Sayfa yüklendiğinde bir kere çağrılabilir
        });
    </script>
</body>
</html>