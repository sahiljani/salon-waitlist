<?php
require_once 'config.php';
require_once 'models/Token.php';
require_once 'auth.php';

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
    case 'get_staff':
        getStaff($pdo);
        break;
    case 'get_services':
        getServices($pdo);
        break;
    case 'create_sale':
        createSale($pdo, $token, $today);
        break;
    case 'get_sale':
        getSale($pdo);
        break;
    case 'daily_sales':
        dailySales($pdo, $today);
        break;
    case 'staff_sales':
        staffSales($pdo, $today);
        break;
    case 'admin_list_staff':
        requireAdminApi();
        adminListStaff($pdo);
        break;
    case 'admin_create_staff':
        requireAdminApi();
        adminCreateStaff($pdo);
        break;
    case 'admin_update_staff':
        requireAdminApi();
        adminUpdateStaff($pdo);
        break;
    case 'admin_list_services':
        requireAdminApi();
        adminListServices($pdo);
        break;
    case 'admin_create_service':
        requireAdminApi();
        adminCreateService($pdo);
        break;
    case 'admin_update_service':
        requireAdminApi();
        adminUpdateService($pdo);
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
    $saleId = $input['sale_id'] ?? null;

    if ($id) {
        $t = $token->first(['id' => $id, 'date' => $today, 'status' => 'SERVING']);
        if (!$t) {
            http_response_code(400);
            echo json_encode(['error' => 'Token not found or not being served']);
            return;
        }

        if (!$saleId) {
            http_response_code(400);
            echo json_encode(['error' => 'Sale required before complete']);
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

function getStaff($pdo) {
    $stmt = $pdo->query("SELECT id, name, icon FROM staff WHERE is_active = 1 ORDER BY id ASC");
    echo json_encode(['staff' => $stmt->fetchAll()]);
}

function getServices($pdo) {
    $stmt = $pdo->query("SELECT id, name, price, icon FROM services WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");
    echo json_encode(['services' => $stmt->fetchAll()]);
}

function createSale($pdo, $token, $today) {
    $input = json_decode(file_get_contents('php://input'), true);
    $tokenId = (int)($input['token_id'] ?? 0);
    $staffId = (int)($input['staff_id'] ?? 0);
    $items = $input['items'] ?? [];
    $discount = (float)($input['discount'] ?? 0);
    $tax = (float)($input['tax'] ?? 0);
    $paymentMethod = strtoupper(trim($input['payment_method'] ?? 'CASH'));

    if (!$tokenId || !$staffId || empty($items)) {
        http_response_code(400);
        echo json_encode(['error' => 'token, staff and items required']);
        return;
    }

    $tokenRow = $token->first(['id' => $tokenId, 'date' => $today, 'status' => 'SERVING']);
    if (!$tokenRow) {
        http_response_code(400);
        echo json_encode(['error' => 'Token not serving']);
        return;
    }

    $validMethods = ['CASH', 'UPI', 'CARD'];
    if (!in_array($paymentMethod, $validMethods, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid payment method']);
        return;
    }

    $subtotal = 0;
    $normalizedItems = [];
    foreach ($items as $item) {
        $serviceId = isset($item['service_id']) ? (int)$item['service_id'] : null;
        $qty = max(1, (int)($item['qty'] ?? 1));
        $unitPrice = (float)($item['unit_price'] ?? 0);
        $itemName = trim($item['item_name'] ?? '');

        if ($serviceId) {
            $stmt = $pdo->prepare("SELECT name, price FROM services WHERE id = ? AND is_active = 1");
            $stmt->execute([$serviceId]);
            $svc = $stmt->fetch();
            if (!$svc) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid service']);
                return;
            }
            $itemName = $svc['name'];
            $unitPrice = (float)$svc['price'];
        } elseif ($itemName === '' || $unitPrice <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Custom item requires name and amount']);
            return;
        }

        $amount = $unitPrice * $qty;
        $subtotal += $amount;
        $normalizedItems[] = [
            'service_id' => $serviceId,
            'item_name' => $itemName,
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'amount' => $amount,
            'is_custom' => $serviceId ? 0 : 1
        ];
    }

    $discount = max(0, $discount);
    $tax = max(0, $tax);
    $total = max(0, $subtotal - $discount + $tax);

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO sales (token_id, staff_id, subtotal, discount, tax, total, payment_method, sale_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$tokenId, $staffId, $subtotal, $discount, $tax, $total, $paymentMethod, $today]);
        $saleId = (int)$pdo->lastInsertId();

        $itemStmt = $pdo->prepare("INSERT INTO sale_items (sale_id, service_id, item_name, qty, unit_price, amount, is_custom) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($normalizedItems as $line) {
            $itemStmt->execute([
                $saleId,
                $line['service_id'],
                $line['item_name'],
                $line['qty'],
                $line['unit_price'],
                $line['amount'],
                $line['is_custom']
            ]);
        }

        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        http_response_code(500);
        echo json_encode(['error' => 'Failed to create sale']);
        return;
    }

    echo json_encode([
        'sale_id' => $saleId,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'tax' => $tax,
        'total' => $total
    ]);
}

function getSale($pdo) {
    $saleId = (int)($_GET['sale_id'] ?? 0);
    if (!$saleId) {
        http_response_code(400);
        echo json_encode(['error' => 'sale_id required']);
        return;
    }

    $stmt = $pdo->prepare("SELECT s.*, st.name AS staff_name, t.name AS customer_name, t.phone AS customer_phone, t.token_no
        FROM sales s
        JOIN staff st ON st.id = s.staff_id
        JOIN tokens t ON t.id = s.token_id
        WHERE s.id = ?");
    $stmt->execute([$saleId]);
    $sale = $stmt->fetch();

    if (!$sale) {
        http_response_code(404);
        echo json_encode(['error' => 'Sale not found']);
        return;
    }

    $itemStmt = $pdo->prepare("SELECT id, sale_id, service_id, item_name, qty, unit_price, amount, is_custom FROM sale_items WHERE sale_id = ? ORDER BY id ASC");
    $itemStmt->execute([$saleId]);
    echo json_encode(['sale' => $sale, 'items' => $itemStmt->fetchAll()]);
}

function dailySales($pdo, $today) {
    $stmt = $pdo->prepare("SELECT COUNT(*) AS bills, COALESCE(SUM(total),0) AS total_sales, COALESCE(SUM(discount),0) AS total_discount, COALESCE(SUM(tax),0) AS total_tax FROM sales WHERE sale_date = ?");
    $stmt->execute([$today]);
    $summary = $stmt->fetch();

    $serviceStmt = $pdo->prepare("SELECT si.item_name, SUM(si.qty) AS qty, SUM(si.amount) AS amount
        FROM sale_items si
        JOIN sales s ON s.id = si.sale_id
        WHERE s.sale_date = ?
        GROUP BY si.item_name
        ORDER BY amount DESC");
    $serviceStmt->execute([$today]);

    echo json_encode(['summary' => $summary, 'by_service' => $serviceStmt->fetchAll()]);
}

function staffSales($pdo, $today) {
    $stmt = $pdo->prepare("SELECT st.id, st.name, st.icon, COUNT(s.id) AS bills, COALESCE(SUM(s.total),0) AS total_sales
        FROM staff st
        LEFT JOIN sales s ON s.staff_id = st.id AND s.sale_date = ?
        WHERE st.is_active = 1
        GROUP BY st.id, st.name, st.icon
        ORDER BY total_sales DESC, st.name ASC");
    $stmt->execute([$today]);

    echo json_encode(['by_staff' => $stmt->fetchAll()]);
}

function readJsonInput() {
    return json_decode(file_get_contents('php://input'), true) ?? [];
}

function adminListStaff($pdo) {
    $stmt = $pdo->query("SELECT id, name, icon, is_active FROM staff ORDER BY id ASC");
    echo json_encode(['staff' => $stmt->fetchAll()]);
}

function adminCreateStaff($pdo) {
    $input = readJsonInput();
    $name = trim($input['name'] ?? '');
    $icon = trim($input['icon'] ?? '👤');
    if ($name === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Staff name required']);
        return;
    }
    $stmt = $pdo->prepare("INSERT INTO staff (name, icon, is_active) VALUES (?, ?, 1)");
    $stmt->execute([$name, $icon ?: '👤']);
    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
}

function adminUpdateStaff($pdo) {
    $input = readJsonInput();
    $id = (int)($input['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Staff id required']);
        return;
    }

    $fields = [];
    $values = [];
    if (array_key_exists('name', $input)) {
        $name = trim((string)$input['name']);
        if ($name === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Name cannot be empty']);
            return;
        }
        $fields[] = 'name = ?';
        $values[] = $name;
    }
    if (array_key_exists('icon', $input)) {
        $fields[] = 'icon = ?';
        $values[] = trim((string)$input['icon']) ?: '👤';
    }
    if (array_key_exists('is_active', $input)) {
        $fields[] = 'is_active = ?';
        $values[] = (int)((bool)$input['is_active']);
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }

    $values[] = $id;
    $sql = "UPDATE staff SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    echo json_encode(['success' => true]);
}

function adminListServices($pdo) {
    $stmt = $pdo->query("SELECT id, name, price, icon, sort_order, is_active FROM services ORDER BY sort_order ASC, id ASC");
    echo json_encode(['services' => $stmt->fetchAll()]);
}

function adminCreateService($pdo) {
    $input = readJsonInput();
    $name = trim($input['name'] ?? '');
    $price = (float)($input['price'] ?? -1);
    $icon = trim($input['icon'] ?? '✂️');
    if ($name === '' || $price < 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Service name and valid price required']);
        return;
    }

    $sortStmt = $pdo->query("SELECT COALESCE(MAX(sort_order), 0) + 1 AS next_sort FROM services");
    $nextSort = (int)$sortStmt->fetch()['next_sort'];

    $stmt = $pdo->prepare("INSERT INTO services (name, price, icon, sort_order, is_active) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([$name, $price, $icon ?: '✂️', $nextSort]);
    echo json_encode(['success' => true, 'id' => (int)$pdo->lastInsertId()]);
}

function adminUpdateService($pdo) {
    $input = readJsonInput();
    $id = (int)($input['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'Service id required']);
        return;
    }

    $fields = [];
    $values = [];
    if (array_key_exists('name', $input)) {
        $name = trim((string)$input['name']);
        if ($name === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Name cannot be empty']);
            return;
        }
        $fields[] = 'name = ?';
        $values[] = $name;
    }
    if (array_key_exists('price', $input)) {
        $price = (float)$input['price'];
        if ($price < 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Price must be >= 0']);
            return;
        }
        $fields[] = 'price = ?';
        $values[] = $price;
    }
    if (array_key_exists('icon', $input)) {
        $fields[] = 'icon = ?';
        $values[] = trim((string)$input['icon']) ?: '✂️';
    }
    if (array_key_exists('is_active', $input)) {
        $fields[] = 'is_active = ?';
        $values[] = (int)((bool)$input['is_active']);
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'No fields to update']);
        return;
    }

    $values[] = $id;
    $sql = "UPDATE services SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    echo json_encode(['success' => true]);
}

