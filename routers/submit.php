<?php
require_once dirname(__DIR__, 1) . '/vendor/autoload.php';
try {
    if (class_exists('Handlers\StudentInfoHandler')) {
    echo "Autoloader woring! Class found.";
    $handler = new \Handlers\StudentInfoHandler();
    echo $handler->info_insert($_POST);
    }
    else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid or missing action']);
    }
}catch (PDOException $e) {
        error_log("Database: " . $e->getMessage());
        die("Something went wrong. Please check back later.");
}

?>