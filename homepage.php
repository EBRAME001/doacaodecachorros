<?php
session_start();
include("connect.php");

// Processamento de adicionar, atualizar e deletar cachorros
if (isset($_POST['add'])) {
    $nome = $_POST['nome'];
    $raça = $_POST['raça'];
    $idade = $_POST['idade'];
    $peso = $_POST['peso'];

    // Verifica se já existe um cachorro com o mesmo nome, peso e idade
    $stmt = $conn->prepare("SELECT * FROM Cachorro WHERE nome = ? AND peso = ? AND idade = ?");
    $stmt->bind_param("sdi", $nome, $peso, $idade);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "Um cachorro com o mesmo nome, idade e peso já existe.<br>";
    } else {
        $stmt = $conn->prepare("INSERT INTO Cachorro (nome, raça, idade, peso) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssid", $nome, $raça, $idade, $peso);
        if ($stmt->execute()) {
            echo "Cachorro adicionado com sucesso!<br>";
        } else {
            echo "Erro ao adicionar cachorro: " . $stmt->error;
        }
    }
}

if (isset($_POST['update'])) {
    $nome_original = $_POST['cachorro_nome'];
    $nome = $_POST['nome'];
    $raça = $_POST['raça'];
    $idade = $_POST['idade'];
    $peso = $_POST['peso'];

    $stmt = $conn->prepare("UPDATE Cachorro SET nome = ?, raça = ?, idade = ?, peso = ? WHERE nome = ?");
    $stmt->bind_param("ssids", $nome, $raça, $idade, $peso, $nome_original);
    if ($stmt->execute()) {
        echo "Cachorro atualizado com sucesso!<br>";
    } else {
        echo "Erro ao atualizar cachorro: " . $stmt->error;
    }
}

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

// Buscar todos os cachorros para o dropdown APÓS as operações de CRUD
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
</head>
<body>
    <h1>Gestão de Cachorros</h1>
    <h2>Adicionar Cachorro</h2>
    <form method="post">
        Nome: <input type="text" name="nome" required><br>
        Raça: <input type="text" name="raça" required><br>
        Idade: <input type="number" name="idade" required><br>
        Peso: <input type="text" name="peso" required><br>
        <input type="submit" name="add" value="Adicionar Cachorro">
    </form>

    <h2>Atualizar ou Deletar Cachorro</h2>
    <form method="post">
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
        <input type="submit" name="update" value="Atualizar Cachorro">
        <input type="submit" name="delete" value="Deletar Cachorro">
    </form>

    <br>
    <a href="visaoDoUsuarioAosCachorros.php">Visão do Usuário dos Cachorros</a>
</body>
</html>
