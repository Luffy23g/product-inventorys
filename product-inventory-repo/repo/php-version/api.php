<?php
/**
 * api.php
 * Handles AJAX requests: list, add, edit.
 * Data is persisted as JSON in data.json (valid JSON syntax, array of objects).
 */

header('Content-Type: application/json');

$dataFile = __DIR__ . '/data.json';

/**
 * Read all items from the JSON data file.
 */
function readData($file)
{
    if (!file_exists($file)) {
        return [];
    }

    $fp = fopen($file, 'r');
    if (!$fp) {
        return [];
    }

    flock($fp, LOCK_SH);
    $content = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    $data = json_decode($content, true);

    return is_array($data) ? $data : [];
}

/**
 * Write all items back to the JSON data file (pretty printed, valid JSON).
 */
function writeData($file, $data)
{
    $fp = fopen($file, 'c');
    if (!$fp) {
        return false;
    }

    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    return true;
}

/**
 * Validate the incoming product_name / quantity / price fields.
 */
function validateInput($productName, $quantity, $price)
{
    if ($productName === '' || $productName === null) {
        return 'Product name is required.';
    }
    if ($quantity === '' || $quantity === null || !is_numeric($quantity) || (float)$quantity < 0) {
        return 'Quantity in stock must be a non-negative number.';
    }
    if ($price === '' || $price === null || !is_numeric($price) || (float)$price < 0) {
        return 'Price per item must be a non-negative number.';
    }
    return null;
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

switch ($action) {

    case 'list':
        $items = readData($dataFile);

        // Order by datetime submitted (ascending).
        usort($items, function ($a, $b) {
            return strtotime($a['datetime']) <=> strtotime($b['datetime']);
        });

        $grandTotal = 0;
        foreach ($items as $it) {
            $grandTotal += $it['total'];
        }

        echo json_encode([
            'success'     => true,
            'items'       => $items,
            'grand_total' => round($grandTotal, 2),
        ]);
        break;

    case 'add':
        $productName = trim($_POST['product_name'] ?? '');
        $quantity    = $_POST['quantity'] ?? null;
        $price       = $_POST['price'] ?? null;

        $error = validateInput($productName, $quantity, $price);
        if ($error) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }

        $quantity = (float)$quantity;
        $price    = (float)$price;
        $total    = round($quantity * $price, 2);

        $items = readData($dataFile);

        $newItem = [
            'id'           => uniqid('p_', true),
            'product_name' => $productName,
            'quantity'     => $quantity,
            'price'        => $price,
            'datetime'     => date('Y-m-d H:i:s'),
            'total'        => $total,
        ];

        $items[] = $newItem;
        writeData($dataFile, $items);

        echo json_encode(['success' => true, 'item' => $newItem]);
        break;

    case 'edit':
        $id          = $_POST['id'] ?? '';
        $productName = trim($_POST['product_name'] ?? '');
        $quantity    = $_POST['quantity'] ?? null;
        $price       = $_POST['price'] ?? null;

        if ($id === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing item id.']);
            exit;
        }

        $error = validateInput($productName, $quantity, $price);
        if ($error) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }

        $quantity = (float)$quantity;
        $price    = (float)$price;
        $total    = round($quantity * $price, 2);

        $items = readData($dataFile);
        $found = false;

        foreach ($items as &$it) {
            if ($it['id'] === $id) {
                $it['product_name'] = $productName;
                $it['quantity']     = $quantity;
                $it['price']        = $price;
                $it['total']        = $total;
                // Note: original submitted datetime is preserved on edit
                // so the row keeps its position in the datetime ordering.
                $found = true;
                break;
            }
        }
        unset($it);

        if (!$found) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Item not found.']);
            exit;
        }

        writeData($dataFile, $items);

        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
        break;
}
