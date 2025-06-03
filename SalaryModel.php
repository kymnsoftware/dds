<?php
// models/SalaryModel.php

class SalaryModel extends BaseModel {
    private $holidayModel;
    private $settingModel;
    private $attendanceModel;
    private $leaveModel;

    public function __construct() {
        parent::__construct('cards'); // Maaş bilgileri cards tablosunda tutuluyor
        $this->holidayModel = new HolidayModel();
        $this->settingModel = new SettingModel();
        $this->attendanceModel = new AttendanceModel();
        $this->leaveModel = new LeaveModel();
    }

    // Gelişmiş maaş hesaplama fonksiyonu
    public function calculateSalary($userId, $startDate, $endDate) {
        $employee = $this->find($userId); // cards tablosundan kullanıcıyı bul
        
        if (!$employee) {
            return [
                'success' => false,
                'message' => 'Kullanıcı bulunamadı.'
            ];
        }
        
        // Maaş türünü kontrol et ve varsayılan değerleri ayarla
        $salaryType = $employee['salary_type'] ?? 'fixed';
        if (empty($salaryType)) {
            $salaryType = ($employee['hourly_rate'] > 0) ? 'hourly' : 'fixed';
        }
        
        $fixedSalary = floatval($employee['fixed_salary']) ?: 35000;
        $hourlyRate = floatval($employee['hourly_rate']) ?: 0;
        $overtimeRate = floatval($employee['overtime_rate']) ?: 1.5;
        $dailyWorkHours = floatval($employee['daily_work_hours']) ?: 8;
        
        // Sistem ayarlarını al
        $settings = $this->settingModel->getSalarySettings(); // SettingModel'den maaş ayarlarını çeken metod
        $minimumWorkDays = $settings['salary_minimum_work_days'] ?? 20;
        $minimumWorkRate = $settings['salary_minimum_work_rate'] ?? 90;
        $minimumType = $settings['salary_minimum_type'] ?? 'percentage';
        $excludeWeekends = ($settings['salary_exclude_weekends'] ?? 'true') === 'true';
        $excludeHolidays = ($settings['salary_exclude_holidays'] ?? 'true') === 'true';
        
        // Tarih aralığı bilgisi
        $periodStartDate = new DateTime($startDate);
        $periodEndDate = new DateTime($endDate);
        
        // Ay başı ve ay sonu tarihlerini belirle
        $monthStart = new DateTime($periodStartDate->format('Y-m-01'));
        $monthEnd = new DateTime($periodEndDate->format('Y-m-t'));
        
        // Takvim gün sayısı
        $interval = $periodStartDate->diff($periodEndDate);
        $calendarDays = $interval->days + 1; // Başlangıç ve bitiş dahil
        
        // İş günlerini hesapla
        $totalWorkDays = $this->calculateWorkDays($monthStart, $monthEnd, $excludeWeekends, $excludeHolidays);
        
        // Minimum çalışma şartını hesapla
        if ($minimumType === 'percentage') {
            $requiredWorkDays = ceil($totalWorkDays * ($minimumWorkRate / 100));
        } else {
            $requiredWorkDays = $minimumWorkDays;
        }
        
        // Çalışılan günleri ve saatleri al
        $workDetails = $this->attendanceModel->getWorkedDetails($userId, $startDate, $endDate, $dailyWorkHours);
        $workedDays = $workDetails['total_days'];
        $totalWorkedHours = $workDetails['total_hours'];
        $totalNormalHours = $workDetails['normal_hours'];
        $totalOvertimeHours = $workDetails['overtime_hours'];
        
        // İzinli günleri al
        $approvedLeaveDays = $this->leaveModel->getApprovedLeaveDays($userId, $startDate, $endDate);
        
        // Toplam devam edilen gün
        $totalAttendedDays = $workedDays + $approvedLeaveDays;
        
        // Maaş hesaplaması - Maaş türüne göre
        $regularSalary = 0;
        $overtimeSalary = 0;
        $leaveSalary = 0;
        $deductionAmount = 0;
        $netSalary = 0;
        $meetsRequirement = false;
        
        if ($salaryType === 'fixed') {
            // SABİT MAAŞ HESAPLAMASI
            $regularSalary = $fixedSalary;
            
            // Minimum şartı kontrol et
            $meetsRequirement = $totalAttendedDays >= $requiredWorkDays;
            
            // Minimum şartı karşılamıyor mu kesinti uygula
            if (!$meetsRequirement) {
                $missingDays = $requiredWorkDays - $totalAttendedDays;
                // Kesinti sadece normal iş günleri üzerinden yapılır, izinli günler etkilemez.
                // Eğer izinli günler de tam maaşa sayılmıyorsa bu mantık değişebilir.
                $dailyDeduction = $fixedSalary / $totalWorkDays; // Aylık iş günü üzerinden günlük kesinti
                $deductionAmount = $missingDays * $dailyDeduction;
            }
            
            // Fazla mesai hesaplama (sabit maaşlı için)
            if ($totalOvertimeHours > 0) {
                $dailySalary = $fixedSalary / 30; // Standart 30 gün üzerinden günlük maaş
                $hourlySalary = $dailySalary / $dailyWorkHours; // Günlük çalışma saatine göre saatlik ücret
                $overtimeSalary = $totalOvertimeHours * $hourlySalary * $overtimeRate;
            }
            
            $netSalary = $regularSalary + $overtimeSalary - $deductionAmount;
            
        } else { // salaryType === 'hourly'
            // SAATLİK ÜCRET HESAPLAMASI
            if ($hourlyRate <= 0) {
                return [
                    'success' => false,
                    'message' => 'Saatlik ücret tanımlanmamış!'
                ];
            }
            
            // Çalışılan saatlere göre normal ücret
            $regularSalary = $totalNormalHours * $hourlyRate;
            $overtimeSalary = $totalOvertimeHours * $hourlyRate * $overtimeRate;
            
            // İzinli günler için ücret (izinli günler de tam gün çalışılmış gibi sayılır)
            $leaveHours = $approvedLeaveDays * $dailyWorkHours;
            $leaveSalary = $leaveHours * $hourlyRate;
            
            // Saatlik çalışanlarda "minimum çalışma şartı" yerine "eksik çalışma saati" mantığı
            // Ancak sistem ayarlarındaki "minimum çalışma günü" saatlik çalışanlar için de bir hedef olabilir.
            // Burada, maaşın sadece çalışılan saat ve izinli saatlere göre hesaplandığını varsayıyoruz.
            // Eksik çalışma için ayrıca bir kesinti mekanizması uygulanmaz.
            $meetsRequirement = true; // Saatlikte bu check direkt geçerli kabul edilebilir, kesinti mekanizması farklıdır.

            $netSalary = $regularSalary + $overtimeSalary + $leaveSalary;
        }
        
        // Devamsızlık detaylarını al
        $absenceDetails = $this->attendanceModel->getAbsenceDetailsForUser($userId, $startDate, $endDate);
        
        return [
            'success' => true,
            'employee' => [
                'id' => $employee['user_id'],
                'name' => $employee['name'] . ' ' . $employee['surname'],
                'department' => $employee['department'],
                'position' => $employee['position'],
                'salary_type' => $salaryType
            ],
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'calendar_days' => $calendarDays,
                'total_work_days' => $totalWorkDays, // Dönemdeki iş günü (haftasonu/tatil hariç)
                'required_work_days' => $requiredWorkDays // Minimum çalışma şartı
            ],
            'attendance' => [
                'worked_days' => $workedDays,
                'worked_hours' => round($totalWorkedHours, 2),
                'normal_hours' => round($totalNormalHours, 2),
                'overtime_hours' => round($totalOvertimeHours, 2),
                'approved_leave_days' => $approvedLeaveDays,
                'total_attended_days' => $totalAttendedDays, // Çalışılan + İzinli günler
                'missing_days' => max(0, $requiredWorkDays - $totalAttendedDays), // Eksik çalışma günü
                'attendance_rate' => ($totalWorkDays > 0) ? round(($totalAttendedDays / $totalWorkDays) * 100, 2) : 100
            ],
            'salary' => [
                'base_salary' => $fixedSalary, // Sabit ise bu, saatlik ise 0
                'hourly_rate' => $hourlyRate, // Saatlik ise bu, sabit ise 0
                'overtime_rate' => $overtimeRate,
                'daily_work_hours' => $dailyWorkHours,
                'monthly_work_days' => $employee['monthly_work_days'] ?? 22, // Karttan gelen ayarlar
                'regular_salary' => round($regularSalary, 2),
                'overtime_salary' => round($overtimeSalary, 2),
                'leave_salary' => round($leaveSalary, 2),
                'deduction_amount' => round($deductionAmount, 2),
                'total_salary' => round($regularSalary + $overtimeSalary + $leaveSalary, 2), // Toplam ham maaş (kesinti öncesi)
                'net_salary' => round($netSalary, 2),
                'meets_minimum_requirement' => $meetsRequirement
            ],
            'daily_details' => $workDetails['details'] // Günlük giriş-çıkış detayları
        ];
    }

    // İş günlerini hesapla (Haftasonu ve Tatil hariç)
    private function calculateWorkDays($startDate, $endDate, $excludeWeekends, $excludeHolidays) {
        $workDays = 0;
        $current = clone $startDate;
        
        $holidays = [];
        if ($excludeHolidays) {
            // İzinli günleri yalnızca belirtilen yıl için çekmek daha verimli
            $holidaysData = $this->holidayModel->getHolidays($startDate->format('Y'));
            $holidays = array_column($holidaysData, 'holiday_date');
        }
        
        while ($current <= $endDate) {
            $isWorkDay = true;
            
            // Hafta sonu kontrolü
            if ($excludeWeekends && in_array($current->format('w'), [0, 6])) { // 0: Pazar, 6: Cumartesi
                $isWorkDay = false;
            }
            
            // Resmi tatil kontrolü
            if ($excludeHolidays && in_array($current->format('Y-m-d'), $holidays)) {
                $isWorkDay = false;
            }
            
            if ($isWorkDay) {
                $workDays++;
            }
            
            $current->modify('+1 day');
        }
        
        return $workDays;
    }

    // Maaş istatistiklerini getir
    public function getSalaryStats() {
        // Toplam personel sayısı (aktif kartlar)
        $totalEmployees = Database::fetchColumn("SELECT COUNT(*) FROM {$this->tableName} WHERE enabled = 'true'");
        
        // Sabit maaş ortalaması
        $avgSalary = Database::fetchColumn("SELECT AVG(fixed_salary) FROM {$this->tableName} WHERE enabled = 'true' AND fixed_salary > 0");
        
        // En yüksek maaş
        $maxSalary = Database::fetchColumn("SELECT MAX(fixed_salary) FROM {$this->tableName} WHERE enabled = 'true'");
        
        // En düşük maaş
        $minSalary = Database::fetchColumn("SELECT MIN(fixed_salary) FROM {$this->tableName} WHERE enabled = 'true' AND fixed_salary > 0");
        
        return [
            'total_employees' => $totalEmployees ?: 0,
            'avg_salary' => $avgSalary ?: 0,
            'max_salary' => $maxSalary ?: 0,
            'min_salary' => $minSalary ?: 0
        ];
    }

    // Personel maaş ayarlarını güncelle
    public function updateEmployeeSalarySettings($userId, $data) {
        // Sadece ilgili maaş ayarlarını güncelle
        $updateData = [
            'salary_type' => $data['salary_type'] ?? 'fixed',
            'fixed_salary' => $data['fixed_salary'] ?? 0,
            'hourly_rate' => $data['hourly_rate'] ?? 0,
            'overtime_rate' => $data['overtime_rate'] ?? 1.5,
            'daily_work_hours' => $data['daily_work_hours'] ?? 8.0,
            'monthly_work_days' => $data['monthly_work_days'] ?? 22
        ];

        // Maaş türüne göre değerleri sıfırla
        if ($updateData['salary_type'] === 'fixed') {
            $updateData['hourly_rate'] = 0;
        } else {
            $updateData['fixed_salary'] = 0;
        }
        
        return $this->update($userId, $updateData, 'user_id');
    }

    // Toplu maaş hesaplama (belirli bir departman için tüm kullanıcılar)
    public function calculateBulkSalaries($startDate, $endDate, $department = '') {
        $sql = "SELECT user_id, name, surname, department, fixed_salary, hourly_rate, salary_type, overtime_rate, daily_work_hours, monthly_work_days FROM cards WHERE enabled = 'true'";
        $params = [];
        
        if (!empty($department)) {
            $sql .= " AND department = :department";
            $params[':department'] = $department;
        }
        $sql .= " ORDER BY name, surname";
        
        $users = Database::fetchAll($sql, $params);
        
        $results = [];
        foreach ($users as $user) {
            $calculation = $this->calculateSalary($user['user_id'], $startDate, $endDate);
            if ($calculation['success']) {
                $results[] = $calculation;
            }
        }
        return $results;
    }
}