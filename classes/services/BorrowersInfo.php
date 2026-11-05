<?php
    namespace Services;
    use PDO;
    require_once dirname(__DIR__, 2) . '/database.php';
    class BorrowersInfo extends \Database {
        public function __construct() {
        parent::__construct();
    }
        public function insert_info(array $borrowers, array $borrow_items){
            try {
            $this->pdo->beginTransaction();
            $stmtStudent = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrowers']} (borrower_no, first_name, last_name, borrower_type)
                VALUES (:borrower_no, :first_name, :last_name, :borrower_type) RETURNING borrower_id
            ");
            $stmtStudent->execute([
                'borrower_no' => $borrowers["borrower_no"],
                'first_name' => $borrowers["first_name"],
                'last_name'  => $borrowers["last_name"],
                'borrower_type' => $borrowers["borrower_type"]
            ]);
            $last_borrower_id = $stmtStudent->fetchColumn();
            $stmtTransaction = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrow_transactions']} (borrower_id, borrow_date, due_date, status)
                VALUES (:borrower_id, CURRENT_DATE, :due_date, 'Borrowed') RETURNING borrow_id
            ");
            $stmtTransaction->execute([
                'borrower_id' => $last_borrower_id,
                'due_date'  => $borrowers["due_date"]
            ]);
            $last_transac_id = $stmtTransaction->fetchColumn();
            $stmtTransaction = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrow_items']} (borrow_id, equipment_id, quantity)
                VALUES (:borrow_id, :equipment_id, :quantity)
            ");
            $stmtTransaction->execute([
                'borrow_id' => $last_transac_id,
                'equipment_id'  => $borrow_items["equipment_id"],
                'quantity' => $borrow_items['quantity']
            ]);
            $this->pdo->commit();
            } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e; 
        }
        }
        public function show_borrowed_equipments($borrowerNo) {
        try {
            $sql = "SELECT 
                        bi.borrow_item_id, 
                        bi.borrow_id, 
                        bi.equipment_id, 
                        bi.quantity
                    FROM borrow_items bi
                    JOIN borrow_transactions bt ON bi.borrow_id = bt.borrow_id
                    JOIN borrowers b ON bt.borrower_id = b.borrower_id
                    WHERE b.borrower_no = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$borrowerNo]);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (\PDOException $e) {
            error_log("Error fetching borrowed equipment: " . $e->getMessage());
            return [];
        }
        }
        public function w6_search_borrowers_dynamic(string $input = '', string $borrowerType = '', string $viewMode = 'detailed', int $minCount = 1): array {
        $searchTerm = '%' . $input . '%';

        if ($viewMode === 'grouped') {
            $stmt = $this->pdo->prepare("
                SELECT 
                    b.borrower_type AS category, 
                    COUNT(b.borrower_id) AS total_count
                FROM borrowers b
                WHERE (
                    b.first_name ILIKE ? 
                    OR b.last_name ILIKE ? 
                    OR CONCAT_WS(' ', b.first_name, b.last_name) ILIKE ? 
                    OR CAST(b.borrower_no AS TEXT) ILIKE ?
                )
                AND (? = '' OR b.borrower_type = ?)
                GROUP BY b.borrower_type
                HAVING COUNT(b.borrower_id) >= ?
                ORDER BY total_count DESC, category ASC
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $borrowerType, $borrowerType, $minCount]);
        } else {
            $stmt = $this->pdo->prepare("
                SELECT DISTINCT 
                    b.borrower_id, 
                    b.borrower_no AS borrower_uid, 
                    b.first_name,
                    b.last_name,
                    CONCAT_WS(' ', b.first_name, b.last_name) AS full_name, 
                    b.borrower_type AS category
                FROM borrowers b
                WHERE (
                    b.first_name ILIKE ? 
                    OR b.last_name ILIKE ? 
                    OR CONCAT_WS(' ', b.first_name, b.last_name) ILIKE ? 
                    OR CAST(b.borrower_no AS TEXT) ILIKE ?
                )
                AND (? = '' OR b.borrower_type = ?)
                ORDER BY b.last_name ASC, b.first_name ASC
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $borrowerType, $borrowerType]);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        public function show_borrowerinfo($uid) {
            try{
            $sql = "SELECT borrower_no, last_name, first_name, borrower_type FROM {$this->query_config['tables']['borrowers']} WHERE borrower_no = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute([$uid]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            return $user;
            }catch (\PDOException $e) {
                die("Something went wrong with fetching borrowers data. Please check back later.");
            }
        }
        public function show_transactions($uid) {
            try{
            $sql = "SELECT * FROM {$this->query_config['tables']['borrow_transactions']} WHERE borrower_id = :uid";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute([
                'uid' => $uid]);
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $user;
            }catch (\PDOException $e) {
                die("Something went wrong with fetching borrowers data. Please check back later.");
            }
        }
        public function show_borrowers() {
            try{
            $sql = "SELECT * FROM {$this->query_config['tables']['borrowers']}";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $user;
            }catch (\PDOException $e) {
                die("Something went wrong with fetching borrowers data. Please check back later.");
            }
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
        public function archiveBorrower(int $borrowerId): bool {
        $sql = "UPDATE borrowers 
                SET is_archived = TRUE, archived_at = CURRENT_TIMESTAMP 
                WHERE borrower_id = ?";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$borrowerId]);
        }

        public function getActiveBorrowers(): array {
            $sql = "SELECT borrower_id, borrower_no, first_name, last_name, borrower_type 
                    FROM borrowers 
                    WHERE is_archived = FALSE 
                    ORDER BY last_name ASC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        public function get_borrowers_borrowerd(): array {
            $sql = "SELECT borrower_id, borrower_no, first_name, last_name, borrower_type 
                    FROM borrow
                    WHERE is_archived = FALSE 
                    ORDER BY last_name ASC";

            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        public function archiveTransaction(string $borrowerNo) {
        try {
            $this->pdo->beginTransaction();

            // 1. Log action as 'ARCHIVE' (7 chars <= 10 limit)
            $stmtArchive = $this->pdo->prepare("INSERT INTO archive (borrow_item_id, operation_type) VALUES (NULL, ?)");
            $stmtArchive->execute(["ARCHIVE"]);

            // 2. Delete child item records
            $stmtItems = $this->pdo->prepare("
                DELETE FROM borrow_items 
                WHERE borrow_id IN (
                    SELECT bt.borrow_id 
                    FROM borrow_transactions bt 
                    JOIN borrowers b ON b.borrower_id = bt.borrower_id 
                    WHERE b.borrower_no = ?
                )
            ");
            $stmtItems->execute([$borrowerNo]);

            // 3. Delete parent transaction records
            $stmtTrans = $this->pdo->prepare("
                DELETE FROM borrow_transactions 
                WHERE borrower_id = (SELECT borrower_id FROM borrowers WHERE borrower_no = ?)
            ");
            $stmtTrans->execute([$borrowerNo]);

            // 4. Delete borrower record
            $stmtDelete = $this->pdo->prepare("DELETE FROM borrowers WHERE borrower_no = ?");
            $stmtDelete->execute([$borrowerNo]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
        }


    }
    

    
            




    





?>