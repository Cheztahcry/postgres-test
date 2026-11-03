<?php
namespace Handlers;
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
use Services\BorrowersInfo;

class ArchivingHandler {
    public function archive_transactions() {
        $borrower_no = trim($_POST['borrower_no'] ?? '');

        try {
            $handler = new BorrowersInfo();
            $handler->archiveTransaction($borrower_no);

            // Removed echo to allow header redirect to work cleanly
            header("Location: ../../index.php");
            exit();
        } catch (\Throwable $e) {
            die("Database Error (" . htmlspecialchars($borrower_no) . "): " . $e->getMessage());
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new ArchivingHandler();
    $handler->archive_transactions();
}