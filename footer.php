<?php
// views/shared/footer.php

// APP_TITLE ve COMPANY_NAME tanımlı olduğundan emin olun
if (!defined('APP_TITLE')) define('APP_TITLE', 'PDKS Yönetim Sistemi');
if (!defined('COMPANY_NAME')) define('COMPANY_NAME', 'Şirket Adınız');
?>
    </main>

    <div class="modal fade" id="userDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user mr-2"></i> Personel Detayları
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="userDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                    <button type="button" class="btn btn-primary" id="edit-user-from-details">
                        <i class="fas fa-edit mr-1"></i> Düzenle
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit mr-2"></i> Personel Düzenle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="edit-user-form" enctype="multipart/form-data">
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div id="edit-photo-container">
                                    <img id="edit-user-photo" src="/public/uploads/default-user.png" class="user-photo mb-3" alt="Profil">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit_photo" name="photo" accept="image/*">
                                    <label class="custom-file-label" for="edit_photo">Fotoğraf Seç</label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Kart ve Sistem Bilgileri</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Kart Numarası</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="edit_card_number" name="card_number" required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" id="edit-scan-card-btn">Okut</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Yetki Seviyesi</label>
                                                    <select class="form-control" id="edit_privilege" name="privilege">
                                                        <option value="0">0 - Normal Kullanıcı</option>
                                                        <option value="1">1 - Kayıt Yetkilisi</option>
                                                        <option value="2">2 - Yönetici</option>
                                                        <option value="3">3 - Süper Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Şifre (değiştirmek için doldurun)</label>
                                                    <input type="password" class="form-control" id="edit_password" name="password">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox mt-4">
                                                        <input type="checkbox" class="custom-control-input" id="edit_enabled" name="enabled">
                                                        <label class="custom-control-label" for="edit_enabled">Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Personel Bilgileri</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Adı</label>
                                            <input type="text" class="form-control" id="edit_name" name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Soyadı</label>
                                            <input type="text" class="form-control" id="edit_surname" name="surname">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Departman</label>
                                            <input type="text" class="form-control" id="edit_department" name="department">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Pozisyon</label>
                                            <input type="text" class="form-control" id="edit_position" name="position">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Telefon</label>
                                            <input type="tel" class="form-control" id="edit_phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">E-posta</label>
                                            <input type="email" class="form-control" id="edit_email" name="email">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">İşe Giriş Tarihi</label>
                                            <input type="date" class="form-control" id="edit_hire_date" name="hire_date">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Doğum Tarihi</label>
                                            <input type="date" class="form-control" id="edit_birth_date" name="birth_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Adres</label>
                                    <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

             <div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0">Maaş Bilgileri</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="edit_salary_type">Maaş Türü</label>
                <select class="form-control" id="edit_salary_type" name="salary_type" onchange="toggleEditSalaryFields()">
                    <option value="fixed">Sabit Maaş</option>
                    <option value="hourly">Saatlik Ücret</option>
                </select>
                <small class="form-text text-muted">Personelin ücret alma şeklini seçin</small>
            </div>
        </div>
        
        <div id="edit-fixed-salary-fields">
            <div class="alert alert-info alert-sm">
                <i class="fas fa-info-circle mr-1"></i>
                <small><strong>Sabit Maaş:</strong> Ayda belirlenen sabit tutar ödenir. Minimum çalışma şartını karşılamazsa kesinti uygulanır.</small>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="edit_fixed_salary">Aylık Sabit Maaş (₺)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₺</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="edit_fixed_salary" name="fixed_salary" placeholder="35000.00">
                    </div>
                </div>
            </div>
        </div>
        
        <div id="edit-hourly-salary-fields" style="display: none;">
            <div class="alert alert-warning alert-sm">
                <i class="fas fa-clock mr-1"></i>
                <small><strong>Saatlik Ücret:</strong> Çalışılan saat başına ödeme yapılır. İzinli günler de ücretlendirilir.</small>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="edit_hourly_rate">Saatlik Ücret (₺/saat)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₺</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="edit_hourly_rate" name="hourly_rate" placeholder="150.00">
                        <div class="input-group-append">
                            <span class="input-group-text">/saat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0 text-secondary">Çalışma Parametreleri</h6>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="edit_daily_work_hours">Günlük Çalışma Saati</label>
                        <div class="input-group">
                            <input type="number" step="0.5" min="0" max="24" class="form-control" id="edit_daily_work_hours" name="daily_work_hours">
                            <div class="input-group-append">
                                <span class="input-group-text">saat</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="edit_overtime_rate">Fazla Mesai Çarpanı</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="1" max="5" class="form-control" id="edit_overtime_rate" name="overtime_rate">
                            <div class="input-group-append">
                                <span class="input-group-text">x</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="edit_monthly_work_days">Aylık Çalışma Günü</label>
                        <div class="input-group">
                            <input type="number" step="1" min="0" max="31" class="form-control" id="edit_monthly_work_days" name="monthly_work_days">
                            <div class="input-group-append">
                                <span class="input-group-text">gün</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1">
                                    <i class="fas fa-history mr-1"></i> Maaş İşlemleri
                                </h6>
                                <small class="text-muted">Bu personelin maaş hesaplaması ve geçmişi</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="calculate-current-salary">
                                    <i class="fas fa-calculator mr-1"></i> Hesapla
                                </button>
                            </div>
                        </div>
                        <div id="edit-salary-preview" style="display: none; margin-top: 10px;">
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" id="save-edit-user">
                        <i class="fas fa-save mr-1"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addCardModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle mr-2"></i> Yeni Personel Ekle
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="add-message-area"></div>
                    <form id="add-card-form" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div>
                                    <img id="new-user-photo" src="/public/uploads/default-user.png" class="user-photo mb-3" alt="Profil">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*">
                                    <label class="custom-file-label" for="photo">Fotoğraf Seç</label>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Kart ve Sistem Bilgileri</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Kullanıcı ID</label>
                                                    <input type="text" class="form-control" id="user_id" name="user_id" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Kart Numarası</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="card_number" name="card_number" required>
                                                        <div class="input-group-append">
                                                            <button class="btn btn-outline-secondary" type="button" id="add-scan-card-btn">Okut</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Yetki Seviyesi</label>
                                                    <select class="form-control" id="privilege" name="privilege">
                                                        <option value="0">0 - Normal Kullanıcı</option>
                                                        <option value="1">1 - Kayıt Yetkilisi</option>
                                                        <option value="2">2 - Yönetici</option>
                                                        <option value="3">3 - Süper Admin</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">Şifre (Opsiyonel)</label>
                                                    <input type="password" class="form-control" id="password" name="password">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="enabled" name="enabled" checked>
                                            <label class="custom-control-label" for="enabled">Aktif</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Personel Bilgileri</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Adı</label>
                                            <input type="text" class="form-control" id="name" name="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Soyadı</label>
                                            <input type="text" class="form-control" id="surname" name="surname">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Departman</label>
                                            <input type="text" class="form-control" id="department" name="department">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Pozisyon</label>
                                            <input type="text" class="form-control" id="position" name="position">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Telefon</label>
                                            <input type="tel" class="form-control" id="phone" name="phone">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">E-posta</label>
                                            <input type="email" class="form-control" id="email" name="email">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">İşe Giriş Tarihi</label>
                                            <input type="date" class="form-control" id="hire_date" name="hire_date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Doğum Tarihi</label>
                                            <input type="date" class="form-control" id="birth_date" name="birth_date">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Adres</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                       <div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0">Maaş Bilgileri</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-12">
                <label for="salary_type">Maaş Türü</label>
                <select class="form-control" id="salary_type" name="salary_type" onchange="toggleSalaryFields()">
                    <option value="fixed">Sabit Maaş</option>
                    <option value="hourly">Saatlik Ücret</option>
                </select>
                <small class="form-text text-muted">Personelin ücret alma şeklini seçin</small>
            </div>
        </div>
        
        <div id="fixed-salary-fields">
            <div class="alert alert-info alert-sm">
                <i class="fas fa-info-circle mr-1"></i>
                <small><strong>Sabit Maaş:</strong> Ayda belirlenen sabit tutar ödenir. Minimum çalışma şartını karşılamazsa kesinti uygulanır.</small>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="fixed_salary">Aylık Sabit Maaş (₺)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₺</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="fixed_salary" name="fixed_salary" value="35000" placeholder="35000.00">
                    </div>
                </div>
            </div>
        </div>
        
        <div id="hourly-salary-fields" style="display: none;">
            <div class="alert alert-warning alert-sm">
                <i class="fas fa-clock mr-1"></i>
                <small><strong>Saatlik Ücret:</strong> Çalışılan saat başına ödeme yapılır. İzinli günler de ücretlendirilir.</small>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="hourly_rate">Saatlik Ücret (₺/saat)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">₺</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control" id="hourly_rate" name="hourly_rate" placeholder="150.00">
                        <div class="input-group-append">
                            <span class="input-group-text">/saat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0 text-secondary">Çalışma Parametreleri</h6>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="daily_work_hours">Günlük Çalışma Saati</label>
                        <div class="input-group">
                            <input type="number" step="0.5" min="0" max="24" class="form-control" id="daily_work_hours" name="daily_work_hours" value="8.0">
                            <div class="input-group-append">
                                <span class="input-group-text">saat</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="overtime_rate">Fazla Mesai Çarpanı</label>
                        <div class="input-group">
                            <input type="number" step="0.1" min="1" max="5" class="form-control" id="overtime_rate" name="overtime_rate" value="1.5">
                            <div class="input-group-append">
                                <span class="input-group-text">x</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="monthly_work_days">Aylık Çalışma Günü</label>
                        <div class="input-group">
                            <input type="number" step="1" min="0" max="31" class="form-control" id="monthly_work_days" name="monthly_work_days" value="22">
                            <div class="input-group-append">
                                <span class="input-group-text">gün</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-calculator mr-1"></i> Maaş Önizlemesi
                        </h6>
                        <div id="salary-preview">
                            <div class="text-center text-muted">
                                <small>Maaş bilgileri girildikçe önizleme burada görünecek</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-success" id="save-new-card">
                        <i class="fas fa-save mr-1"></i> Kaydet
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="alertContainer" style="position: fixed; top: 90px; right: 20px; z-index: 9999; width: 400px;"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <?php if (isset($is_calendar_page) && $is_calendar_page): ?>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script>
    <?php endif; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="/public/js/main.js"></script> </body>
</html>