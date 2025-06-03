<?php
// views/absence/history_partial.php
// $absences, $allAbsenceTypes, $allDepartments değişkenleri AbsenceController tarafından extract edilmiştir.
?>

<div class="mb-4 p-3" style="background-color: #f8f9fa; border-radius: 5px;">
    <h6 class="mb-3"><i class="fas fa-filter mr-1"></i> Filtreleme</h6>
    <div class="row">
        <div class="col-md-3 mb-2">
            <input type="text" class="form-control form-control-sm" id="filter-search" 
                   placeholder="Personel ara..." value="<?php echo sanitize_html($_GET['search'] ?? ''); ?>">
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control form-control-sm" id="filter-department">
                <option value="">Tüm Departmanlar</option>
                <?php foreach ($allDepartments as $dept): ?>
                    <option value="<?php echo sanitize_html($dept['department']); ?>" <?php echo (($_GET['department'] ?? '') == $dept['department']) ? 'selected' : ''; ?>>
                        <?php echo sanitize_html($dept['department']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control form-control-sm" id="filter-absence-type">
                <option value="">Tüm Türler</option>
                <?php foreach ($allAbsenceTypes as $type): ?>
                    <option value="<?php echo sanitize_html($type['id']); ?>" <?php echo (($_GET['absence_type'] ?? '') == $type['id']) ? 'selected' : ''; ?>>
                        <?php echo sanitize_html($type['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 mb-2">
            <input type="date" class="form-control form-control-sm" id="filter-date" 
                   value="<?php echo sanitize_html($_GET['date_filter'] ?? ''); ?>">
        </div>
        <div class="col-md-2 mb-2">
            <select class="form-control form-control-sm" id="filter-status">
                <option value="">Tüm Durumlar</option>
                <option value="justified" <?php echo (($_GET['status_filter'] ?? '') == 'justified') ? 'selected' : ''; ?>>Mazeretli</option>
                <option value="unjustified" <?php echo (($_GET['status_filter'] ?? '') == 'unjustified') ? 'selected' : ''; ?>>Mazeretsiz</option>
            </select>
        </div>
        <div class="col-md-1 mb-2">
            <button type="button" class="btn btn-primary btn-sm btn-block filter-btn">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-12">
            <button type="button" class="btn btn-secondary btn-sm clear-filters-btn">
                <i class="fas fa-eraser mr-1"></i> Filtreleri Temizle
            </button>
        </div>
    </div>
</div>

<?php if (count($absences) > 0): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th width="15%">Personel</th>
                    <th width="12%">Departman</th>
                    <th width="15%">Devamsızlık Türü</th>
                    <th width="10%">Başlangıç</th>
                    <th width="10%">Bitiş</th>
                    <th width="5%">Gün</th>
                    <th width="10%">Durum</th>
                    <th width="10%">Kaydeden</th>
                    <th width="13%">İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($absences as $absence): ?>
                    <tr id="absence-row-<?php echo sanitize_html($absence['id']); ?>">
                        <td><strong><?php echo sanitize_html($absence['name']) . ' ' . sanitize_html($absence['surname']); ?></strong></td>
                        <td><?php echo sanitize_html($absence['department']); ?></td>
                        <td>
                            <span class="badge" style="background-color: <?php echo sanitize_html($absence['color']); ?>; color: white;">
                                <?php echo sanitize_html($absence['absence_type_name']); ?>
                            </span>
                            <?php if ($absence['auto_generated']): ?>
                                <br><small class="text-muted"><i class="fas fa-robot mr-1"></i>Otomatik</small>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('d.m.Y', strtotime($absence['start_date'])); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($absence['end_date'])); ?></td>
                        <td><strong><?php echo sanitize_html($absence['total_days']); ?></strong></td>
                        
                        <td>
                            <?php if ($absence['is_justified']): ?>
                                <span class="absence-status status-justified">Mazeretli</span>
                            <?php else: ?>
                                <span class="absence-status status-unjustified">Mazeretsiz</span>
                            <?php endif; ?>
                        </td>
                        
                        <td><?php echo sanitize_html($absence['created_by_name'] ?: 'Sistem'); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info view-absence" data-id="<?php echo sanitize_html($absence['id']); ?>" title="Detayları Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-warning edit-absence" data-id="<?php echo sanitize_html($absence['id']); ?>" title="Düzenle">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-absence" data-id="<?php echo sanitize_html($absence['id']); ?>" title="Sil">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="mt-3 text-muted">
        <small><i class="fas fa-info-circle mr-1"></i> Toplam <?php echo count($absences); ?> kayıt gösteriliyor (Son 100 kayıt)</small>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle mr-1"></i> Filtrelere uygun devamsızlık kaydı bulunamadı.
    </div>
<?php endif; ?>