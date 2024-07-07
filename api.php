<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include 'connect.php'; // Inclui o script de conexão

// Verifica se a conexão foi estabelecida (opcional se o connect.php já lida com erros de conexão)
if ($conn->connect_error) {
    echo json_encode(['error' => 'Falha na conexão com o banco de dados: ' . $conn->connect_error]);
    exit;
}

// Consulta para buscar os dados dos cachorros
$query = "SELECT nome, raça, idade, peso, foto FROM Cachorro";
$result = $conn->query($query);

if ($result) {
    $cachorros = [];
    while ($row = $result->fetch_assoc()) {
        // Codifica a foto em base64 se ela não estiver vazia
        if (!empty($row['foto'])) {
            $row['foto'] = 'data:image/jpeg;base64,' . base64_encode($row['foto']);
        } else {
            $row['foto'] = null; // Garante que o campo foto sempre exista, mesmo que seja nulo
        }
        $cachorros[] = $row;
    }
    echo json_encode($cachorros);
} else {
    echo json_encode(['error' => 'Erro ao realizar a consulta: ' . $conn->error]);
}

$conn->close();
?>
