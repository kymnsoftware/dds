<?php
// models/BaseModel.php

require_once APP_ROOT . '/core/Database.php';

class BaseModel {
    protected $tableName;
    protected $primaryKey = 'id'; // Varsayılan primary key
    protected $pdo;

    public function __construct($tableName) {
        $this->tableName = $tableName;
        $this->pdo = Database::connect();
    }

    public function getAll($orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->tableName}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        return Database::fetchAll($sql);
    }

    public function find($value, $key = null) {
        $key = $key ?? $this->primaryKey;
        $sql = "SELECT * FROM {$this->tableName} WHERE {$key} = :value";
        return Database::fetch($sql, [':value' => $value]);
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})";
        Database::execute($sql, $data);
        return Database::lastInsertId();
    }

    public function update($value, $data, $key = null) {
        $key = $key ?? $this->primaryKey;
        $setClauses = [];
        foreach ($data as $col => $val) {
            $setClauses[] = "{$col} = :{$col}";
        }
        $setClause = implode(', ', $setClauses);
        $data[':value'] = $value; // Where koşulu için
        $sql = "UPDATE {$this->tableName} SET {$setClause} WHERE {$key} = :value";
        return Database::execute($sql, $data)->rowCount();
    }

    public function delete($value, $key = null) {
        $key = $key ?? $this->primaryKey;
        $sql = "DELETE FROM {$this->tableName} WHERE {$key} = :value";
        return Database::execute($sql, [':value' => $value])->rowCount();
    }

    // Filtreleme ve arama için özel metod
    public function getFiltered($filters = [], $orderBy = 'id DESC', $limit = null) {
        $sql = "SELECT * FROM {$this->tableName} WHERE 1=1";
        $params = [];

        foreach ($filters as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (strpos($key, 'search_') === 0) { // 'search_' ile başlayanlar LIKE araması
                $column = str_replace('search_', '', $key);
                $sql .= " AND ({$column} LIKE :{$key})";
                $params[":{$key}"] = "%{$value}%";
            } else { // Diğer filtreler tam eşleşme
                $sql .= " AND ({$key} = :{$key})";
                $params[":{$key}"] = $value;
            }
        }

        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        return Database::fetchAll($sql, $params);
    }

    // Dinamik olarak tek bir sütun için farklı değerleri çekme
    public function getDistinct($column, $whereClause = '', $params = [], $orderBy = '') {
        $sql = "SELECT DISTINCT {$column} FROM {$this->tableName}";
        if (!empty($whereClause)) {
            $sql .= " WHERE {$whereClause}";
        }
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        } else {
             $sql .= " ORDER BY {$column}"; // Varsayılan sıralama
        }
        return Database::fetchAll($sql, $params);
    }
}
?>