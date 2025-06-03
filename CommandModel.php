<?php
// models/CommandModel.php

require_once APP_ROOT . '/models/BaseModel.php';

class CommandModel extends BaseModel {
    public function __construct() {
        parent::__construct('commands');
    }

    public function addCommand($userId, $commandType) {
        $data = [
            'command_type' => $commandType,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        if ($userId !== null) {
            $data['user_id'] = $userId;
        }
        return $this->create($data);
    }

    public function triggerSyncAll() {
        // Tüm kartları senkronizasyon gereksinimi olarak işaretle
        Database::execute("UPDATE cards SET synced_to_device = 0 WHERE 1");
        return $this->addCommand(null, 'sync_all');
    }
}
?>