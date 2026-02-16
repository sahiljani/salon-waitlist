<?php
require_once __DIR__ . '/Model.php';

class Token extends Model {
    protected $table = 'tokens';
    protected $fillable = ['token_no', 'name', 'phone', 'status', 'date'];

    public function getServing($date) {
        return $this->where(['date' => $date, 'status' => 'SERVING'], 'token_no ASC');
    }

    public function getWaiting($date) {
        return $this->where(['date' => $date, 'status' => 'WAITING'], 'token_no ASC');
    }

    public function getStats($date) {
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'WAITING' THEN 1 ELSE 0 END) as waiting,
                SUM(CASE WHEN status = 'SERVING' THEN 1 ELSE 0 END) as serving,
                SUM(CASE WHEN status = 'DONE' THEN 1 ELSE 0 END) as done,
                SUM(CASE WHEN status = 'NO_SHOW' THEN 1 ELSE 0 END) as noshow
            FROM {$this->table}
            WHERE date = ?
        ");
        $stmt->execute([$date]);
        return $stmt->fetch();
    }

    public function nextTokenNumber($date) {
        $max = $this->max('token_no', ['date' => $date]);
        return ($max ?? 0) + 1;
    }
}
