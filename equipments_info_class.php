<?php
    require_once __DIR__ . '/database.php';
    class EquipmentsInfo extends Database {
        public function __construct() {
        parent::__construct();
    }
        public function show_equipments() {
            $sql = "SELECT * FROM {$this->query_config['tables']['equipment']}";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $user;
        }
        public function update_equipments_info($new_quantity, $clause){
            $sql = "UPDATE {$this->query_config['tables']['equipment']} SET quantity_available = :new_quantity WHERE equipment_id = :clause";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'new_quantity' => $new_quantity,
                'clause' => $clause
            ]);
            return $stmt->execute($params);
        }
    }
    
            




    





?>