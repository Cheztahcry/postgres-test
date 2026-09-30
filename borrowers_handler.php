<?php 
    require_once __DIR__ . '/borrowers_info_class.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $last_name  = trim($_POST['last_name'] ?? '');
        $first_name  = trim($_POST['first_name'] ?? '');
        $borrower_type = $_POST['borrower_type'] ?? null;
        $borrower = new BorrowersInfo();
        $borrower_no = "BR-". str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $rawDate = !empty($_POST['due_date']) ? trim($_POST['due_date']) : null;
        $dueDate = null;
        if ($rawDate) {
            $parsed = DateTime::createFromFormat('Y-m-d', $rawDate);
            if ($parsed && $parsed->format('Y-m-d') === $rawDate) {
                $dueDate = $rawDate;
            } else {
                die("Invalid date format provided.");
            }
        }

        

        $borrower_info = [ 
            "first_name"   => $first_name,
            "last_name"    => $last_name,
            "borrower_type"  => $borrower_type,
            "borrower_no" => $borrower_no,
            "due_date" => $dueDate
        ];
        $borrower->insert_student_info($borrower_info);
        header("Location: index.php");
        exit;
}
?>