<?php

class Model {
    protected $pdo;
    protected $table;
    protected $fillable = [];

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function where($conditions, $orderBy = null) {
        $clauses = [];
        $values = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = "$col = ?";
            $values[] = $val;
        }
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $clauses);
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll();
    }

    public function first($conditions, $orderBy = null) {
        $clauses = [];
        $values = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = "$col = ?";
            $values[] = $val;
        }
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $clauses);
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        $sql .= " LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetch();
    }

    public function create($data) {
        $filtered = array_intersect_key($data, array_flip($this->fillable));
        $cols = implode(', ', array_keys($filtered));
        $placeholders = implode(', ', array_fill(0, count($filtered), '?'));
        $sql = "INSERT INTO {$this->table} ($cols) VALUES ($placeholders)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($filtered));
        return $this->pdo->lastInsertId();
    }

    public function update($id, $data) {
        $filtered = array_intersect_key($data, array_flip($this->fillable));
        $sets = [];
        $values = [];
        foreach ($filtered as $col => $val) {
            $sets[] = "$col = ?";
            $values[] = $val;
        }
        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $sets) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return $stmt->rowCount();
    }

    public function count($conditions) {
        $clauses = [];
        $values = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = "$col = ?";
            $values[] = $val;
        }
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} WHERE " . implode(' AND ', $clauses);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        return (int)$stmt->fetch()['cnt'];
    }

    public function max($column, $conditions) {
        $clauses = [];
        $values = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = "$col = ?";
            $values[] = $val;
        }
        $sql = "SELECT MAX($column) as max_val FROM {$this->table} WHERE " . implode(' AND ', $clauses);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
        $result = $stmt->fetch();
        return $result['max_val'];
    }
}
