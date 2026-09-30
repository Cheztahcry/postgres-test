<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}
require_once __DIR__ . '/owner_info_class.php';

$userId = $_SESSION['user_id'];
$fname = trim($_POST['fname'] ?? '');
$lname = trim($_POST['lname'] ?? '');
$age = trim($_POST['age'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

try {
    $db = new OwnerInfo();
    $updates = [];
    if ($fname !== '') $updates['fname'] = $fname;
    if ($lname !== '') $updates['lname'] = $lname;
    if ($age !== '') $updates['age'] = $age;
    if ($gender !== '') $updates['gender'] = $gender;
    if ($email !== '') $updates['email'] = $email;
    if ($password !== '') $updates['password_hash'] = password_hash($password, PASSWORD_DEFAULT);

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'No data to update']);
        exit;
    }

    $ok = $db->update_owner($userId, $updates);
    if ($ok) {
        echo json_encode(['success' => true, 'message' => 'Account updated']);
        exit;
    }
    echo json_encode(['success' => false, 'message' => 'Update failed']);
    exit;
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

?>
