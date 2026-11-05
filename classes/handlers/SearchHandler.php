<?php
namespace Handlers;
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
use Services\SearchInfo;
header('Content-Type: application/json; charset=utf-8');
class SearchHandler{
    public function results_search(){
    try{
        $input = trim(($_POST['input'] ?? ""));
        $search = new SearchInfo();
        $results = $search->search_results($input);
        echo json_encode($results);
    }catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database query failed',
        'message' => $e->getMessage(), // Exposes exact PDO or PHP error
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
    exit();
       
}

}  
    $handler = new SearchHandler();
    $handler->results_search();
?>
