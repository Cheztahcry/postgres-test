<?php


    include_once __DIR__ . '/equipments_info_class.php';
    $equipment_id = ($_POST['equipment_id'] ?? '');
    $quantity = ($_POST['quantity'] ?? '');
    $equipments_info = new EquipmentsInfo();
    $equipments = $equipments_info->update_equipments_info($quantity, $equipment_id);
    header("Location: index.php");
    exit;
    

    header("Location: error_page.php");
    exit;





?>


