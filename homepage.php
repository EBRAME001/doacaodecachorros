<?php
session_start();
include("connect.php");

// Adicionar Cachorro
if (isset($_POST['add'])) {
    $nome = $_POST['nome'];
    $raça = $_POST['raça'];
    $idade = $_POST['idade'];
    $peso = $_POST['peso'];
    $foto = null;

    // Tratamento da foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO Cachorro (nome, raça, idade, peso, foto) VALUES (?, ?, ?, ?, ?)");
    $null = NULL; // Usado para bind no tipo blob
    $stmt->bind_param("ssidb", $nome, $raça, $idade, $peso, $null);
    if ($foto !== null) {
        $stmt->send_long_data(4, $foto);
    }
    if ($stmt->execute()) {
        echo "Cachorro adicionado com sucesso!<br>";
    } else {
        echo "Erro ao adicionar cachorro: " . $stmt->error;
    }
}

// Atualizar Cachorro
if (isset($_POST['update'])) {
    $nome_original = $_POST['cachorro_nome'];
    $nome = $_POST['nome'];
    $raça = $_POST['raça'];
    $idade = $_POST['idade'];
    $peso = $_POST['peso'];
    $foto = null;

    // Preparar consulta sem a foto inicialmente
    $query = "UPDATE Cachorro SET nome = ?, raça = ?, idade = ?, peso = ? WHERE nome = ?";
    $types = "ssids";  // Tipos para o bind_param

    // Se uma nova foto foi enviada, incluir na atualização
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $foto = file_get_contents($_FILES['foto']['tmp_name']);
        $query = "UPDATE Cachorro SET nome = ?, raça = ?, idade = ?, peso = ?, foto = ? WHERE nome = ?";
        $types = "ssidbs";
    }

    $stmt = $conn->prepare($query);
    if ($foto !== null) {
        $stmt->bind_param($types, $nome, $raça, $idade, $peso, $foto, $nome_original);
        $stmt->send_long_data(4, $foto);
    } else {
        $stmt->bind_param($types, $nome, $raça, $idade, $peso, $nome_original);
    }

    if ($stmt->execute()) {
        echo "Cachorro atualizado com sucesso!<br>";
    } else {
        echo "Erro ao atualizar cachorro: " . $stmt->error;
    }
}

// Deletar Cachorro
if (isset($_POST['delete'])) {
    $nome = $_POST['cachorro_nome'];
    $stmt = $conn->prepare("DELETE FROM Cachorro WHERE nome = ?");
    $stmt->bind_param("s", $nome);
    if ($stmt->execute()) {
        echo "Cachorro deletado com sucesso!<br>";
    } else {
        echo "Erro ao deletar cachorro: " . $stmt->error;
    }
}

// Buscar todos os cachorros para o dropdown
$stmt = $conn->prepare("SELECT id_cachorro, nome FROM Cachorro");
$stmt->execute();
$result = $stmt->get_result();
$cachorros = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Cachorros</title>
    <link rel="stylesheet" href="homepage.css">
</head>
<body>
    <h1>Gestão de Cachorros</h1>
    <h2>Adicionar Cachorro</h2>
    <form method="post" enctype="multipart/form-data">
        Nome: <input type="text" name="nome" required><br>
        Raça: <input type="text" name="raça" required><br>
        Idade: <input type="number" name="idade" required><br>
        Peso: <input type="text" name="peso" required><br>
        Foto: <input type="file" name="foto"><br>
        <input type="submit" name="add" value="Adicionar Cachorro">
    </form>

    <h2>Atualizar ou Deletar Cachorro</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="cachorro_nome">Escolha um Cachorro:</label>
        <select name="cachorro_nome" required>
            <?php foreach ($cachorros as $cachorro) {
                echo "<option value='{$cachorro['nome']}'>{$cachorro['nome']}</option>";
            } ?>
        </select><br>
        Nome: <input type="text" name="nome"><br>
        Raça: <input type="text" name="raça"><br>
        Idade: <input type="number" name="idade"><br>
        Peso: <input type="text" name="peso"><br>
        Foto: <input type="file" name="foto"><br>
        <input type="submit" name="update" value="Atualizar Cachorro">
        <input type="submit" name="delete" value="Deletar Cachorro">
    </form>

    <br>
    <a href="visaoDoUsuarioAosCachorros.php">Visão do Usuário dos Cachorros</a>
</body>
</html>
