<?php
session_start();
include("connect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
</head>
<body>
    <div style="text-align:center; padding:15%;">
        <p style="font-size:50px; font-weight:bold;">
            Hello
            <?php 
            if (isset($_SESSION['email'])) {
                $email = $_SESSION['email'];
                // Usar declarações preparadas para evitar injeção de SQL
                $stmt = $conn->prepare("SELECT name FROM logindb WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo $row['name'];
                }
            }
            ?> :)
        </p>
        <a href="logout.php">Logout</a>
    </div>
</body>
</html>
