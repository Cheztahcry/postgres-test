<?php
    namespace Handlers;
    use Services\EquipmentsInfo;
    
    class EquipmentsHandler{
    public function update_equipments($input){
    try{
        $equipments_info = new EquipmentsInfo();
        $equipment_id = ($_POST['equipment_id'] ?? '');
        $quantity = ($_POST['quantity'] ?? '');
        $equipments = $equipments_info->update_equipments_info($quantity, $equipment_id);
        header("Location: index.php");
        exit;
    }catch (\PDOException $e) {
        die("Something went wrong. Please check back later.");
    }
    
    }
    public function show_equipments(){
    try{
        $equipments_info = new EquipmentsInfo();
        $equipments_list = $equipments_info->show_equipments();
        return $equipments_list;
    }catch (\PDOException $e) {
        die("Something went wrong. Please check back later.");
    }
    
    }
    
} 
    





?>