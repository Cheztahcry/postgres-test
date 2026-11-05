<?php
namespace Handlers;

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Services\EquipmentsInfo;

class EquipmentsHandler {
    
    public function handle_request(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: ../../equipments_info.php");
            exit();
        }

        $action = $_POST['action'] ?? 'update';
        $service = new EquipmentsInfo();

        try {
            if ($action === 'create') {
                // Generate property number if left empty (e.g., EQ-123456)
                $propertyNo = trim($_POST['property_no'] ?? '');
                if (empty($propertyNo)) {
                    $propertyNo = "EQ-" . str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                }
                
                $name = trim($_POST['equipment_name'] ?? '');
                $qty  = (int) ($_POST['quantity_available'] ?? 1);

                if (!empty($name) && $qty >= 0) {
                    $service->create_equipment($propertyNo, $name, $qty);
                }
            } else if ($action === 'update') {
                $equipmentId = (int) ($_POST['equipment_id'] ?? 0);
                $quantity    = (int) ($_POST['quantity'] ?? 0);
                $propertyNo  = !empty($_POST['property_no']) ? trim($_POST['property_no']) : null;
                $name        = !empty($_POST['equipment_name']) ? trim($_POST['equipment_name']) : null;

                if ($equipmentId > 0 && $quantity >= 0) {
                    $service->update_equipments_info($quantity, $equipmentId, $propertyNo, $name);
                }
            }

            header("Location: ../../equipments_info.php?success=1");
            exit();
        } catch (\Throwable $e) {
            die("Database Action Error: " . $e->getMessage());
        }
    }
    
}

// Handler execution block
$handler = new EquipmentsHandler();
$handler->handle_request();