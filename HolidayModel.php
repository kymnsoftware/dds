<?php
// models/HolidayModel.php

require_once APP_ROOT . '/models/BaseModel.php';

class HolidayModel extends BaseModel {
    public function __construct() {
        parent::__construct('holidays');
    }

    public function getHolidays($year = null) {
        $sql = "SELECT holiday_date FROM {$this->tableName}";
        $params = [];
        if ($year !== null) {
            $sql .= " WHERE YEAR(holiday_date) = :year";
            $params[':year'] = $year;
        }
        $sql .= " ORDER BY holiday_date";
        return Database::fetchAll($sql, $params);
    }

    public function addCommonHolidays() {
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        
        $commonHolidays = [
            [$currentYear . '-01-01', 'Yılbaşı'],
            [$currentYear . '-04-23', '23 Nisan Ulusal Egemenlik ve Çocuk Bayramı'],
            [$currentYear . '-05-01', 'İşçi Bayramı'],
            [$currentYear . '-05-19', '19 Mayıs Atatürk\'ü Anma, Gençlik ve Spor Bayramı'],
            [$currentYear . '-08-30', '30 Ağustos Zafer Bayramı'],
            [$currentYear . '-10-29', '29 Ekim Cumhuriyet Bayramı'],
            
            [$nextYear . '-01-01', 'Yılbaşı'],
            [$nextYear . '-04-23', '23 Nisan Ulusal Egemenlik ve Çocuk Bayramı'],
            [$nextYear . '-05-01', 'İşçi Bayramı'],
            [$nextYear . '-05-19', '19 Mayıs Atatürk\'ü Anma, Gençlik ve Spor Bayramı'],
            [$nextYear . '-08-30', '30 Ağustos Zafer Bayramı'],
            [$nextYear . '-10-29', '29 Ekim Cumhuriyet Bayramı']
        ];
        
        $addedCount = 0;
        $skippedCount = 0;
        
        foreach ($commonHolidays as $holiday) {
            list($date, $name) = $holiday;
            
            // Zaten var mı kontrol et
            $existing = $this->find($date, 'holiday_date');
            if ($existing) {
                $skippedCount++;
            } else {
                $this->create(['holiday_date' => $date, 'name' => $name, 'is_active' => 1]);
                $addedCount++;
            }
        }
        
        return ['success' => true, 'message' => "$addedCount yeni tatil eklendi. $skippedCount tatil zaten mevcut olduğu için atlandı."];
    }
}
?>