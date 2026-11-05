<?php
require_once __DIR__ . '/vendor/autoload.php';
use Services\BorrowersInfo;

try {
    $borrowers_info = new BorrowersInfo();
    $borrowers = $borrowers_info->show_borrowers();
} catch (\PDOException $e) {
    header("Location: error_page.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipment Borrowing System</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
<header>
    <div class="logo_row"></div>
    <div class="header-links">
        <div class="action-group">
            <a href="borrowers_info.php" class="signin-btn">Borrow An Item</a>
        </div>
        <div class="action-group">
            <a href="equipments_info.php" class="signin-btn">Edit Equipments</a>
        </div>
    </div>
</header>

<div class="options">
    <!-- Row Filter: Text Search (WHERE ILIKE) -->
    <div class="search-field">
        <span class="search-icon" aria-hidden="true"></span>
        <input type="text" name="search_bar" id="search_bar" class="search-input" placeholder="Search by Name, Borrower No.">
    </div>

    <!-- Row Filter: Category Dropdown (WHERE borrower_type) -->
    <select name="borrower_type" id="borrower_type_filter" class="filter-select">
        <option value="">All Borrower Types</option>
        <option value="Student">Student</option>
        <option value="Faculty">Faculty</option>
        <option value="Staff">Staff</option>
    </select>

    <!-- Mode Selector: Detailed Rows vs Grouped Summary -->
    <select id="view_mode" class="filter-select">
        <option value="detailed">Detailed List</option>
        <option value="grouped">Category Summary (GROUP BY)</option>
    </select>

    <!-- Report Selector (Weeks 6 - 10 Deliverables) -->
    <select id="report_selector" class="filter-select">
        <option value="search">Dynamic Search Dashboard</option>
        <optgroup label="Week 6 Reports">
            <option value="w6_equipment_inventory">Equipment Inventory Listing</option>
            <option value="w6_filtered_borrowings">Filtered Borrowing Records</option>
            <option value="w6_borrower_summary">Borrower Type Summary</option>
            <option value="w6_status_summary">Transaction Status Summary</option>
            <option value="w6_grouped_analysis">Grouped Borrowing Analysis (HAVING >= 2)</option>
        </optgroup>
        <optgroup label="Week 7 Reports">
            <option value="w7_complete_borrowing">Complete Borrowing Report (INNER JOIN)</option>
            <option value="w7_equipment_summary">Equipment Borrowing Summary (Aggregates)</option>
            <option value="w7_borrowers_no_tx">Borrowers Without Transactions (LEFT JOIN)</option>
            <option value="w7_equipment_no_history">Equipment Without History (LEFT JOIN)</option>
            <option value="w7_combined_activity">Combined Borrower Activity (UNION)</option>
            <option value="w7_monthly_report">Monthly Borrowing Report (Date Functions)</option>
        </optgroup>
        <optgroup label="Week 8 Views & Math">
            <option value="w8_borrow_details_view">View: borrow_details_view</option>
            <option value="w8_equipment_usage_view">View: equipment_usage_view</option>
            <option value="w8_math_case_analysis">Math & CASE Inventory Analysis</option>
        </optgroup>
        <optgroup label="Week 10 Audit & Triggers">
            <option value="w10_archive_logs">Audit Logs (archive table)</option>
        </optgroup>
    </select>

    <!-- Group Filter: Min Count Input (HAVING) -->
    <div class="filter-group" id="having_filter_group" style="display: none;">
        <label for="min_count_filter">Min Borrowers:</label>
        <input type="number" id="min_count_filter" class="filter-number" value="1" min="1">
    </div>

    <button type="button" class="option-btn search-btn" id="search-button">Search</button>
</div>

<!-- Default Master List View -->
<div class="student-dashboard" id="student-dashboard">
    <div class="dashboard-container">
        <table>
            <thead>
                <tr>
                    <th>Borrower No.</th>
                    <th>Full Name</th>
                    <th>Borrower Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($borrowers && count($borrowers) > 0): ?>
                    <?php foreach ($borrowers as $row): ?>           
                    <tr>
                        <td><?= htmlspecialchars($row->borrower_no ?? '') ?></td>
                        <td><?= htmlspecialchars($row->full_name ?? trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? ''))) ?></td>
                        <td><?= htmlspecialchars($row->borrower_type ?? '') ?></td>
                        <td class="action-cell">
                            <form action="borrowers_dashboard.php" method="POST">
                                <button type="submit" name="borrower_no" class="action-btn inquire-btn" value="<?= htmlspecialchars($row->borrower_no ?? '') ?>">More Info</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px; color: #666;">
                            <strong>No borrower records are currently available.</strong><br>
                            Please try refreshing the page or check back later.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Dynamic Search & Multi-Report Results Container -->
<div id="search-results" style="display: none;">
    <div class="student-dashboard">
        <div class="dashboard-container">
            <table class="results-table">
                <thead></thead>
                <tbody></tbody>
            </table>

            <div class="no-results-card" style="display: none;">
                <div class="no-results-icon" aria-hidden="true">🔎</div>
                <h3>No results found</h3>
                <p>Try refining your search keywords, filters, or selected report mode.</p>
            </div>
        </div>
    </div>
</div>

<footer></footer>

<!-- Core Script Libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="js/jquery.js" defer></script>
</body>
</html>