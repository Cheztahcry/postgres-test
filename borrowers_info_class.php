<?php
    require_once __DIR__ . '/database.php';
    class BorrowersInfo extends Database {
        public function __construct() {
        parent::__construct();
    }
        public function insert_student_info(array $borrower_info){
            try {
            $this->pdo->beginTransaction();
            $stmtStudent = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrowers']} (borrower_no, first_name, last_name, borrower_type)
                VALUES (:borrower_no, :first_name, :last_name, :borrower_type) RETURNING borrower_id
            ");
            $stmtStudent->execute([
                'borrower_no' => $borrower_info["borrower_no"],
                'first_name' => $borrower_info["first_name"],
                'last_name'  => $borrower_info["last_name"],
                'borrower_type' => $borrower_info["borrower_type"]
            ]);
            $last_borrower_id = $stmtStudent->fetchColumn();
            $stmtTransaction = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrow_transactions']} (borrower_id, borrow_date, due_date, status)
                VALUES (:borrower_id, CURRENT_DATE, :due_date, 'Borrowed')
            ");
            $stmtTransaction->execute([
                'borrower_id' => $last_borrower_id,
                'due_date'  => $borrower_info["due_date"]
            ]);
            $this->pdo->commit();


        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "Error inserting records: " . $e->getMessage();
        }
        }
        public function show_borrowerinfo($uid) {
            $sql = "SELECT * FROM {$this->query_config['tables']['borrowers']} WHERE borrower_id = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute([$uid]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            return $user;
        }
        public function show_transactions($uid) {
            $sql = "SELECT * FROM {$this->query_config['tables']['borrow_transactions']} WHERE borrower_id = :uid";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute([
                'uid' => $uid]);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $user;
        }
        public function show_borrowers() {
            $sql = "SELECT * FROM {$this->query_config['tables']['borrowers']}";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $user;
        }


        public function update_equipments_info($new_quantity, $clause){
            $sql = "UPDATE {$this->query_config['tables']['equipment']} SET quantity_available = quantity_available - :new_quantity WHERE equipment_id = :clause";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'new_quantity' => $new_quantity,
                'clause' => $clause
                ]);
            return $stmt->execute($params);
        }
       public function generate_user_id() {
            // Generate 16 bytes of random data
            $data = random_bytes(16);

            // Set version to 0100 (version 4)
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
            // Set bits 6-7 to 10
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

            // Format the string into the standard 8-4-4-4-12 UUID layout
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }


    }
    

    
            




    





?>