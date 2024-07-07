<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Veja nossos cães disponíveis</title>
    <link rel="stylesheet" href="visaoDoUsuarioAosCachorros.css">
</head>
<body>
    <div class="container">
        <!-- Cachorros serão inseridos aqui pelo JavaScript -->
    </div>

    <script>
    const container = document.querySelector('.container');

    fetch('http://localhost/doacaodecachorros/api.php')  // Substitua com a URL correta da sua API
        .then(response => response.json())
        .then(cachorros => {
            cachorros.forEach(cachorro => {
                const div = document.createElement('div');
                div.className = 'grid-item';
                let htmlContent = `
                    <h3>${cachorro.nome}</h3>
                    <p>Raça: ${cachorro.raça}</p>
                    <p>Idade: ${cachorro.idade} anos</p>
                    <p>Peso: ${cachorro.peso} kg</p>
                `;
                // Verifica se a foto existe e adiciona ao conteúdo HTML
                if (cachorro.foto) {
                    htmlContent += `<img src="${cachorro.foto}" alt="Foto de ${cachorro.nome}" style="width: 100%;">`;
                }
                div.innerHTML = htmlContent;
                container.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Erro ao buscar dados:', error);
            container.innerHTML = '<p>Não foi possível carregar os dados.</p>';
        });
</script>
</body>
</html>
