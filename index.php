<?php

try {
    include_once __DIR__ . '/borrowers_info_class.php';
    $student_info = new BorrowersInfo();
    $students = $student_info->show_students();
}catch(\PDOException $e){
    header("Location: error_page.php");
    exit;
}


?>
<DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/index.css">
    
</head>
<body>
<header>
    <div class="logo_row">
        
    </div>
    <div class="header-links">
            
            <div class="action-group">
                <a href="borrowers_info.php" class="signin-btn">Borrow An Item</a>
            </div>
            <div class="action-group">
                <a href="equipments_info.php" class="signin-btn">Edit Equipments</a>
            </div>
    </div>
</header>
    <div class = "options">
        
        <div class="search-field">
            <span class="search-icon" aria-hidden="true"></span>
            <input type="text" name="search_bar" id="search_bar" class="search-input" placeholder="Search by Name, Type">
        </div>
        <button type="button" class="option-btn search-btn" id= "search-button">Search</button>

    </div>
    

        
    <div class = "student-dashboard" id = "student-dashboard">
        <div class = "dashboard-container">
        
            <table>
            <thead>
            <tr>
                <th>Borrowers ID.</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Borrower Type</th>
            </tr>
            </thead>
            <tbody>
                <?php if ($students && count($students) > 0): ?>
                <?php foreach ($students as $row): ?>           
                <tr>
                    <td><?= htmlspecialchars($row->borrower_no) ?></td>
                    <td><?= htmlspecialchars($row->first_name) ?></td>
                    <td><?= htmlspecialchars($row->last_name) ?></td>
                    <td><?= htmlspecialchars($row->borrower_type) ?></td>
                    <td class="action-cell">
                        <form action="borrowers_dashboard.php" method="POST">
                        <button type="submit" name ="borrower_no" class="action-btn inquire-btn" value = "<?= htmlspecialchars($row->borrower_no) ?>">More Info</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                            <strong>No borrowers record are currently available.</strong><br>
                            Please try refreshing the page or check back later.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
                </table>
        </div>
        
    </div>
    <div id = "search-results">
        
    </div>
        
</body>
 <footer>
    </footer>
        <script src="js/index.js" defer></script>
        <script src="js/jquery.js" defer></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

</body>
</html>