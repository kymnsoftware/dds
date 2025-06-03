<?php
// models/CardModel.php

require_once APP_ROOT . '/models/BaseModel.php';

class CardModel extends BaseModel {
    public function __construct() {
        parent::__construct('cards');
        $this->primaryKey = 'user_id'; // cards tablosunun primary key'ini user_id olarak ayarla
    }

    // Kullanıcı doğrulama (login için)
    public function authenticateUser($login_name, $password_input) {
        $sql = "SELECT * FROM cards WHERE name = :login_name AND card_number = :password_input AND enabled = 'true'";
        return Database::fetch($sql, [':login_name' => $login_name, ':password_input' => $password_input]);
    }

    // Kartları filtreli çekme (index.php'deki get_cards.php'nin yerini alacak)
    public function getCards($search = '', $department = '') {
        $filters = [];
        if (!empty($search)) {
            $filters['search_name'] = $search;
            $filters['search_surname'] = $search; // Hem name hem surname'de arama
            $filters['search_user_id'] = $search;
            $filters['search_card_number'] = $search;
            $filters['search_department'] = $search;

            // Özel arama koşulu oluştur (LIKE için OR bağlacı ile)
            $sql = "SELECT * FROM {$this->tableName} WHERE (name LIKE :search_name OR surname LIKE :search_surname OR user_id LIKE :search_user_id OR card_number LIKE :search_card_number OR department LIKE :search_department)";
            $params = [
                ':search_name' => "%{$search}%",
                ':search_surname' => "%{$search}%",
                ':search_user_id' => "%{$search}%",
                ':search_card_number' => "%{$search}%",
                ':search_department' => "%{$search}%"
            ];

            if (!empty($department)) {
                $sql .= " AND department = :department";
                $params[':department'] = $department;
            }
            $sql .= " ORDER BY id DESC";
            return Database::fetchAll($sql, $params);

        } else {
            if (!empty($department)) {
                 $filters['department'] = $department;
            }
            return $this->getFiltered($filters, 'id DESC');
        }
    }

    // Sonraki kullanıcı ID'sini al
    public function getNextUserId() {
        $sql = "SELECT MAX(CAST(user_id AS UNSIGNED)) AS max_id FROM {$this->tableName}";
        $result = Database::fetch($sql);
        return ($result['max_id'] > 0) ? $result['max_id'] + 1 : 1;
    }

    // Kullanıcıyı fotoğrafıyla birlikte kaydet/güncelle
    public function saveUser($userData, $photoFile = null) {
        $isUpdate = isset($userData['is_update']) && $userData['is_update'] == true;
        $userId = $userData['user_id'];
        $card_number = $userData['card_number'];

        // Kart numarası veya user_id benzersiz mi kontrol et
        $checkSql = "SELECT user_id, card_number, photo_path FROM {$this->tableName} WHERE user_id = :user_id OR card_number = :card_number";
        $checkParams = [':user_id' => $userId, ':card_number' => $card_number];

        if ($isUpdate) {
            $checkSql .= " AND user_id != :current_user_id"; // Kendi user_id'sini hariç tut
            $checkParams[':current_user_id'] = $userId;
        }

        $existingUser = Database::fetch($checkSql, $checkParams);
        if ($existingUser) {
            if ($existingUser['user_id'] == $userId && !$isUpdate) { // Sadece ekleme durumunda kontrol
                return ['success' => false, 'message' => 'Bu Kullanıcı ID zaten kullanılıyor!'];
            }
            if ($existingUser['card_number'] == $card_number) {
                 return ['success' => false, 'message' => 'Bu Kart Numarası zaten kullanılıyor!'];
            }
        }
        
        $currentPhotoPath = $isUpdate ? ($this->find($userId)['photo_path'] ?? UPLOAD_DIR . 'default-user.png') : UPLOAD_DIR . 'default-user.png';
        $photoPath = $currentPhotoPath;

        // Fotoğraf yüklemesi
        if ($photoFile && $photoFile['error'] == 0) {
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }
            
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $photoFile['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowed)) {
                return ['success' => false, 'message' => 'Sadece JPG, PNG ve GIF formatları kabul edilir!'];
            }
            
            if ($photoFile['size'] > 2097152) { // 2MB
                return ['success' => false, 'message' => 'Dosya boyutu maksimum 2MB olmalıdır!'];
            }

            // Dosya içeriğini de doğrula (gerçekten resim mi?)
            $imageInfo = getimagesize($photoFile['tmp_name']);
            if ($imageInfo === false) {
                 return ['success' => false, 'message' => 'Yüklenen dosya geçerli bir resim değil!'];
            }
            
            $newFilename = 'user_' . $userId . '_' . time() . '.' . $ext;
            $uploadPath = UPLOAD_DIR . $newFilename;
            
            if (move_uploaded_file($photoFile['tmp_name'], $uploadPath)) {
                // Eski fotoğrafı sil (varsayılan dışındaysa)
                if ($currentPhotoPath != UPLOAD_DIR . 'default-user.png' && file_exists($currentPhotoPath)) {
                    unlink($currentPhotoPath);
                }
                $photoPath = $uploadPath;
            } else {
                return ['success' => false, 'message' => 'Fotoğraf yüklenirken bir hata oluştu!'];
            }
        }

        // Güncellenecek/eklenecek veriler
        $data = [
            'card_number' => $card_number,
            'name' => $userData['name'],
            'surname' => $userData['surname'],
            'department' => $userData['department'],
            'position' => $userData['position'],
            'phone' => $userData['phone'],
            'email' => $userData['email'],
            'hire_date' => $userData['hire_date'],
            'birth_date' => $userData['birth_date'],
            'address' => $userData['address'],
            'privilege' => $userData['privilege'],
            'enabled' => $userData['enabled'],
            'photo_path' => $photoPath,
            'salary_type' => $userData['salary_type'] ?? 'fixed',
            'fixed_salary' => $userData['fixed_salary'] ?? 0,
            'hourly_rate' => $userData['hourly_rate'] ?? 0,
            'overtime_rate' => $userData['overtime_rate'] ?? 1.5,
            'daily_work_hours' => $userData['daily_work_hours'] ?? 8.0,
            'monthly_work_days' => $userData['monthly_work_days'] ?? 22,
            'synced_to_device' => 0 // Senkronizasyon gerekecek
        ];

        if (!empty($userData['password'])) {
            $data['password'] = $userData['password'];
        }

        if ($isUpdate) {
            $this->update($userId, $data, 'user_id');
            $message = 'Kullanıcı bilgileri başarıyla güncellendi.';
        } else {
            $data['user_id'] = $userId;
            $this->create($data);
            $message = 'Yeni personel başarıyla kaydedildi.';
        }
        
        // Senkronizasyon komutu ekle
        $commandModel = new CommandModel(); // CommandModel'in de oluşturulması gerekecek
        $commandModel->addCommand($userId, 'sync_user');

        return ['success' => true, 'message' => $message];
    }

    // Kullanıcı silme (fotoğraf ve cihaz senkronizasyonu dahil)
    public function deleteUser($userId, $deleteType = 'db_only') {
        $user = $this->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Silinecek kullanıcı bulunamadı!'];
        }

        // Fotoğrafı sil (varsayılan dışındaysa)
        if (!empty($user['photo_path']) && $user['photo_path'] != UPLOAD_DIR . 'default-user.png' && file_exists($user['photo_path'])) {
            unlink($user['photo_path']);
        }

        // Veritabanından sil
        $this->delete($userId, 'user_id');

        // Cihazdan da silme komutu gönderilecekse
        if ($deleteType == 'both') {
            $commandModel = new CommandModel();
            $commandModel->addCommand($userId, 'delete_user');
            return ['success' => true, 'message' => 'Kullanıcı başarıyla silindi ve cihazdan silme komutu gönderildi.'];
        }
        return ['success' => true, 'message' => 'Kullanıcı başarıyla silindi.'];
    }

    // Tüm kullanıcıları silme
    public function deleteAllUsers($confirmText, $deleteType = 'db_only') {
        if ($confirmText !== 'TÜM KULLANICILARI SİL') {
            return ['success' => false, 'message' => 'Onay metni doğru değil!'];
        }

        // Tüm fotoğraf yollarını al
        $photoPaths = Database::fetchAll("SELECT photo_path FROM {$this->tableName} WHERE photo_path IS NOT NULL AND photo_path != :default_photo", [':default_photo' => UPLOAD_DIR . 'default-user.png']);
        
        // Tüm kullanıcıları veritabanından sil
        Database::execute("DELETE FROM {$this->tableName}");

        // Fotoğrafları sil
        foreach ($photoPaths as $pathRow) {
            if (file_exists($pathRow['photo_path'])) {
                unlink($pathRow['photo_path']);
            }
        }

        // Cihazdan da silme komutu gönderilecekse
        if ($deleteType == 'both') {
            $commandModel = new CommandModel();
            $commandModel->addCommand(null, 'delete_all'); // null user_id tümünü sil komutu için
            return ['success' => true, 'message' => 'Tüm kullanıcılar başarıyla silindi. Cihazdan silme işlemi için komut gönderildi.'];
        }
        return ['success' => true, 'message' => 'Tüm kullanıcılar başarıyla silindi.'];
    }

    // Sadece cihazdan silme
    public function deleteUserFromDeviceOnly($userId) {
        $user = $this->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Kullanıcı bulunamadı!'];
        }

        $commandModel = new CommandModel();
        $commandModel->addCommand($userId, 'delete_from_device');

        return ['success' => true, 'message' => 'Kullanıcı başarıyla cihazdan silme komutu gönderildi. Veritabanında kayıtları durmaya devam edecek.'];
    }

    // Tüm departmanları çekme
    public function getDepartments() {
        return $this->getDistinct('department', "department != ''");
    }

    // Kullanıcı listesi (dropdown'lar için)
    public function getUsersList() {
        return Database::fetchAll("SELECT user_id, name, surname FROM {$this->tableName} WHERE enabled = 'true' ORDER BY name, surname");
    }

    // Dashboard istatistikleri için toplam personel sayısı
    public function getTotalPersonnelCount() {
        return Database::fetchColumn("SELECT COUNT(*) FROM {$this->tableName}");
    }
}
?>