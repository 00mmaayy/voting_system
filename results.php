<?php
include 'db.php';

$results = $conn->query("
    SELECT candidates.name, COUNT(votes.id) AS vote_count
    FROM candidates
    LEFT JOIN votes ON candidates.id = votes.candidate_id
    GROUP BY candidates.id
    ORDER BY vote_count DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Voting Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .center-container {
            min-height: 100vh;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center center-container">
    <div class="text-center" style="width: 80%; max-width: 800px;">
        <h2 class="mb-4">Voting Results</h2>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Candidate</th>
                    <th>Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['vote_count'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

