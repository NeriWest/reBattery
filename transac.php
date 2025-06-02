<?php
include 'db.php';
$totalAmount = $totalClearance = $totalPermit = $totalResidency = 0;
$documentTypes = [];
$row = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $residentCode = $_POST['residentCode'] ?? '';

    $sql = "SELECT * FROM residents WHERE residentCode = '$residentCode'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        if (isset($_POST['submitBtn'])) {
            $permit = $_POST['permit'] ?? null;
            $clearance = $_POST['clearance'] ?? null;
            $residency = $_POST['residency'] ?? null;

            $isSingle = strtolower($row['civilStatus']);
            $today = new DateTime();
            $dob = new DateTime($row['dateOfBirth']);
            $dos = new DateTime($row['dateOfStay']);

            $age = $dob->diff($today)->y;
            $stay = $dos->diff($today)->y;

            if ($clearance == 'clearance') {
                $totalClearance += 150;
                if ($age < 18 || $age >= 60) {
                    // Free
                } elseif ($age <= 25) {
                    $totalClearance += 25;
                } else {
                    $totalClearance += 30;
                }
                $documentTypes[] = 'Barangay Clearance';
            }

            if ($permit == 'permit') {
                $totalPermit += 325;
                if ($isSingle == 'single') {
                    $totalPermit += 125;
                }
                $documentTypes[] = 'Business Permit';
            }

            if ($residency == 'residency') {
                $totalResidency += 200;
                if ($stay >= 10) {
                    $totalResidency += 50;
                } else {
                    $totalResidency += 100;
                }
                $documentTypes[] = 'Certificate of Residency';
            }

            $totalAmount = $totalClearance + $totalPermit + $totalResidency;
        }
    }
}

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
    <title>Transaction</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php if (!empty($row)): ?>
        <fieldset>
            <legend>REQUEST DOCUMENT</legend>
            <form action="" method="POST">
                <input type="hidden" name="residentCode" value="<?= $row['residentCode'] ?>">
                <table>
                    <tr>
                        <th>Fields</th>
                        <th>Values</th>
                    </tr>

                    <tr>
                        <td>
                            <p><strong>Resident Code:</strong>
                        </td>
                        <td> <?= $row['residentCode'] ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p><strong>Resident Name:</strong>
                        </td>
                        <td> <?= strtoupper($row['lastName'] . ', ' . $row['firstName'] . ' ' . $row['middleName']) ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p><strong>Date of Birth:</strong>
                        </td>
                        <td> <?= formatDate($row['dateOfBirth']) ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p><strong>Date of Stay:</strong>
                        </td>
                        <td> <?= formatDate($row['dateOfStay']) ?></p>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <p><strong>Civil Status:</strong>
                        </td>
                        <td> <?= $row['civilStatus'] ?></p>
                        </td>
                    </tr>

                </table>

                <p>Documents:</p>
                <ul>
                    <li><input type="checkbox" name="clearance" value="clearance"> Barangay Clearance</li>
                    <li><input type="checkbox" name="permit" value="permit"> Business Permit</li>
                    <li><input type="checkbox" name="residency" value="residency"> Certificate of Residency</li>
                </ul>
                <input type="submit" name="submitBtn" value="Transact">
                <input type="button" value="Cancel" onclick="window.location.href='index.php'">
            </form>
        </fieldset>

        <?php if (isset($_POST['submitBtn'])): ?>
            <fieldset>
                <legend>REQUESTED DOCUMENT</legend>
                <table>
                    <thead>
                        <tr>
                            <th>Fields</th>
                            <th>Values</th>
                        </tr>
                    </thead>
                    <td>
                        <p><strong>Resident Code:</strong>
                    </td>
                    <td> <?= $row['residentCode'] ?></p>
                    </td>
                    <tr>
                        <td><strong>Document Types:</strong></td>
                        <td>
                            <?php foreach ($documentTypes as $doc): ?>
                                <?= $doc ?><br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Amount Due:</strong></td>
                        <td><strong><?= number_format($totalAmount, 2) ?></strong></td>
                    </tr>
                </table>
            </fieldset>
        <?php endif; ?>
    <?php else: ?>
        <p>Resident not found. <a href="index.php">Back</a></p>
    <?php endif; ?>

</body>

</html>