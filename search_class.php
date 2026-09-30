<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/database.php';
$input = trim(($_POST['input'] ?? null));

class SearchResults extends Database{
    public function __construct() {
    parent::__construct();
    }
    public function search_query($input) {
    $query = "
        SELECT *
        FROM {$this->query_config['tables']['students']} AS students
        WHERE (
            students.first_name LIKE :search 
            OR students.last_name LIKE :search 
            OR students.student_code LIKE :search
            OR students.id LIKE :search
            OR students.netlead_fname LIKE :search
            OR students.netlead_lname LIKE :search 
        )
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
        'search' => '%' . $input . '%']);
    
    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
    $row_num = count($results);
    
    try {
        if ($row_num > 0) { ?>
            <div class="student-dashboard" id="student-dashboard">
                <div class="dashboard-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Student No.</th>
                                <th>Last Name</th>
                                <th>First Name</th> 
                                <th>Gender</th>
                                <th>Network Leader</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row->id) ?></td>
                                <td><?= htmlspecialchars($row->first_name) ?></td>
                                <td><?= htmlspecialchars($row->last_name) ?></td>
                                <td><?= htmlspecialchars($row->gender) ?></td>
                                <td><?= htmlspecialchars($row->netlead_fname . ' ' . $row->netlead_lname) ?></td>
                                <td class="action-cell">
                                    <button type="button" class="action-btn inquire-btn">More Info</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
<?php 
        } else {
            // Displays your custom card when a search yields zero matches
            echo "<div class='no-results-card'>";
            echo "<div class='no-results-icon' aria-hidden='true'>🔎</div>";
            echo "<h3>No results found</h3>";
            echo "<p>Try searching by another block number, lot number, or house ID.</p>";
            echo "</div>";
        }
    } catch(PDOException $e) { 
        if (isset($e->errorInfo) && $e->errorInfo[1] == 1064) {
            echo "<div class='no-results-card'>";
            echo "<div class='no-results-icon' aria-hidden='true'>🔎</div>";
            echo "<h3>No results found</h3>";
            echo "<p>Try searching by another block number, lot number, or house ID.</p>";
            echo "</div>";
        } else {
            die("Insert Error: " . $e->getMessage());
        }
    }
}
}
$search = new SearchResults;
$search->search_query($input);






?>
