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
            <h1>Equipments Information</h1>
            <form action="equipments_handler.php" method="post" id="owner-enrollment">
               <div class="field-group">
                    <label for="equipment_id">Select Equipment to Edit</label>
                    <div class="field-row">
                        <select name="equipment_id" id="equipment_id" >
                                   
                            <option value="" disabled selected>-- Choose an item --</option>
                            <?php if ($equipments && count($equipments) > 0): ?>
                            <?php foreach ($equipments as $row): ?>  
                            <option value="<?= htmlspecialchars($row->equipment_id)?>"><?= htmlspecialchars($row->equipment_name) . " - " .htmlspecialchars($row->quantity_available) ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        
                        <input type="number" name="quantity" min="1" max="99" placeholder="Qty" value="1" style="width: 80px;">
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