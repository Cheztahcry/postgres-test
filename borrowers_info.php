<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/owner_info_style.css">
</head>
<body>
    <div class="student form">
        <div class="container">
            <div class="page-header">
                <nav class="page-nav">
                    <a class="nav-link" href="index.php">Home</a>
                </nav>
            </div>
            <h1>Borrower Information</h1>
            <form action="borrowers_info_class.php" method="post" id="owner-enrollment">
                <div class="field-group">
                    <label>Full Name</label>
                    <div class="field-row">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="text" name="first_name" placeholder="First Name" required>
                        </label>
                    </div>
                </div>
                <div class="field-group">
                    <label>Borrower Type</label>
                    <div class="field-row">
                        <label>
                            <input type="radio" name="borrower_type" value="Student"> Student
                        </label>
                        <label>
                            <input type="radio" name="borrower_type" value="Faculty"> Faculty/Teacher
                        </label>
                        <label>
                            <input type="radio" name="borrower_type" value="Staff"> Staff
                        </label>
                        <label>
                            <input type="radio" name="borrower_type" value="Guest"> Guest
                        </label>
                    </div>

                </div>
                <div class="field-group">
                    <label for="equipment_id">Select Equipment</label>
                    <div class="field-row">
                        <select name="equipment_id" id="equipment_id" required>
                            <option value="" disabled selected>-- Choose an item --</option>
                            <option value="1">Projector (Epson EB-X05) - Stock: 5</option>
                            <option value="2">HDMI Cable (2m) - Stock: 12</option>
                            <option value="3">Wireless Presenter / Clicker - Stock: 3</option>
                            <option value="4">Microphone & Stand - Stock: 2</option>
                        </select>
                        
                        <input type="number" name="quantity" min="1" max="10" placeholder="Qty" value="1" style="width: 80px;" required>
                    </div>
                </div>

                <div class="field-group">
                    <label for="due_date">Expected Return Date</label>
                    <div class="field-row">
                        <input type="date" id="due_date" name="due_date" required>
                    </div>
                </div>
                <div class="button-row">
                    <button type="submit" name="submit" id="submit">Next Section</button>
                    <button type="button" id="clear">Clear Fields</button>
                </div>

            </form>
        </div>
    </div>
    <script src="js/owner_info.js"></script>
    <script src="js/duplicate_handler.js" defer></script>

</body>
</html>