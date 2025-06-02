<?php
include('db.php');

$residentID = $_GET['residentsID'] ?? null;

if (!$residentID) {
    die("Invalid resident ID.");
}

$sql = "SELECT * FROM residents WHERE residentsID = $residentID";
$result = $conn->query($sql);
$resident = $result->fetch_assoc();

if (!$resident) {
    die("Resident not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $firstName = $_POST['firstName'];
    $middleName = $_POST['middleName'] ?? '';
    $lastName = $_POST['lastName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $dateOfStay = $_POST['dateOfStay'];
    $civilStatus = $_POST['civilStatus'];
    // $residentCode = strtoupper(substr($lastName, 0, 3)) . "-" . date('mdY', strtotime($dateOfStay)) .
    //     "-" . strtoupper(substr($civilStatus, 0, 1)) . "-" . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

    $sqlUpdate = "UPDATE residents SET 
        -- residentCode = '$residentCode',
        firstName = '$firstName', 
        middleName = '$middleName', 
        lastName = '$lastName', 
        dateOfBirth = '$dateOfBirth', 
        dateOfStay = '$dateOfStay', 
        civilStatus = '$civilStatus' 
        WHERE residentsID = $residentID";

    if ($conn->query($sqlUpdate)) {
        header("Location: index.php");
        exit();
    } else {
        die("Error updating record: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Resident</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="topNav">
        <a href="index.php">Go Back</a>
    </div><br><br>

    <div class="content">
        <form method="POST" action="">
            <fieldset>
                <legend>Edit Resident</legend>

                <label>First Name*:
                    <input type="text" name="firstName" value="<?= htmlspecialchars($resident['firstName']) ?>"
                        required>
                </label><br>

                <label>Middle Name:
                    <input type="text" name="middleName" value="<?= htmlspecialchars($resident['middleName']) ?>">
                </label><br>

                <label>Last Name*:
                    <input type="text" name="lastName" value="<?= htmlspecialchars($resident['lastName']) ?>" required>
                </label><br>

                <?php
                $maxDate = date('Y-m-d');
                $minDate = date('Y-m-d', strtotime('-120 years'));
                ?>
                <label>Birthdate:
                    <input type="date" name="dateOfBirth" value="<?= $resident['dateOfBirth'] ?>" min="<?= $minDate ?>"
                        max="<?= $maxDate ?>" required>
                </label><br>

                <label>Date of Stay:
                    <input type="date" name="dateOfStay" value="<?= $resident['dateOfStay'] ?>" min="<?= $minDate ?>"
                        max="<?= $maxDate ?>" required>
                </label><br>

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

                <input type="submit" name="update" value="Update Resident">
            </fieldset>
        </form>
    </div>
</body>

</html>