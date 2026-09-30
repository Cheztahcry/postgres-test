<?php
try {
    include_once __DIR__ . '/equipments_info_class.php';
    $equipments_info = new EquipmentsInfo();
    $equipments = $equipments_info->show_equipments();
}catch(\PDOException $e){
    header("Location: error_page.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/owner_info_style.css">
</head>
<body>
    <div class="student form">
        <div class="container">
            <div class="page-header">
                <nav class="page-nav">
                    <a class="nav-link" href="index.php">Home</a>
                </nav>
            </div>
            <h1>Borrower Information</h1>
            <form action="borrowers_handler.php" method="post" id="owner-enrollment">
                <div class="field-group">
                    <label>Full Name</label>
                    <div class="field-row">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="text" name="first_name" placeholder="First Name" required>
                        </label>
                    </div>
                </div>
                <div class="field-group">
                    <label>Borrower Type</label>
                    <div class="field-row">
                        <label>
                            <input type="radio" name="borrower_type" value="Student"> Student
                        </label>
                        <label>
                            <input type="radio" name="borrower_type" value="Faculty"> Faculty/Teacher
                        </label>
                        <label>
                            <input type="radio" name="borrower_type" value="Staff"> Staff
                        </label>
                        <label>
                            <input type="radio" name="borrower_type" value="Guest"> Guest
                        </label>
                    </div>

                </div>
                <div class="field-group">
                    <label for="equipment_id">Select Equipment</label>
                    <div class="field-row">
                        <select name="equipment_id" id="equipment_id" >
                            <option value="" disabled selected>-- Choose an item --</option>
                            <?php if ($equipments && count($equipments) > 0): ?>
                            <?php foreach ($equipments as $row): ?>  
                            <option value="<?= htmlspecialchars($row->equipment_id)?>"><?= htmlspecialchars($row->equipment_name) . " - " .htmlspecialchars($row->quantity_available) ?></option>
                            <?php endforeach; ?>
                            
                        </select>
                        
                        <input type="number" name="quantity" min="1" max="<?= htmlspecialchars($row->quantity_available)?>" placeholder="Qty" value="1" style="width: 80px;" required>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="field-group">
                    <label for="due_date">Expected Return Date</label>
                    <div class="field-row">
                        <input type="date" id="due_date" name="due_date" required>
                    </div>
                </div>
                <div class="button-row">
                    <button type="submit" name="submit" id="submit">Next Section</button>
                    <button type="button" id="clear">Clear Fields</button>
                </div>

            </form>
        </div>
    </div>
    <script src="js/owner_info.js"></script>
    <script src="js/duplicate_handler.js" defer></script>

</body>
</html>