<?php
namespace Services;

use PDO;
use PDOException;

require_once dirname(__DIR__, 2) . '/database.php';

class EquipmentsInfo extends \Database {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Retrieve all equipment items from PostgreSQL database
     */
    public function show_equipments(): array {
        try {
            $sql = "SELECT equipment_id, property_no, equipment_name, quantity_available 
                    FROM equipment 
                    ORDER BY equipment_name ASC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching equipment: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Insert new equipment record (Rubric Criteria)
     */
    public function create_equipment(string $propertyNo, string $name, int $quantity): bool {
        try {
            $sql = "INSERT INTO equipment (property_no, equipment_name, quantity_available) 
                    VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([trim($propertyNo), trim($name), $quantity]);
        } catch (PDOException $e) {
            error_log("Error inserting equipment: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update existing equipment quantity or property details
     */
    public function update_equipments_info(int $quantity, int $equipmentId, ?string $propertyNo = null, ?string $name = null): bool {
        try {
            if ($propertyNo && $name) {
                $sql = "UPDATE equipment 
                        SET property_no = ?, equipment_name = ?, quantity_available = ? 
                        WHERE equipment_id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([trim($propertyNo), trim($name), $quantity, $equipmentId]);
            } else {
                $sql = "UPDATE equipment 
                        SET quantity_available = ? 
                        WHERE equipment_id = ?";
                $stmt = $this->pdo->prepare($sql);
                return $stmt->execute([$quantity, $equipmentId]);
            }
        } catch (PDOException $e) {
            error_log("Error updating equipment: " . $e->getMessage());
            throw $e;
        }
    }
}