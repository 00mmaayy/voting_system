<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_code = $_POST['user_code'];
    $passkey = $_POST['passkey'];

    // Check voter credentials
    $sql = "SELECT * FROM voters WHERE user_code = '$user_code' AND passkey = '$passkey'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $voter = $result->fetch_assoc();
        $_SESSION['voter_id'] = $voter['id'];
        echo "<script>window.location.href = 'vote.php';</script>";
        //header("Location: vote.php");
        exit();
    } else {
        $error = "Invalid User Code or Passkey!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Voter Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .center-container {
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center center-container">
    <div class="text-center w-100" style="max-width: 400px;">
    <h2 class="mb-4">PALAWAN PEOPLES CREDIT COOP</h2>
        <form method="post" class="p-4 border rounded shadow bg-light">
            <div class="mb-3">
                <label class="form-label">User Code</label>
                <input type="text" name="user_code" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Passkey</label>
                <input type="password" name="passkey" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
            <?php if (isset($error)) echo "<p class='text-danger mt-2'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>

