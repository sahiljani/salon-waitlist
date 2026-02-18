<?php
require_once 'config.php';
require_once 'models/Token.php';

header('Content-Type: application/json');

$pdo = getDB();
$token = new Token($pdo);
$action = $_GET['action'] ?? '';
$today = getToday();

define('MAX_SERVING', 4);

switch ($action) {
    case 'create_token':
        createToken($token, $today);
        break;
    case 'get_queue':
        getQueue($token, $today);
        break;
    case 'next':
        callNext($token, $today);
        break;
    case 'done':
        markDone($token, $today);
        break;
    case 'noshow':
        markNoShow($token, $today);
        break;
    case 'call_specific':
        callSpecific($token, $today);
        break;
    case 'back_to_queue':
        backToQueue($token, $today);
        break;
    case 'stats':
        getStats($token, $today);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function createToken($token, $today) {
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

    $tokenNo = $token->nextTokenNumber($today);

    $token->create([
        'token_no' => $tokenNo,
        'name' => $name,
        'phone' => $phone,
        'date' => $today
    ]);

    echo json_encode([
        'token_no' => $tokenNo,
        'formatted' => formatToken($tokenNo),
        'name' => $name,
        'phone' => $phone
    ]);
}

function getQueue($token, $today) {
    $servingAll = $token->getServing($today);
    $waiting = $token->getWaiting($today);

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

function callNext($token, $today) {
    $count = $token->count(['date' => $today, 'status' => 'SERVING']);

    if ($count >= MAX_SERVING) {
        http_response_code(400);
        echo json_encode(['error' => 'All 4 chairs are occupied. Please complete a customer first (DONE or NO_SHOW)']);
        return;
    }

    $next = $token->first(['date' => $today, 'status' => 'WAITING'], 'token_no ASC');

    if (!$next) {
        http_response_code(400);
        echo json_encode(['error' => 'No waiting customers']);
        return;
    }

    $token->update($next['id'], ['status' => 'SERVING']);

    echo json_encode([
        'token_no' => (int)$next['token_no'],
        'formatted' => formatToken($next['token_no']),
        'name' => $next['name'],
        'phone' => $next['phone']
    ]);
}

function callSpecific($token, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer']);
        return;
    }

    $count = $token->count(['date' => $today, 'status' => 'SERVING']);

    if ($count >= MAX_SERVING) {
        http_response_code(400);
        echo json_encode(['error' => 'All 4 chairs are occupied']);
        return;
    }

    $t = $token->first(['id' => $id, 'date' => $today, 'status' => 'WAITING']);

    if (!$t) {
        http_response_code(400);
        echo json_encode(['error' => 'Token not found or not waiting']);
        return;
    }

    $token->update($id, ['status' => 'SERVING']);

    echo json_encode([
        'token_no' => (int)$t['token_no'],
        'formatted' => formatToken($t['token_no']),
        'name' => $t['name'],
        'phone' => $t['phone']
    ]);
}

function markDone($token, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        $t = $token->first(['id' => $id, 'date' => $today, 'status' => 'SERVING']);
        if (!$t) {
            http_response_code(400);
            echo json_encode(['error' => 'Token not found or not being served']);
            return;
        }
        $token->update($id, ['status' => 'DONE']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer to complete']);
        return;
    }

    echo json_encode(['success' => true]);
}

function markNoShow($token, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if ($id) {
        $t = $token->first(['id' => $id, 'date' => $today, 'status' => 'SERVING']);
        if (!$t) {
            http_response_code(400);
            echo json_encode(['error' => 'Token not found or not being served']);
            return;
        }
        $token->update($id, ['status' => 'NO_SHOW']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer']);
        return;
    }

    echo json_encode(['success' => true]);
}

function backToQueue($token, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Please specify which customer']);
        return;
    }

    $t = $token->first(['id' => $id, 'date' => $today, 'status' => 'SERVING']);

    if (!$t) {
        http_response_code(400);
        echo json_encode(['error' => 'Token not found or not being served']);
        return;
    }

    $newTokenNo = $token->nextTokenNumber($today);
    $token->update($id, ['status' => 'WAITING', 'token_no' => $newTokenNo]);

    echo json_encode(['success' => true, 'name' => $t['name'], 'formatted' => formatToken($newTokenNo)]);
}

function getStats($token, $today) {
    $stats = $token->getStats($today);

    echo json_encode([
        'total' => (int)($stats['total'] ?? 0),
        'waiting' => (int)($stats['waiting'] ?? 0),
        'serving' => (int)($stats['serving'] ?? 0),
        'done' => (int)($stats['done'] ?? 0),
        'noshow' => (int)($stats['noshow'] ?? 0)
    ]);
}
