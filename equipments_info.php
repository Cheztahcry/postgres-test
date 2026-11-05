<?php
// Replace manual include_once with PSR-4 Composer Autoloader
require_once __DIR__ . '/vendor/autoload.php';

use Services\EquipmentsInfo;

try {
    $equipments_info = new EquipmentsInfo();
    $equipments = $equipments_info->show_equipments();
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
    <title>Manage Equipment Inventory</title>
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

            <!-- SECTION 1: ADD NEW EQUIPMENT -->
            <fieldset style="margin-bottom: 24px; padding: 16px; border: 1px solid #CBD5E1; border-radius: 8px;">
                <legend style="font-weight: bold; padding: 0 8px;">Add New Equipment Item</legend>
                <form action="classes/handlers/EquipmentsHandler.php" method="post">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="field-group">
                        <label for="new_property_no">Property Number (Optional)</label>
                        <input type="text" name="property_no" id="new_property_no" placeholder="Auto-generated if blank (e.g. EQ-102934)">
                    </div>

                    <div class="field-group">
                        <label for="new_equipment_name">Equipment Name</label>
                        <input type="text" name="equipment_name" id="new_equipment_name" required placeholder="e.g. Projector, HDMI Cable">
                    </div>

                    <div class="field-group">
                        <label for="new_quantity">Initial Quantity Available</label>
                        <input type="number" name="quantity_available" id="new_quantity" min="0" max="999" value="1" required style="width: 100px;">
                    </div>

                    <div class="button-row" style="margin-top: 12px;">
                        <button type="submit" class="action-btn">Add Equipment</button>
                    </div>
                </form>
            </fieldset>

            <!-- SECTION 2: EDIT EXISTING EQUIPMENT QUANTITY -->
            <fieldset style="padding: 16px; border: 1px solid #CBD5E1; border-radius: 8px;">
                <legend style="font-weight: bold; padding: 0 8px;">Edit Existing Stock Quantity</legend>
                <form action="classes/handlers/EquipmentsHandler.php" method="post" id="owner-enrollment">
                    <input type="hidden" name="action" value="update">

                    <div class="field-group">
                        <label for="equipment_id">Select Equipment to Edit</label>
                        <div class="field-row">
                            <select name="equipment_id" id="equipment_id" required>
                                <option value="" disabled selected>-- Choose an item --</option>
                                <?php if ($equipments && count($equipments) > 0): ?>
                                    <?php foreach ($equipments as $row): ?>  
                                        <option value="<?= htmlspecialchars($row->equipment_id)?>">
                                            <?= htmlspecialchars($row->equipment_name) . " (Property No: " . htmlspecialchars($row->property_no) . ") - Stock: " . htmlspecialchars($row->quantity_available) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            
                            <input type="number" name="quantity" min="0" max="999" placeholder="Qty" value="1" style="width: 80px;" required>
                        </div>
                    </div>

                    <div class="button-row" style="margin-top: 12px;">
                        <button type="submit" name="submit" id="submit">Update Quantity</button>
                        <button type="button" id="clear">Clear Fields</button>
                    </div>
                </form>
            </fieldset>

        </div>
    </div>

    <script src="js/owner_info.js"></script>
    <script src="js/duplicate_handler.js" defer></script>
</body>
</html>