<?php
    require_once __DIR__ . '/database.php';
    class TablesInfo extends Database {
        public function __construct() {
        parent::__construct();
    }
        public function create_all_table(){
        $tables = "
            CREATE TABLE IF NOT EXISTS borrowers (
                borrower_id SERIAL PRIMARY KEY,
                borrower_no VARCHAR(50) UNIQUE NOT NULL,
                full_name VARCHAR(255) NOT NULL,
                borrower_type VARCHAR(50) NOT NULL
            );

            CREATE TABLE IF NOT EXISTS equipment (
                equipment_id SERIAL PRIMARY KEY,
                property_no VARCHAR(50) UNIQUE NOT NULL,
                equipment_name VARCHAR(255) NOT NULL,
                quantity_available INT NOT NULL CHECK (quantity_available >= 0)
            );

            CREATE TABLE IF NOT EXISTS borrow_transactions (
                borrow_id SERIAL PRIMARY KEY,
                borrower_id INT NOT NULL REFERENCES borrowers(borrower_id) ON DELETE CASCADE,
                borrow_date DATE DEFAULT CURRENT_DATE NOT NULL,
                due_date DATE NOT NULL,
                status VARCHAR(20) DEFAULT 'Borrowed' NOT NULL
            );

            CREATE TABLE IF NOT EXISTS borrow_items (
                borrow_item_id SERIAL PRIMARY KEY,
                borrow_id INT NOT NULL REFERENCES borrow_transactions(borrow_id) ON DELETE CASCADE,
                equipment_id INT NOT NULL REFERENCES equipment(equipment_id) ON DELETE RESTRICT,
                quantity INT NOT NULL CHECK (quantity > 0),
                UNIQUE (borrow_id, equipment_id)
            );
            ";
        try {
            $this->pdo->exec($tables);
            echo "Successfuladd creating tables:";

        } catch (Exception $e) {
            echo "Error creating tables: " . $e->getMessage();
            exit;
        }
        }
    }
    $all_tables = new Tablesinfo();
    $all_tables->create_all_table();
    