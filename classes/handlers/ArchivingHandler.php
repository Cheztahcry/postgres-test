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

            // Redirect cleanly to index.php
            header("Location: ../../index.php");
            exit();
        } catch (\Throwable $e) {
            die("Database Error (" . htmlspecialchars($borrower_no) . "): " . $e->getMessage());
        }
    }

    /**
     * Additional Week 10 Archive Logs Fetcher
     */
    public function show_archive_logs(): array {
        try {
            $handler = new BorrowersInfo();
            return $handler->w10_get_archive_logs();
        } catch (\Throwable $e) {
            return [];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $handler = new ArchivingHandler();
    $handler->archive_transactions();
}