<?php
require_once __DIR__ . '/database.php';

class TablesInfo extends Database {
    public function __construct() {
        parent::__construct();
    }

    public function create_all_tables_and_objects(): void {
        $sql = "
        -- 0. CLEAN DROP OF VIEWS & TABLES
        DROP VIEW IF EXISTS borrow_details_view CASCADE;
        DROP VIEW IF EXISTS equipment_usage_view CASCADE;
        DROP TABLE IF EXISTS archive CASCADE;
        DROP TABLE IF EXISTS borrow_items CASCADE;
        DROP TABLE IF EXISTS borrow_transactions CASCADE;
        DROP TABLE IF EXISTS equipment CASCADE;
        DROP TABLE IF EXISTS borrowers CASCADE;

        -- 1. CORE TABLES
        CREATE TABLE borrowers (
            borrower_id SERIAL PRIMARY KEY,
            borrower_no VARCHAR(50) UNIQUE NOT NULL,
            first_name VARCHAR(255) NOT NULL,
            last_name VARCHAR(255) NOT NULL,
            borrower_type VARCHAR(50) NOT NULL
        );

        CREATE TABLE equipment (
            equipment_id SERIAL PRIMARY KEY,
            property_no VARCHAR(50) UNIQUE NOT NULL,
            equipment_name VARCHAR(255) NOT NULL,
            quantity_available INT NOT NULL CHECK (quantity_available >= 0)
        );

        CREATE TABLE borrow_transactions (
            borrow_id SERIAL PRIMARY KEY,
            borrower_id INT NOT NULL REFERENCES borrowers(borrower_id) ON DELETE CASCADE,
            borrow_date DATE DEFAULT CURRENT_DATE NOT NULL,
            due_date DATE NOT NULL,
            status VARCHAR(20) DEFAULT 'Borrowed' NOT NULL
        );

        CREATE TABLE borrow_items (
            borrow_item_id SERIAL PRIMARY KEY,
            borrow_id INT NOT NULL REFERENCES borrow_transactions(borrow_id) ON DELETE CASCADE,
            equipment_id INT NOT NULL REFERENCES equipment(equipment_id) ON DELETE RESTRICT,
            quantity INT NOT NULL CHECK (quantity > 0),
            UNIQUE (borrow_id, equipment_id)
        );

        -- 2. ARCHIVE / AUDIT TABLE
        CREATE TABLE archive (
            archive_id SERIAL PRIMARY KEY,
            borrow_item_id INT,
            operation_type VARCHAR(10) NOT NULL,
            old_borrow_id INT,
            new_borrow_id INT,
            old_equipment_id INT,
            new_equipment_id INT,
            old_quantity INT,
            new_quantity INT,
            changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL
        );

        -- 3. VIEWS (Fixed typo: b.last_name)
        CREATE OR REPLACE VIEW borrow_details_view AS
        SELECT 
            bt.borrow_id,
            b.borrower_id,
            b.borrower_no,
            b.first_name,
            b.last_name,
            b.borrower_type,
            e.equipment_id,
            e.property_no,
            e.equipment_name,
            bi.quantity AS borrowed_quantity,
            bt.borrow_date,
            bt.due_date,
            bt.status
        FROM borrow_transactions bt
        JOIN borrowers b ON bt.borrower_id = b.borrower_id
        JOIN borrow_items bi ON bt.borrow_id = bi.borrow_id
        JOIN equipment e ON bi.equipment_id = e.equipment_id;

        CREATE OR REPLACE VIEW equipment_usage_view AS
        SELECT 
            e.equipment_id,
            e.property_no,
            e.equipment_name,
            e.quantity_available,
            COUNT(DISTINCT bi.borrow_id) AS total_transactions,
            COALESCE(SUM(bi.quantity), 0) AS total_quantity_borrowed,
            CASE 
                WHEN e.quantity_available = 0 THEN 'Out of Stock'
                WHEN e.quantity_available <= 3 THEN 'Low Stock'
                ELSE 'Available'
            END AS stock_status
        FROM equipment e
        LEFT JOIN borrow_items bi ON e.equipment_id = bi.equipment_id
        GROUP BY e.equipment_id, e.property_no, e.equipment_name, e.quantity_available;

        -- 4. SQL & PL/pgSQL FUNCTIONS
        CREATE OR REPLACE FUNCTION fn_total_borrowed(p_equipment_id INT)
        RETURNS INT AS $$ 
            SELECT COALESCE(SUM(bi.quantity), 0)::INT 
            FROM borrow_items bi 
            JOIN borrow_transactions bt ON bi.borrow_id = bt.borrow_id 
            WHERE bi.equipment_id = p_equipment_id AND bt.status = 'Borrowed'; 
        $$ LANGUAGE SQL;

        CREATE OR REPLACE FUNCTION fn_check_equipment_availability(p_equipment_id INT, p_requested_quantity INT)
        RETURNS BOOLEAN AS $$ 
        DECLARE 
            v_available INT; 
        BEGIN 
            SELECT quantity_available INTO v_available FROM equipment WHERE equipment_id = p_equipment_id;
            IF NOT FOUND THEN RETURN FALSE; END IF;
            RETURN v_available >= p_requested_quantity; 
        END; 
        $$ LANGUAGE plpgsql;

        -- 5. BEFORE TRIGGER (Stock Validation)
        CREATE OR REPLACE FUNCTION fn_trg_validate_borrow_quantity()
        RETURNS TRIGGER AS $$ 
        DECLARE 
            v_available INT; 
        BEGIN 
            SELECT quantity_available INTO v_available FROM equipment WHERE equipment_id = NEW.equipment_id;
            IF v_available < NEW.quantity THEN 
                RAISE EXCEPTION 'Requested quantity (%) exceeds available stock (%) for equipment ID %.', NEW.quantity, v_available, NEW.equipment_id; 
            END IF;
            RETURN NEW; 
        END; 
        $$ LANGUAGE plpgsql;

        DROP TRIGGER IF EXISTS trg_validate_borrow_quantity ON borrow_items;
        CREATE TRIGGER trg_validate_borrow_quantity
        BEFORE INSERT OR UPDATE ON borrow_items
        FOR EACH ROW
        EXECUTE FUNCTION fn_trg_validate_borrow_quantity();

        -- 6. AFTER TRIGGER (Audit Logging)
        CREATE OR REPLACE FUNCTION fn_trg_borrow_items_archive()
        RETURNS TRIGGER AS $$ 
        BEGIN 
            IF (TG_OP = 'DELETE') THEN 
                INSERT INTO archive (borrow_item_id, operation_type, old_borrow_id, old_equipment_id, old_quantity) 
                VALUES (OLD.borrow_item_id, 'DELETE', OLD.borrow_id, OLD.equipment_id, OLD.quantity); 
                RETURN OLD; 
            ELSIF (TG_OP = 'UPDATE') THEN 
                INSERT INTO archive (borrow_item_id, operation_type, old_borrow_id, new_borrow_id, old_equipment_id, new_equipment_id, old_quantity, new_quantity) 
                VALUES (NEW.borrow_item_id, 'UPDATE', OLD.borrow_id, NEW.borrow_id, OLD.equipment_id, NEW.equipment_id, OLD.quantity, NEW.quantity); 
                RETURN NEW; 
            ELSIF (TG_OP = 'INSERT') THEN 
                INSERT INTO archive (borrow_item_id, operation_type, new_borrow_id, new_equipment_id, new_quantity) 
                VALUES (NEW.borrow_item_id, 'INSERT', NEW.borrow_id, NEW.equipment_id, NEW.quantity); 
                RETURN NEW; 
            END IF; 
            RETURN NULL; 
        END; 
        $$ LANGUAGE plpgsql;

        DROP TRIGGER IF EXISTS trg_borrow_items_archive ON borrow_items;
        CREATE TRIGGER trg_borrow_items_archive
        AFTER INSERT OR UPDATE OR DELETE ON borrow_items
        FOR EACH ROW
        EXECUTE FUNCTION fn_trg_borrow_items_archive();
        ";

        try {
            $this->pdo->exec($sql);
            echo "Successfully created database schema matching exact table names.";
        } catch (\PDOException $e) {
            echo "DDL Execution Error: " . $e->getMessage();
            exit();
        }
    }
}

$all_tables = new TablesInfo();
$all_tables->create_all_tables_and_objects();