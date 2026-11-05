<?php
namespace Services;
use PDO;
use PDOException;
require_once dirname(__DIR__, 2) . '/database.php';

class SearchInfo extends \Database{
    public function __construct() {
    parent::__construct();
    }
    public function search_results($input) {
    $query = "
        SELECT DISTINCT 
            borrowers.first_name, 
            borrowers.last_name 
        FROM {$this->query_config['tables']['borrowers']} AS borrowers 
        WHERE (
            borrowers.first_name ILIKE ?
            OR borrowers.last_name ILIKE ?
        )";
    $searchTerm = '%' . $input . '%';
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([$searchTerm, $searchTerm]);
    
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $results;
}
}
?>
