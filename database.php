<?php
    class DataBase{
        protected $pdo;
        protected $query_config;
        
        public function __construct(
        ){
            $this->query_config = require __DIR__ . '/query_config.php';
            $config = require __DIR__ . '/config.php';

            $dsn = "pgsql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
            $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $this->pdo = new PDO($dsn, $config['user'], $config['password'], $options);
            }
            catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());

            }
        }
        public function create_table($table, array $column) {
            $fields = [];
            $constraints = [];

            // 1. Separate standard columns from SQL constraints
            foreach ($column as $key => $value) {
                if (strtoupper($key) === 'FOREIGN KEY' || strtoupper($key) === 'PRIMARY KEY' || strtoupper($key) === 'UNIQUE KEY') {
                    $constraints[] = "$key $value";
                } else {
                    $fields[] = "`$key` $value";
                }
            }

            $all_parts = array_merge($fields, $constraints);
            $create_table = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $all_parts);
            $this->pdo->exec($create_table);
            foreach ($fields as $colDef) {
                if (preg_match('/`([^`]+)`/', $colDef, $m)) {
                    $alter = "ALTER TABLE `$table` ADD COLUMN IF NOT EXISTS " . $colDef;
                    try {
                        $this->pdo->exec($alter);
                    } catch (\PDOException $e) {
                    }
                }
            }

            return true;
        }


        public function show_table($table){
            $show = "SELECT * FROM `$table`";
            try {
                $show_query = $this->pdo->query($show);
                return $show_query->fetchAll(PDO::FETCH_OBJ);
            } catch (\PDOException $e) {
                return [];
            }
        }

        public function insert_table($table, array $column){
            unset($column['id']);
            $attributes = array_keys($column);
            $imp_att = implode(", ", $attributes);
            $place_att = ":". implode(", :", $attributes);
            $insert_info = "INSERT INTO `$table` ($imp_att) VALUES($place_att)";
            try {
                $insert = $this->pdo->prepare($insert_info);
                return $insert->execute($column);
                } 
            catch (PDOException $e) {
                throw $e;

        }
        
        }
       public function filterData(array $input, array $allowedKeys) {
        // 1. Whitelist the keys
        $whitelisted = array_intersect_key($input, array_flip($allowedKeys));
        
        // 2. NEW: Drop any keys where the user left the input blank
        return array_filter($whitelisted, function($value) {
            return $value !== ''; 
        });
        }
}
    

        

        



?>
