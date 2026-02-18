<?php
require_once 'config.php';

header('Content-Type: application/json');

$pdo = getDB();
$action = $_GET['action'] ?? '';
$today = getToday();

define('MAX_SERVING', 4);

switch ($action) {
    case 'create_token':
        createToken($pdo, $today);
        break;
    case 'get_queue':
        getQueue($pdo, $today);
        break;
    case 'next':
        callNext($pdo, $today);
        break;
    case 'done':
        markDone($pdo, $today);
        break;
    case 'noshow':
        markNoShow($pdo, $today);
        break;
    case 'call_specific':
        callSpecific($pdo, $today);
        break;
    case 'back_to_queue':
        backToQueue($pdo, $today);
        break;
    case 'stats':
        getStats($pdo, $today);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function createToken($pdo, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $name = trim($input['name'] ?? '');
    $phone = trim($input['phone'] ?? '');

    if (empty($name)) {
        http_response_code(400);
        echo json_encode(['error' => 'Name is required']);
        return;
    }

    if (empty($phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'Phone number is required']);
        return;
    }

    $stmt = $pdo->prepare("SELECT MAX(token_no) as max_token FROM tokens WHERE date = ?");
    $stmt->execute([$today]);
    $result = $stmt->fetch();
    $tokenNo = ($result['max_token'] ?? 0) + 1;

    $stmt = $pdo->prepare("INSERT INTO tokens (token_no, name, phone, date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$tokenNo, $name, $phone, $today]);

    echo json_encode([
        'token_no' => $tokenNo,
        'formatted' => formatToken($tokenNo),
        'name' => $name,
        'phone' => $phone
    ]);
}

function getQueue($pdo, $today) {
    // Get all currently serving (up to 4)
    $stmt = $pdo->prepare("SELECT id, token_no, name, phone FROM tokens WHERE date = ? AND status = 'SERVING' ORDER BY token_no ASC");
    $stmt->execute([$today]);
    $servingAll = $stmt->fetchAll();

    // Get waiting list
    $stmt = $pdo->prepare("SELECT id, token_no, name, phone FROM tokens WHERE date = ? AND status = 'WAITING' ORDER BY token_no ASC");
    $stmt->execute([$today]);
    $waiting = $stmt->fetchAll();

    $formatRow = function($t) {
        return [
            'id' => (int)$t['id'],
            'token_no' => (int)$t['token_no'],
            'name' => $t['name'],
            'phone' => $t['phone'],
            'formatted' => formatToken($t['token_no'])
        ];
    };

    echo json_encode([
        'serving' => array_map($formatRow, $servingAll),
        'servingCount' => count($servingAll),
        'maxServing' => MAX_SERVING,
        'waiting' => array_map($formatRow, $waiting),
        'nextFive' => array_map($formatRow, array_slice($waiting, 0, 5))
    ]);
}

function callNext($pdo, $today) {
    // Check how many are currently being served
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM tokens WHERE date = ? AND status = 'SERVING'");
    $stmt->execute([$today]);
    $count = (int)$stmt->fetch()['cnt'];

    if ($count >= MAX_SERVING) {
        http_response_code(400);
        echo json_encode(['error' => 'All 4 chairs are occupied. Please complete a customer first (DONE or NO_SHOW)']);
        return;
    }

    // Get next waiting
    $stmt = $pdo->prepare("SELECT id, token_no, name, phone FROM tokens WHERE date = ? AND status = 'WAITING' ORDER BY token_no ASC LIMIT 1");
    $stmt->execute([$today]);
    $next = $stmt->fetch();

    if (!$next) {
        http_response_code(400);
        echo json_encode(['error' => 'No waiting customers']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE tokens SET status = 'SERVING' WHERE id = ?");
    $stmt->execute([$next['id']]);

    echo json_encode([
        'token_no' => (int)$next['token_no'],
        'formatted' => formatToken($next['token_no']),
        'name' => $next['name'],
        'phone' => $next['phone']
    ]);
}

function callSpecific($pdo, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer']);
        return;
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM tokens WHERE date = ? AND status = 'SERVING'");
    $stmt->execute([$today]);
    $count = (int)$stmt->fetch()['cnt'];

    if ($count >= MAX_SERVING) {
        http_response_code(400);
        echo json_encode(['error' => 'All 4 chairs are occupied']);
        return;
    }

    $stmt = $pdo->prepare("SELECT id, token_no, name, phone FROM tokens WHERE id = ? AND date = ? AND status = 'WAITING'");
    $stmt->execute([$id, $today]);
    $token = $stmt->fetch();

    if (!$token) {
        http_response_code(400);
        echo json_encode(['error' => 'Token not found or not waiting']);
        return;
    }

    $stmt = $pdo->prepare("UPDATE tokens SET status = 'SERVING' WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'token_no' => (int)$token['token_no'],
        'formatted' => formatToken($token['token_no']),
        'name' => $token['name'],
        'phone' => $token['phone']
    ]);
}

function markDone($pdo, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        // Mark specific token as done
        $stmt = $pdo->prepare("SELECT id FROM tokens WHERE id = ? AND date = ? AND status = 'SERVING'");
        $stmt->execute([$id, $today]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Token not found or not being served']);
            return;
        }
        $stmt = $pdo->prepare("UPDATE tokens SET status = 'DONE' WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer to complete']);
        return;
    }

    echo json_encode(['success' => true]);
}

function markNoShow($pdo, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("SELECT id FROM tokens WHERE id = ? AND date = ? AND status = 'SERVING'");
        $stmt->execute([$id, $today]);
        if (!$stmt->fetch()) {
            http_response_code(400);
            echo json_encode(['error' => 'Token not found or not being served']);
            return;
        }
        $stmt = $pdo->prepare("UPDATE tokens SET status = 'NO_SHOW' WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer']);
        return;
    }

    echo json_encode(['success' => true]);
}

function backToQueue($pdo, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer']);
        return;
    }

    $stmt = $pdo->prepare("SELECT id, token_no, name FROM tokens WHERE id = ? AND date = ? AND status = 'SERVING'");
    $stmt->execute([$id, $today]);
    $token = $stmt->fetch();

    if (!$token) {
        http_response_code(400);
        echo json_encode(['error' => 'Token not found or not being served']);
        return;
    }

    // Get highest token number so they go to the end
    $stmt = $pdo->prepare("SELECT MAX(token_no) as max_token FROM tokens WHERE date = ?");
    $stmt->execute([$today]);
    $maxToken = (int)($stmt->fetch()['max_token'] ?? 0);
    $newTokenNo = $maxToken + 1;

    $stmt = $pdo->prepare("UPDATE tokens SET status = 'WAITING', token_no = ? WHERE id = ?");
    $stmt->execute([$newTokenNo, $id]);

    echo json_encode(['success' => true, 'name' => $token['name'], 'formatted' => formatToken($newTokenNo)]);
}

function getStats($pdo, $today) {
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN status = 'WAITING' THEN 1 ELSE 0 END) as waiting,
            SUM(CASE WHEN status = 'SERVING' THEN 1 ELSE 0 END) as serving,
            SUM(CASE WHEN status = 'DONE' THEN 1 ELSE 0 END) as done,
            SUM(CASE WHEN status = 'NO_SHOW' THEN 1 ELSE 0 END) as noshow
        FROM tokens
        WHERE date = ?
    ");
    $stmt->execute([$today]);
    $stats = $stmt->fetch();

    echo json_encode([
        'total' => (int)($stats['total'] ?? 0),
        'waiting' => (int)($stats['waiting'] ?? 0),
        'serving' => (int)($stats['serving'] ?? 0),
        'done' => (int)($stats['done'] ?? 0),
        'noshow' => (int)($stats['noshow'] ?? 0)
    ]);
}
