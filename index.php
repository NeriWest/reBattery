<?php
include('db.php');

//INITIALIZE ALL VARIABLES
$firstName = null;
$middleName = null;
$lastName = null;
$dateOfBirth = null;
$dateOfStay = null;
$civilStatus = null;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $dateOfStay = $_POST['dateOfStay'];

    $civilStatus = $_POST['civilStatus'];
    $residentCode = strtoupper(substr($lastName, 0, 3)) . "-" . date('mdY', strtotime($dateOfStay)) .
        "-" . strtoupper(substr($civilStatus, 0, 1)) . "-" . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

    $sqlAdd = "INSERT INTO residents(residentCode, lastName, firstName, middleName, dateOfBirth, dateOfStay, civilStatus) VALUES ('$residentCode','$lastName', '$firstName', '$middleName', '$dateOfBirth', '$dateOfStay', '$civilStatus')";

    $stmt = $conn->prepare($sqlAdd);
    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        die("Error: " . $stmt->error);
    }

}

$search = $_GET['search'] ?? '';
if ($search) {
    $sql = "SELECT * FROM residents 
            WHERE CONCAT(firstName, ' ', middleName, ' ', lastName) LIKE '%$search%' 
            OR residentCode LIKE '%$search%' 
            ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM residents ORDER BY created_at DESC";
}
$result = $conn->query($sql);

// Function to format date as mm/dd/yyyy
function formatDate($dateString)
{
    if (empty($dateString) || $dateString == '0000-00-00')
        return 'N/A';
    $date = new DateTime($dateString);
    return $date->format('F j, Y'); // Example: September 2, 1977
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="content">
        <form method="POST" action="index.php">
            <fieldset>
                <legend>Add Resident</legend>

                <!-- First Name -->
                <label>First Name*:
                    <input type="text" name="firstName" value="<?= $firstName ?>" minlength="2" maxlength="35" required>
                </label><br>

                <!-- Middle Name -->
                <label>Middle Name:
                    <input type="text" name="middleName" value="<?= $middleName ?>" minlength="2" maxlength="35">
                </label><br>

                <!-- Last Name -->
                <label>Last Name*:
                    <input type="text" name="lastName" value="<?= $lastName ?>" minlength="2" maxlength="35" required>
                </label><br>

                <?php
                $maxDate = date('Y-m-d'); // today's date
                $minDate = date('Y-m-d', strtotime('-120 years')); // 120 years ago
                ?>
                <!-- Birthdate -->
                <label>Birthdate:
                    <input type="date" name="dateOfBirth" value="<?= $dateOfBirth ?>" min="<?php echo $minDate; ?>"
                        max="<?php echo $maxDate; ?>" required>
                </label><br>

                <!-- Date of Stay -->
                <label>Date of Stay:
                    <input type="date" name="dateOfStay" value="<?= $dateOfStay ?>" min="<?php echo $minDate; ?>"
                        max="<?php echo $maxDate; ?>" required>
                </label><br>

                <!-- Civil Status -->
                <label>Civil Status:
                    <select name="civilStatus" required>
                        <option value="Single" <?= ($civilStatus ?? '') == 'Single' ? 'selected' : '' ?>>Single</option>
                        <option value="Married" <?= ($civilStatus ?? '') == 'Married' ? 'selected' : '' ?>>Married</option>
                        <option value="Divorced" <?= ($civilStatus ?? '') == 'Divorced' ? 'selected' : '' ?>>Divorced
                        </option>
                        <option value="Separated" <?= ($civilStatus ?? '') == 'Separated' ? 'selected' : '' ?>>Separated
                        </option>
                        <option value="Widowed" <?= ($civilStatus ?? '') == 'Widow/Widower' ? 'selected' : '' ?>>
                            Widow/Widower</option>
                    </select>
                </label><br><br>
                <input type="submit" name="add" value="Add Resident">
            </fieldset>
        </form>
        <div class="center">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <input type="submit" value="Search">
                <a href="index.php">Reset</a>
            </form>
        </div>
        <br>
        <?php if ($result && $result->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Resident Code</th>
                        <th>Resident Name</th>
                        <th>Date of Birth</th>
                        <th>Date of Stay</th>
                        <th>Civil Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['residentCode'] ?: 'N/A') ?></td>
                            <td>
                                <?= htmlspecialchars(
                                    ($row['firstName'] ?? '') . ' ' .
                                    ($row['middleName'] ?? '') . ' ' .
                                    ($row['lastName'] ?? '')
                                ) ?>
                            </td>
                            <td><?= formatDate($row['dateOfBirth'] ?? '') ?></td>
                            <td><?= formatDate($row['dateOfStay'] ?? '') ?></td>
                            <td><?= htmlspecialchars($row['civilStatus'] ?: 'N/A') ?></td>
                            <td class="table-actions">
                                <div class="editButton center">
                                    <a href="edit.php?residentsID=<?= $row['residentsID'] ?>">Update</a>
                                </div><br>
                                <div>
                                    <form action="transac.php" method="POST">
                                        <input type="hidden" name="residentCode" value="<?= $row['residentCode'] ?>">
                                        <button class="transacButton" type="submit" name="transactBtn">Transact</button>
                                    </form>
                                </div>
                                <form action="delete.php" method="POST">
                                    <input type="hidden" name="residentsID" value="<?= $row['residentsID'] ?>">
                                    <button class='deleteButton' type="submit" name="delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="center">No Residents Found.</p>
        <?php endif; ?>
    </div>
</body>

</html>