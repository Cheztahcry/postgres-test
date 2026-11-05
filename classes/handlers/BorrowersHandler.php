<?php
namespace Handlers;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Services\BorrowersInfo;

class BorrowersHandler {

    public function insert_all_transactions(array $input) {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                
                $first_name    = trim($input['first_name'] ?? "");
                $last_name     = trim($input['last_name'] ?? "");
                $borrower_type = $input['borrower_type'] ?? null;

                $equipment_borrowed = $input['equipment_id'] ?? null;
                $equipment_quantity = $input['quantity'] ?? null;

                $rawDate = !empty($input['due_date']) ? trim($input['due_date']) : null;
                $dueDate = null;

                if ($rawDate) {
                    $parsed = \DateTime::createFromFormat('Y-m-d', $rawDate);
                    if ($parsed && $parsed->format('Y-m-d') === $rawDate) {
                        $dueDate = $rawDate;
                    } else {
                        die("Invalid date format provided.");
                    }
                }

                // Generate borrower_no
                $borrower_no = "BR-" . str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

                $borrowers = [ 
                    "first_name"    => $first_name,
                    "last_name"     => $last_name,
                    "borrower_type" => $borrower_type,
                    "borrower_no"   => $borrower_no,
                    "due_date"      => $dueDate
                ];

                $borrow_items = [ 
                    "equipment_id"  => $equipment_borrowed,
                    "quantity"      => $equipment_quantity,
                ];

                $borrower = new BorrowersInfo();
                $borrower->insert_info($borrowers, $borrow_items);
                header("Location: ../../index.php");
                exit();
            }
        } catch (\Throwable $e) {
            die("Database Error: " . $e->getMessage());
        }
    }

    /**
     * Additional Borrower Update Handler
     */
    public function update_borrower(array $input): void {
        try {
            $id           = (int) ($input['borrower_id'] ?? 0);
            $borrower_no  = trim($input['borrower_no'] ?? '');
            $full_name    = trim($input['full_name'] ?? '');
            $borrower_type = trim($input['borrower_type'] ?? '');

            $service = new BorrowersInfo();
            $service->crud_update_borrower($id, $borrower_no, $full_name, $borrower_type);
            header("Location: ../../index.php");
            exit();
        } catch (\Throwable $e) {
            die("Update Error: " . $e->getMessage());
        }
    }

    /**
     * Additional Borrower Delete Handler
     */
    public function delete_borrower(int $id): void {
        try {
            $service = new BorrowersInfo();
            $service->crud_delete_borrower($id);
            header("Location: ../../index.php");
            exit();
        } catch (\Throwable $e) {
            die("Delete Error: " . $e->getMessage());
        }
    }
    
}

// --- EXECUTION BLOCK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $handler = new BorrowersHandler();
    $handler->insert_all_transactions($_POST);
}