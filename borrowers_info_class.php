<?php
    require_once 'Database.php';
    class BorrowersInfo extends Database {
        public function __construct() {
        parent::__construct();
    }
        public function insert_student_info(array $borrower_info){
            try {
            $this->pdo->beginTransaction();
            $stmtStudent = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrowers']} (borrower_no, first_name, last_name, borrower_type)
                VALUES (:borrower_no, :first_name, :last_name, :borrower_type)
            ");
            $stmtStudent->execute([
                'borrower_no' => $borrower_info["borrower_no"],
                'first_name' => $borrower_info["first_name"],
                'last_name'  => $borrower_info["last_name"],
                'borrower_type' => $borrower_info["borrower_type"]
            ]);
            $this->pdo->beginTransaction();
            $stmtStudent = $this->pdo->prepare("
                INSERT INTO {$this->query_config['tables']['borrowers']} (borrower_no, first_name, last_name, borrower_type)
                VALUES (:borrower_no, :first_name, :last_name, :borrower_type)
            ");
            $stmtStudent->execute([
                'borrower_no' => $borrower_info["borrower_no"],
                'first_name' => $borrower_info["first_name"],
                'last_name'  => $borrower_info["last_name"],
                'borrower_type' => $borrower_info["borrower_type"]
            ]); 
            $this->pdo->commit();


        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "Error inserting records: " . $e->getMessage();
        }
        }
        public function show_studentinfo($uid) {
            $sql = "SELECT * FROM {$this->query_config['tables']['borrowers']} WHERE borrower_no = ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute([$uid]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            return $user;
        }
        public function show_students() {
            $sql = "SELECT * FROM {$this->query_config['tables']['borrowers']}";
            $stmt = $this->pdo->prepare($sql);          
            $stmt->execute();
            $user = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $user;
        }


        public function update_studentinfo(int $id, array $updates){
            if (empty($updates)) return false;
            $sets = [];
            $params = [];
            foreach ($updates as $col => $val) {
                $sets[] = "`$col` = :$col";
                $params[":$col"] = $val;
            }
            $params[':id'] = $id;
            $sql = "UPDATE `{$this->tbl_name}` SET " . implode(', ', $sets) . " WHERE `id` = :id";
            $stmt = $this->pdo->prepare($sql);
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
    
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $last_name  = trim($_POST['last_name'] ?? '');
    $first_name  = trim($_POST['first_name'] ?? '');
    $borrower_type = $_POST['borrower_type'] ?? null;
    $borrower = new BorrowerInfo();
    $borrower_no = "BR-". str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    

    $borrower_info = [ 
        "first_name"   => $first_name,
        "last_name"    => $last_name,
        "borrower_type"  => $borrower_type,
        "borrower_no" => $borrower_no
    ];
    $borrower_info = [ 
        "first_name"   => $first_name,
        "last_name"    => $last_name,
        "borrower_type"  => $borrower_type,
        "borrower_no" => $borrower_no
    ];


    $borrower->insert_student_info($borrower_info);
    header("Location: index.php");
    exit;
}
    
            




    





?>