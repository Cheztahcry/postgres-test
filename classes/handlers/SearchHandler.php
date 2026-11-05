<?php
namespace Handlers;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Services\BorrowersInfo;

header('Content-Type: application/json; charset=utf-8');

class SearchHandler {
    public function results_search(): void {
        try {
            $action = $_POST['action'] ?? $_GET['action'] ?? 'search';
            $search = new BorrowersInfo();

            switch ($action) {
                /* --- WEEK 6 REPORTS --- */
                case 'w6_equipment_inventory':
                    echo json_encode(['success' => true, 'payload' => $search->w6_r_equipment_inventory_listing()]);
                    exit();

                case 'w6_filtered_borrowings':
                    $status    = trim($_POST['status'] ?? '');
                    $startDate = trim($_POST['start_date'] ?? '');
                    $endDate   = trim($_POST['end_date'] ?? '');
                    echo json_encode(['success' => true, 'payload' => $search->w6_r_filtered_borrowing_records($status, $startDate, $endDate)]);
                    exit();

                case 'w6_borrower_summary':
                    echo json_encode(['success' => true, 'payload' => $search->w6_r_borrower_type_summary()]);
                    exit();

                case 'w6_status_summary':
                    echo json_encode(['success' => true, 'payload' => $search->w6_r_transaction_status_summary()]);
                    exit();

                case 'w6_grouped_analysis':
                    $minTx = (int) ($_POST['min_tx'] ?? 2);
                    echo json_encode(['success' => true, 'payload' => $search->w6_r_grouped_borrowing_analysis($minTx)]);
                    exit();

                /* --- WEEK 7 REPORTS --- */
                case 'w7_complete_borrowing':
                    echo json_encode(['success' => true, 'payload' => $search->w7_r_complete_borrowing_report()]);
                    exit();

                case 'w7_equipment_summary':
                    echo json_encode(['success' => true, 'payload' => $search->w7_r_equipment_borrowing_summary()]);
                    exit();

                case 'w7_borrowers_no_tx':
                    echo json_encode(['success' => true, 'payload' => $search->w7_r_borrowers_without_transactions()]);
                    exit();

                case 'w7_equipment_no_history':
                    echo json_encode(['success' => true, 'payload' => $search->w7_r_equipment_without_borrowing_history()]);
                    exit();

                case 'w7_combined_activity':
                    $unionAll = isset($_POST['union_all']) && $_POST['union_all'] === 'true';
                    echo json_encode(['success' => true, 'payload' => $search->w7_r_combined_borrower_activity($unionAll)]);
                    exit();

                case 'w7_monthly_report':
                    echo json_encode(['success' => true, 'payload' => $search->w7_r_monthly_borrowing_report()]);
                    exit();

                /* --- WEEK 8 VIEWS --- */
                case 'w8_borrow_details_view':
                    echo json_encode(['success' => true, 'payload' => $search->w8_get_borrow_details_view()]);
                    exit();

                case 'w8_equipment_usage_view':
                    echo json_encode(['success' => true, 'payload' => $search->w8_get_equipment_usage_view()]);
                    exit();

                case 'w8_math_case_analysis':
                    echo json_encode(['success' => true, 'payload' => $search->w8_r_math_and_case_inventory_analysis()]);
                    exit();

                /* --- WEEK 10 AUDIT LOGS & FUNCTIONS --- */
                case 'w10_archive_logs':
                    echo json_encode(['success' => true, 'payload' => $search->w10_get_archive_logs()]);
                    exit();

                /* --- DEFAULT DYNAMIC SEARCH (Original Search Request Handling) --- */
                case 'search':
                default:
                    $input        = trim($_POST['input'] ?? '');
                    $borrowerType = trim($_POST['borrower_type'] ?? '');
                    $viewMode     = trim($_POST['view_mode'] ?? 'detailed');
                    $minCount     = (int) ($_POST['min_count'] ?? 1);

                    $results = $search->w6_search_borrowers_dynamic($input, $borrowerType, $viewMode, $minCount);

                    echo json_encode([
                        'mode' => $viewMode,
                        'data' => $results ?: []
                    ]);
                    exit();
            }
        } catch (\Throwable $e) {
            error_log("Search Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Database query failed']);
            exit();
        }
    }
}

$handler = new SearchHandler();
$handler->results_search();