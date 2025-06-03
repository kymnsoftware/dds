// public/js/main.js

$(document).ready(function() {
    // Global değişkenler
    var currentCardScanInterval = null;

    // Alert fonksiyonu
    function showAlert(message, type = 'success') {
        var alertClass = 'alert-' + type;
        var iconClass = type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle';
        
        var alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show shadow-medium" role="alert">
                <i class="fas fa-${iconClass} mr-2"></i>
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        `;
        
        $('#alertContainer').append(alertHtml);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $('#alertContainer .alert:first').alert('close');
        }, 5000);
    }

    // Sidebar navigation
    $('.menu-item').click(function() {
        var target = $(this).data('target');
        
        // Update active menu item
        $('.menu-item').removeClass('active');
        $(this).addClass('active');
        
        // Hide all sections
        $('.content-section').hide();
        
        // Show target section
        $('#' + target).show();
        
        // Load data based on section
        switch(target) {
            case 'dashboard':
                updateDashboard();
                break;
            case 'cards':
                updateCardsTable();
                break;
            case 'attendance':
                updateAttendanceData();
                break;
            case 'absence':
                loadTodayAbsences();
                loadAbsenceStats();
                break;
            case 'leave':
                loadPendingLeaves();
                loadLeaveBalances();
                break;
            case 'salary':
                updateSalaryPersonnelDropdown();
                loadSalaryStats();
                loadSalarySettingsSummary();
                loadSalaryOverview();
                break;
            case 'logs':
                updateLogs();
                break;
            case 'settings':
                refreshUserDropdown();
                loadSystemSettings(); // Yeni eklendi
                break;
        }
    });

    // Kart okutma fonksiyonu
    function startCardScan(targetField) {
        if (currentCardScanInterval) {
            clearInterval(currentCardScanInterval);
        }
        
        $(targetField).siblings('.input-group-append').find('button').html('<span class="spinner-border spinner-border-sm"></span> Bekleniyor...');
        
        currentCardScanInterval = setInterval(function() {
            $.ajax({
                url: '/ajax/api.php?action=getLastCard', // Yeni AJAX endpoint
                dataType: 'json',
                success: function(data) {
                    if (data.success && data.card_number) {
                        clearInterval(currentCardScanInterval);
                        currentCardScanInterval = null;
                        $(targetField).val(data.card_number);
                        $(targetField).siblings('.input-group-append').find('button').text('Okut');
                        showAlert('Kart başarıyla okundu: ' + data.card_number, 'info');
                    }
                }
            });
        }, 2000);
        
        setTimeout(function() {
            if (currentCardScanInterval) {
                clearInterval(currentCardScanInterval);
                currentCardScanInterval = null;
                $(targetField).siblings('.input-group-append').find('button').text('Okut');
                showAlert('Kart okutma zaman aşımına uğradı. Lütfen tekrar deneyin.', 'warning');
            }
        }, 30000);
    }

    // Dashboard güncelleme
    function updateDashboard() {
        $.ajax({
            url: '/ajax/api.php?action=getDashboardData', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                $('#total-personnel').text(data.total_personnel);
                $('#currently-inside').text(data.currently_inside);
                $('#today-entries').text(data.today_entries);
                $('#today-exits').text(data.today_exits);
                $('#recent-activities').html(data.recent_activities);
                $('#recent-scans').html(data.recent_scans);
            }
        });
    }

    // Kartlar tablosunu güncelleme
    function updateCardsTable() {
        var search = $('#search-cards').val();
        var department = $('#filter-department').val();
        $.ajax({
            url: '/ajax/api.php?action=getCards', // Yeni AJAX endpoint
            data: {
                search: search,
                department: department
            },
            success: function(data) {
                $('#cards-table-body').html(data);
            }
        });
    }

    // Giriş-çıkış verilerini güncelleme
    function updateAttendanceData() {
        var search = $('#search-attendance').val();
        var date = $('#date-filter').val();
        var type = $('#type-filter').val();
        $.ajax({
            url: '/ajax/api.php?action=getAttendanceLogs', // Yeni AJAX endpoint
            data: {
                search: search,
                date: date,
                type: type
            },
            success: function(data) {
                $('#attendance-tbody').html(data);
                updateAttendanceStats();
            }
        });
    }

    // Giriş-çıkış istatistiklerini güncelleme
    function updateAttendanceStats() {
        $.ajax({
            url: '/ajax/api.php?action=getAttendanceStats', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                $('#today-entries-att').text(data.entries);
                $('#today-exits-att').text(data.exits);
                $('#currently-inside-att').text(data.inside);
            }
        });
    }

    // Devamsızlık verilerini yükleme
    function loadTodayAbsences() {
        $.ajax({
            url: '/ajax/api.php?action=getTodayAbsences', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#today-absences-container').html(data.html);
                    if (data.count > 0) {
                        $('#absence-count').text(data.count).show();
                    } else {
                        $('#absence-count').hide();
                    }
                } else {
                    $('#today-absences-container').html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            },
            error: function() {
                $('#today-absences-container').html('<div class="alert alert-danger">Veriler yüklenirken bir hata oluştu!</div>');
            }
        });
    }

    function loadAbsenceStats() {
        $.ajax({
            url: '/ajax/api.php?action=getAbsenceStats', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#absence-stats-container').html(data.html);
                } else {
                    $('#absence-stats-container').html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            },
            error: function() {
                $('#absence-stats-container').html('<div class="alert alert-danger">Veriler yüklenirken bir hata oluştu!</div>');
            }
        });
    }

    // İzin verilerini yükleme
    function loadPendingLeaves() {
        $.ajax({
            url: '/ajax/api.php?action=getPendingLeaves', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#pending-leaves-container').html(data.html);
                    if (data.count > 0) {
                        $('#leave-count').text(data.count).show();
                    } else {
                        $('#leave-count').hide();
                    }
                } else {
                    $('#pending-leaves-container').html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            },
            error: function() {
                $('#pending-leaves-container').html('<div class="alert alert-danger">Veriler yüklenirken bir hata oluştu!</div>');
            }
        });
    }

    function loadLeaveBalances() {
        $.ajax({
            url: '/ajax/api.php?action=getLeaveBalances', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#leave-balances-container').html(data.html);
                } else {
                    $('#leave-balances-container').html('<div class="alert alert-danger">' + data.message + '</div>');
                }
            },
            error: function() {
                $('#leave-balances-container').html('<div class="alert alert-danger">Veriler yüklenirken bir hata oluştu!</div>');
            }
        });
    }

    // Maaş verilerini yükleme
    function updateSalaryPersonnelDropdown() {
        $.ajax({
            url: '/ajax/api.php?action=getUsersList', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                var dropdown = $('#quick_user_select');
                dropdown.empty();
                dropdown.append('<option value="">Seçiniz</option>');
                if (data && data.length > 0) {
                    $.each(data, function(i, user) {
                        dropdown.append($('<option></option>').attr('value', user.user_id).text(user.name + ' ' + user.surname));
                    });
                } else {
                    dropdown.append('<option value="" disabled>Kayıtlı personel bulunamadı</option>');
                }
            }
        });
    }

    function loadSalaryStats() {
        $.ajax({
            url: '/ajax/api.php?action=getSalaryStats', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#salary-stats').html(response.html);
                } else {
                    $('#salary-stats').html('<div class="alert alert-danger">İstatistikler yüklenemedi!</div>');
                }
            },
            error: function() {
                $('#salary-stats').html('<div class="alert alert-danger">İstatistikler yüklenirken hata oluştu!</div>');
            }
        });
    }

    function loadSalarySettingsSummary() {
        $.ajax({
            url: '/ajax/api.php?action=getSalarySettingsSummary', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#salary-settings-summary').html(response.html);
                } else {
                    $('#salary-settings-summary').html('<div class="text-muted"><small>Ayarlar yüklenemedi</small></div>');
                }
            },
            error: function() {
                $('#salary-settings-summary').html('<div class="text-muted"><small>Yükleme hatası</small></div>');
            }
        });
    }

    function loadSalaryOverview() {
        $.ajax({
            url: '/ajax/api.php?action=getSalaryOverview', // Yeni AJAX endpoint
            data: {
                month: $('#salary-filter-month').val(),
                department: $('#salary-filter-department').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#salary-overview-table').html(response.html);
                } else {
                    $('#salary-overview-table').html('<div class="alert alert-danger">Veriler yüklenemedi!</div>');
                }
            },
            error: function() {
                $('#salary-overview-table').html('<div class="alert alert-danger">Veriler yüklenirken hata oluştu!</div>');
            }
        });
    }

    // Logları güncelleme
    function updateLogs() {
        var search = $('#search-logs').val();
        var date = $('#date-filter-logs').val();
        $.ajax({
            url: '/ajax/api.php?action=getCardLogs', // Yeni AJAX endpoint
            data: {
                search: search,
                date: date
            },
            success: function(data) {
                $('#logs-table-body').html(data);
            }
        });
    }

    // Kullanıcı listesi güncelleme (delete dropdown için)
    function refreshUserDropdown() {
        $.ajax({
            url: '/ajax/api.php?action=getUsersList', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                var dropdown = $('#user_id_to_delete');
                var deviceOnlyDropdown = $('#device_only_user_id');
                dropdown.empty().append('<option value="">Kullanıcı Seçin</option>');
                deviceOnlyDropdown.empty().append('<option value="">Kullanıcı Seçin</option>');
                if (data && data.length > 0) {
                    $.each(data, function(i, user) {
                        var userName = user.name + ' ' + user.surname;
                        dropdown.append($('<option></option>').attr('value', user.user_id).text(user.user_id + ' - ' + userName));
                        deviceOnlyDropdown.append($('<option></option>').attr('value', user.user_id).text(user.user_id + ' - ' + userName));
                    });
                } else {
                    dropdown.append('<option value="" disabled>Kayıtlı kullanıcı bulunamadı</option>');
                    deviceOnlyDropdown.append('<option value="" disabled>Kayıtlı kullanıcı bulunamadı</option>');
                }
            },
            error: function() {
                $('#delete-message-area').html('<div class="alert alert-danger">Kullanıcı listesi alınırken bir hata oluştu!</div>');
            }
        });
    }

    // Sistem Ayarlarını Yükle (yeni eklendi)
    function loadSystemSettings() {
        $.ajax({
            url: '/ajax/api.php?action=getSystemSettings', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#company_name').val(data.settings.company_name || '');
                    $('#system_title').val(data.settings.system_title || '');
                    $('#auto_sync').val(data.settings.auto_sync || 'enabled');
                    $('#smtp_server').val(data.settings.smtp_server || '');
                    $('#smtp_email').val(data.settings.smtp_email || '');
                    // Şifre alanı güvenlik nedeniyle boş bırakılır veya placeholder gösterilir.
                    $('#smtp_password').val(''); 
                } else {
                    showAlert('Sistem ayarları yüklenirken hata: ' + data.message, 'danger');
                }
            },
            error: function() {
                showAlert('Sistem ayarları yüklenirken bir hata oluştu!', 'danger');
            }
        });
    }

    // Event Handlers
    
    // Arama ve filtreleme
    $('#search-cards, #filter-department').on('keyup change', function() {
        updateCardsTable();
    });

    $('#search-attendance, #date-filter, #type-filter').on('change keyup', function() {
        updateAttendanceData();
    });

    $('#search-logs, #date-filter-logs').on('change keyup', function() {
        updateLogs();
    });

    $('#clear-log-filters').click(function() {
        $('#search-logs').val('');
       // $('#date-filter-logs').val('<?php echo date('Yy-mm-dd'); ?>');
        updateLogs();
    });

    // Quick actions
    $('#quick-add-card, #add-new-card-btn, #new-card-btn').click(function() {
        $('#add-card-form')[0].reset();
        $('#new-user-photo').attr('src', '/public/uploads/default-user.png');
        
        $.ajax({
            url: '/ajax/api.php?action=getNextUserId', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(data) {
                $('#user_id').val(data.next_id);
            }
        });
        
        $('#addCardModal').modal('show');
        toggleSalaryFields(); // Yeni eklenen maaş alanları için
    });

    $('#quick-scan-card, #scan-card-btn').click(function() {
        startCardScan('#card_number');
        $('#addCardModal').modal('show');
        toggleSalaryFields(); // Yeni eklenen maaş alanları için
    });

    $('#quick-reports').click(function() {
        $('.menu-item[data-target="reports"]').click();
    });

    // Kart okutma butonları
    $('#add-scan-card-btn').click(function() {
        startCardScan('#card_number');
    });

    $('#edit-scan-card-btn').click(function() {
        startCardScan('#edit_card_number');
    });

    // Senkronizasyon
    $('#trigger-sync-btn, #sync-cards-btn, #sync-devices-btn').click(function() {
        $.ajax({
            url: '/ajax/api.php?action=triggerSync', // Yeni AJAX endpoint
            success: function(response) {
                showAlert('Senkronizasyon talebi gönderildi. Cihaz uygulaması çalışıyorsa kartlar cihaza aktarılacaktır.');
            },
            error: function() {
                showAlert('Senkronizasyon başlatılırken bir hata oluştu!', 'danger');
            }
        });
    });

    // Yeni personel kaydetme
    $('#save-new-card').click(function() {
        var formData = new FormData($('#add-card-form')[0]);
        $.ajax({
            type: 'POST',
            url: '/ajax/api.php?action=saveCard', // Yeni AJAX endpoint
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert(response.message);
                    $('#addCardModal').modal('hide');
                    updateCardsTable();
                    updateSalaryPersonnelDropdown();
                    refreshUserDropdown();
                } else {
                    $('#add-message-area').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#add-message-area').html('<div class="alert alert-danger">İşlem sırasında bir hata oluştu!</div>');
            }
        });
    });

    // Hızlı maaş hesaplama
    $('#quick-salary-calc').submit(function(e) {
        e.preventDefault();
        var userId = $('#quick_user_select').val();
        var month = $('#quick_month').val();
        
        if (!userId) {
            showAlert('Lütfen bir personel seçin!', 'warning');
            return;
        }
        
        var startDate = month + '-01';
        var endDate = moment(month).endOf('month').format('YYYY-MM-DD');
        
        $('#quick-salary-result').html('<div class="spinner-border spinner-border-sm text-primary"></div>').show();
        
        $.ajax({
            url: '/ajax/api.php?action=calculateSalary', // Yeni AJAX endpoint
            data: {
                user_id: userId,
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var cssClass = response.salary.meets_minimum_requirement ? 'success' : 'warning';
                    var html = '<div class="alert alert-' + cssClass + ' p-2">';
                    html += '<small><strong>Net Maaş:</strong> ' + formatCurrency(response.salary.net_salary) + '<br>';
                    html += '<strong>Devam:</strong> ' + response.attendance.total_attended_days + '/' + response.period.required_work_days + ' gün<br>';
                    html += '<strong>Durum:</strong> ' + (response.salary.meets_minimum_requirement ? 'Şartı Karşılıyor' : 'Şartı Karşılamıyor') + '</small>';
                    html += '</div>';
                    $('#quick-salary-result').html(html);
                } else {
                    $('#quick-salary-result').html('<div class="alert alert-danger p-2"><small>' + response.message + '</small></div>');
                }
            },
            error: function() {
                $('#quick-salary-result').html('<div class="alert alert-danger p-2"><small>Hesaplama hatası!</small></div>');
            }
        });
    });

    // Kullanıcı detaylarını görüntüleme
    $(document).on('click', '.view-details', function() {
        var userId = $(this).data('user-id');
        $.ajax({
            url: '/ajax/api.php?action=getUserDetailsHtml', // Yeni AJAX endpoint
            type: 'GET',
            data: { user_id: userId },
            success: function(response) {
                $('#userDetails').html(response);
                $('#userDetailsModal').modal('show');
                $('#edit-user-from-details').data('user-id', userId);
            },
            error: function() {
                showAlert('Kullanıcı bilgileri alınırken bir hata oluştu!', 'danger');
            }
        });
    });

    // Detay modalından düzenleme moduna geçiş
    $('#edit-user-from-details').click(function() {
        var userId = $(this).data('user-id');
        $('#userDetailsModal').modal('hide');
        loadUserForEdit(userId);
    });

    // Maaş türü değişimi - Yeni personel ekleme
    window.toggleSalaryFields = function() { // Global hale getirildi
        var salaryType = document.getElementById('salary_type').value;
        var fixedFields = document.getElementById('fixed-salary-fields');
        var hourlyFields = document.getElementById('hourly-salary-fields');
        
        if (salaryType === 'fixed') {
            fixedFields.style.display = 'block';
            hourlyFields.style.display = 'none';
            // Saatlik alanları temizle
            document.getElementById('hourly_rate').value = '0';
        } else {
            fixedFields.style.display = 'none';
            hourlyFields.style.display = 'block';
            // Sabit maaş alanını temizle
            document.getElementById('fixed_salary').value = '0';
        }
        updateSalaryPreview();
    }

    // Maaş türü değişimi - Personel düzenleme
    window.toggleEditSalaryFields = function() { // Global hale getirildi
        var salaryType = document.getElementById('edit_salary_type').value;
        var fixedFields = document.getElementById('edit-fixed-salary-fields');
        var hourlyFields = document.getElementById('edit-hourly-salary-fields');
        
        if (salaryType === 'fixed') {
            fixedFields.style.display = 'block';
            hourlyFields.style.display = 'none';
        } else {
            fixedFields.style.display = 'none';
            hourlyFields.style.display = 'block';
        }
    }

    // Maaş önizlemesi güncelleme (Yeni personel ekleme)
    function updateSalaryPreview() {
        var salaryType = document.getElementById('salary_type').value;
        var dailyHours = parseFloat(document.getElementById('daily_work_hours').value) || 8;
        var monthlyDays = parseInt(document.getElementById('monthly_work_days').value) || 22;
        var overtimeRate = parseFloat(document.getElementById('overtime_rate').value) || 1.5;
        
        var previewHtml = '';
        
        if (salaryType === 'fixed') {
            var fixedSalary = parseFloat(document.getElementById('fixed_salary').value) || 0;
            var dailySalary = fixedSalary / 30; // 30 gün standart
            var hourlySalary = dailySalary / dailyHours;
            
            previewHtml = `
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Aylık Maaş:</small><br>
                        <strong>${formatCurrency(fixedSalary)}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Günlük Karşılığı:</small><br>
                        <strong>${formatCurrency(dailySalary)}</strong>
                    </div>
                    <div class="col-6 mt-2">
                        <small class="text-muted">Saatlik Karşılığı:</small><br>
                        <strong>${formatCurrency(hourlySalary)}</strong>
                    </div>
                    <div class="col-6 mt-2">
                        <small class="text-muted">Fazla Mesai/Saat:</small><br>
                        <strong>${formatCurrency(hourlySalary * overtimeRate)}</strong>
                    </div>
                </div>
            `;
        } else {
            var hourlyRate = parseFloat(document.getElementById('hourly_rate').value) || 0;
            var dailyPotential = hourlyRate * dailyHours;
            var monthlyPotential = dailyPotential * monthlyDays;
            var overtimeHourly = hourlyRate * overtimeRate;
            
            previewHtml = `
                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Saatlik Ücret:</small><br>
                        <strong>${formatCurrency(hourlyRate)}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Günlük Potansiyel:</small><br>
                        <strong>${formatCurrency(dailyPotential)}</strong>
                    </div>
                    <div class="col-6 mt-2">
                        <small class="text-muted">Aylık Potansiyel:</small><br>
                        <strong>${formatCurrency(monthlyPotential)}</strong>
                    </div>
                    <div class="col-6 mt-2">
                        <small class="text-muted">Fazla Mesai/Saat:</small><br>
                        <strong>${formatCurrency(overtimeHourly)}</strong>
                    </div>
                </div>
            `;
        }
        
        document.getElementById('salary-preview').innerHTML = previewHtml;
    }

    // Para formatı
    function formatCurrency(amount) {
        return new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: 'TRY',
            minimumFractionDigits: 2
        }).format(amount);
    }
    window.formatCurrency = formatCurrency; // Diğer js dosyaları için global yapıldı

    // Maaş alanları değiştiğinde önizleme güncelle
    $('#fixed_salary, #hourly_rate, #daily_work_hours, #monthly_work_days, #overtime_rate').on('input', function() {
        updateSalaryPreview();
    });
    
    // Düzenleme modalında mevcut maaş hesaplama
    $('#calculate-current-salary').click(function() {
        var userId = $('#edit_user_id').val();
        if (!userId) {
            showAlert('Kullanıcı ID bulunamadı!', 'warning');
            return;
        }
        
        var currentMonth = new Date().toISOString().slice(0, 7); // YYYY-MM
        var startDate = currentMonth + '-01';
        var endDate = moment(currentMonth).endOf('month').format('YYYY-MM-DD');
        
        $('#edit-salary-preview').html('<div class="text-center"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>').show();
        
        $.ajax({
            url: '/ajax/api.php?action=calculateSalary', // Yeni AJAX endpoint
            data: {
                user_id: userId,
                start_date: startDate,
                end_date: endDate
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    var html = '<div class="alert alert-' + (response.salary.meets_minimum_requirement ? 'success' : 'warning') + ' p-2">';
                    html += '<div class="row">';
                    html += '<div class="col-6">';
                    html += '<small><strong>Bu Ay Net Maaş:</strong><br>' + formatCurrency(response.salary.net_salary) + '</small>';
                    html += '</div>';
                    html += '<div class="col-6">';
                    html += '<small><strong>Çalışma Durumu:</strong><br>' + (response.salary.meets_minimum_requirement ? '✓ Şartı Karşılıyor' : '⚠ Eksik Çalışma') + '</small>';
                    html += '</div>';
                    html += '<div class="col-12 mt-2">';
                    html += '<small><strong>Detay:</strong> ' + response.attendance.worked_days + ' gün çalışıldı</small>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    $('#edit-salary-preview').html(html);
                } else {
                    $('#edit-salary-preview').html('<div class="alert alert-danger p-2"><small>' + response.message + '</small></div>');
                }
            },
            error: function() {
                $('#edit-salary-preview').html('<div class="alert alert-danger p-2"><small>Hesaplama hatası!</small></div>');
            }
        });
    });
    
    // İlk yüklemede önizleme
    updateSalaryPreview(); // Sadece add modal için geçerli, edit modal için loadUserForEdit sonrası çağrılmalı
    
    // Kullanıcı düzenleme
    $(document).on('click', '.edit-user', function() {
        var userId = $(this).data('user-id');
        loadUserForEdit(userId);
    });

    // Kullanıcı verilerini düzenleme formuna yükle
    function loadUserForEdit(userId) {
        $.ajax({
            url: '/ajax/api.php?action=getUserData', // Yeni AJAX endpoint
            type: 'GET',
            data: { user_id: userId },
            dataType: 'json',
            success: function(user) {
                // Kişisel bilgiler
                $('#edit_user_id').val(user.user_id);
                $('#edit_card_number').val(user.card_number);
                $('#edit_name').val(user.name);
                $('#edit_surname').val(user.surname);
                $('#edit_department').val(user.department);
                $('#edit_position').val(user.position);
                $('#edit_phone').val(user.phone);
                $('#edit_email').val(user.email);
                $('#edit_hire_date').val(user.hire_date);
                $('#edit_birth_date').val(user.birth_date);
                $('#edit_address').val(user.address);
                $('#edit_privilege').val(user.privilege);
                $('#edit_enabled').prop('checked', user.enabled === 'true');
                
                // Maaş bilgilerini doldur
                $('#edit_salary_type').val(user.salary_type || 'fixed');
                $('#edit_fixed_salary').val(user.fixed_salary || 35000);
                $('#edit_hourly_rate').val(user.hourly_rate || 0);
                $('#edit_overtime_rate').val(user.overtime_rate || 1.5);
                $('#edit_daily_work_hours').val(user.daily_work_hours || 8.0);
                $('#edit_monthly_work_days').val(user.monthly_work_days || 22);
                
                // Fotoğrafı göster
                if (user.photo_path) {
                    $('#edit-user-photo').attr('src', user.photo_path);
                } else {
                    $('#edit-user-photo').attr('src', '/public/uploads/default-user.png');
                }
                
                // Maaş türüne göre alanları göster/gizle
                toggleEditSalaryFields();
                
                // Modalı göster
                $('#editUserModal').modal('show');
            },
            error: function() {
                showAlert('Kullanıcı verileri alınırken bir hata oluştu!', 'danger');
            }
        });
    }

    // Kullanıcı düzenlemesini kaydet
    $('#save-edit-user').click(function() {
        var formData = new FormData($('#edit-user-form')[0]);
        $.ajax({
            type: 'POST',
            url: '/ajax/api.php?action=updateUser', // Yeni AJAX endpoint
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert(response.message);
                    $('#editUserModal').modal('hide');
                    updateCardsTable();
                    updateSalaryPersonnelDropdown();
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Kullanıcı güncellenirken bir hata oluştu!', 'danger');
            }
        });
    });

    // Kullanıcı silme
    $(document).on('click', '.delete-user', function() {
        var userId = $(this).data('user-id');
        if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
            $.ajax({
                type: 'POST',
                url: '/ajax/api.php?action=deleteUser', // Yeni AJAX endpoint
                data: {
                    user_id: userId,
                    delete_type: 'db_only'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert(response.message);
                        updateCardsTable();
                        refreshUserDropdown();
                        updateSalaryPersonnelDropdown();
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

    // Tek kullanıcı silme formu
    $('#single-delete-form').submit(function(e) {
        e.preventDefault();
        if ($('#user_id_to_delete').val() === '') {
            $('#delete-message-area').html('<div class="alert alert-warning">Lütfen silinecek kullanıcıyı seçin.</div>');
            return;
        }
        if (confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')) {
            $.ajax({
                type: 'POST',
                url: '/ajax/api.php?action=deleteUser', // Yeni AJAX endpoint
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#delete-message-area').html('<div class="alert alert-success">' + response.message + '</div>');
                        refreshUserDropdown();
                        updateCardsTable();
                        updateSalaryPersonnelDropdown();
                    } else {
                        $('#delete-message-area').html('<div class="alert alert-danger">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('#delete-message-area').html('<div class="alert alert-danger">Silme işlemi sırasında bir hata oluştu!</div>');
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
                url: '/ajax/api.php?action=deleteDeviceOnly', // Yeni AJAX endpoint
                data: {
                    user_id: userId
                },
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

    // Rapor dışa aktarma (attendance için)
    $('.export-format').click(function(e) {
        e.preventDefault();
        var format = $(this).data('format');
        var search = $('#search-attendance').val();
        var date = $('#date-filter').val();
        var type = $('#type-filter').val();
        window.location.href = '/ajax/api.php?action=exportAttendance&format=' + format +
                              '&search=' + encodeURIComponent(search) +
                              '&date=' + encodeURIComponent(date) +
                              '&type=' + encodeURIComponent(type);
    });

    // Veritabanı yedekleme
    $('#backup-btn').click(function() {
        $.ajax({
            url: '/ajax/api.php?action=backupDatabase', // Yeni AJAX endpoint
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('Veritabanı başarıyla yedeklendi: ' + response.filename);
                } else {
                    showAlert('Veritabanı yedeklenirken bir hata oluştu: ' + response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Veritabanı yedeklenirken bir hata oluştu!', 'danger');
            }
        });
    });

    // Sistem ayarlarını kaydet
    $('#system-settings-form').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/ajax/api.php?action=saveSystemSettings', // Yeni AJAX endpoint
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('Sistem ayarları başarıyla kaydedildi.');
                } else {
                    showAlert('Sistem ayarları kaydedilirken bir hata oluştu: ' + response.message, 'danger');
                }
            },
            error: function() {
                showAlert('Sistem ayarları kaydedilirken bir hata oluştu!', 'danger');
            }
        });
    });

    // Fotoğraf önizleme (Add Modal)
    $('#photo').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#new-user-photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        }
    });

    // Fotoğraf önizleme (Edit Modal)
    $('#edit_photo').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#edit-user-photo').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        }
    });

    // Maaş filtreleme fonksiyonları
    window.filterSalaryOverview = function() { // Global yapıldı
        loadSalaryOverview();
    }

    window.refreshSalaryOverview = function() { // Global yapıldı
        loadSalaryOverview();
    }

    // Sayı formatlama
    function numberFormat(number) {
        return new Intl.NumberFormat('tr-TR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
    }
    window.numberFormat = numberFormat; // Global yapıldı

    // Periyodik güncellemeler (Sadece aktif sekme için)
    setInterval(function() {
        var activeSection = $('.content-section:visible').attr('id');
        switch(activeSection) {
            case 'dashboard':
                updateDashboard();
                break;
            case 'cards':
                updateCardsTable();
                break;
            case 'attendance':
                updateAttendanceData();
                break;
            case 'logs':
                updateLogs();
                break;
            // Diğer bölümlerin canlı güncellemeye ihtiyacı varsa buraya eklenebilir
        }
    }, 30000); // Her 30 saniyede bir güncelle

    // İlk yükleme: Dashboard'u göster ve verilerini yükle
    updateDashboard();
    // Diğer sayfalara geçişte ilk yüklemesi için click eventini tetikle
    $('.menu-item[data-target="dashboard"]').click();
});