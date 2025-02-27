<?php
session_start();
include 'db.php';

if (!isset($_SESSION['voter_id'])) {
    header("Location: login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Fetch candidates
$candidates = $conn->query("SELECT * FROM candidates");

// Count how many times the voter has voted
$vote_count = $conn->query("SELECT COUNT(*) as total FROM votes WHERE voter_id = $voter_id")->fetch_assoc()['total'];

// Check if the voter has already voted
if ($vote_count > 0) {
    // Disable voting if they have already voted
    $voting_disabled = true;
} else {
    $voting_disabled = false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['candidate_ids']) && count($_POST['candidate_ids']) == 2) {
        foreach ($_POST['candidate_ids'] as $candidate_id) {
            $checkVote = $conn->query("SELECT * FROM votes WHERE voter_id = $voter_id AND candidate_id = $candidate_id");

            if ($checkVote->num_rows == 0) {
                $conn->query("INSERT INTO votes (voter_id, candidate_id) VALUES ($voter_id, $candidate_id)");
            }
        }
        
        $success = "Your votes have been submitted!";

        echo "<script>window.location.href='vote.php?msg=".$success."'</script>";

    } else {
        $error = "You must select exactly 2 candidates!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Vote</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function limitSelection() {
            let checkboxes = document.querySelectorAll('input[name="candidate_ids[]"]');
            let checkedBoxes = Array.from(checkboxes).filter(box => box.checked);

            if (checkedBoxes.length >= 2) {
                checkboxes.forEach(box => {
                    if (!box.checked) {
                        box.disabled = true;
                    }
                });
            } else {
                checkboxes.forEach(box => {
                    box.disabled = false;
                });
            }
        }
    </script>
</head>
<body class="container mt-5">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">PPCC VOTING SYSTEM</h2>
            <?php 
                $s="SELECT voterName FROM voters WHERE id = $voter_id";
                $q=$conn->query($s); 
                $r=$q->fetch_assoc();
            ?>

            <form method="post" class="text-center">
                <p><strong><?php echo $r['voterName']; ?></strong></p>
                <p>Select 2 candidates</p><br>

                <style>
                    .form-check-input {
                        width: 30px;
                        height: 30px;
                        transform: scale(1.5);
                        margin-right: 15px;
                    }

                    .form-check-label {
                        font-size: 1.8rem;
                    }

                    .candidate-list {
                        display: flex;
                        flex-direction: column;
                        gap: 15px;
                        align-items: center;
                    }
                </style>

                <style>
                    .btn-square {
                        border-radius: 5px;
                        padding: 10px 20px;
                        font-size: 1.2rem;
                    }
                </style>

                <div class="mb-3 candidate-list">
                    <?php while ($row = $candidates->fetch_assoc()): ?>
                        <div class="form-check d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" name="candidate_ids[]" value="<?= $row['id'] ?>" onclick="limitSelection()" <?= $voting_disabled ? 'disabled' : '' ?>>
                            <label class="form-check-label"><?= $row['name'] ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <br><br>
                <?php if ($voting_disabled): ?>
                    <p class="text-warning">Thank you for voting!<br>You have already voted. You cannot vote again.</p>
                <?php else: ?>
                    <button type="submit" class="btn btn-success btn-square" onclick="return confirm('SUBMIT VOTE?')">Submit Vote</button>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger btn-square" onclick="return confirm('LOGOUT?')">Logout</a>
            </form>

            <?php if (isset($success)) echo "<p class='text-success mt-3 text-center'>$success</p>"; ?>
            <?php if (isset($error)) echo "<p class='text-danger mt-3 text-center'>$error</p>"; ?>
        </div>
    </div>
</body>
</html>
