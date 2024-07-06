<?php
include 'connect.php';

if (isset($_POST['signUp'])) {
    $name = $_POST['fName'] . ' ' . $_POST['lName']; // Concatenando primeiro e último nome
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash seguro para senha

    // Verificar se o e-mail já existe usando declarações preparadas
    $stmt = $conn->prepare("SELECT * FROM logindb WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        // Inserir novo usuário na logindb
        $stmt = $conn->prepare("INSERT INTO logindb (name, email, password, isAdmin, status) VALUES (?, ?, ?, 0, 1)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            // Inserir na tabela Dono após registro bem-sucedido
            $id_logindb = $conn->insert_id; // ID gerado para o novo usuário
            $stmt = $conn->prepare("INSERT INTO Dono (id_logindb, nome, email) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id_logindb, $name, $email);
            if ($stmt->execute()) {
                header("location: index.php");
                exit();
            } else {
                echo "Erro ao inserir no dono: " . $conn->error;
            }
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>



<?php

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM logindb WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['email'] = $user['email'];
            $_SESSION['id_logindb'] = $user['id'];

            // Atualizar informações no Dono após login bem-sucedido
            $stmt = $conn->prepare("UPDATE Dono SET nome = ?, email = ? WHERE id_logindb = ?");
            $stmt->bind_param("ssi", $user['name'], $email, $user['id']);
            $stmt->execute();

            header("Location: homepage.php");
            exit();
        } else {
            echo "Incorrect Password";
        }
    } else {
        echo "Email Not Found";
    }
}

?>
