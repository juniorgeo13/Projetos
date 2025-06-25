<?php 
session_start();
include 'conexao.php'; 
 

?>
<!DOCTYPE html> 
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVAC</title>
    <link rel="stylesheet" href="css/coluna.css">
    
</head>
<body style="height: 100vh;">

    <main class="two-column-layout">
        <!-- Coluna do formulário de login -->
        <div class="login-container">
            <h2>SIVAC - Login</h2>
            <?php if (!empty($erro)): ?>
                <div style="color:red; margin-bottom:10px;"><?php echo $erro; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST" class="login-form">
                <label for="username">Nome de usuário:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>

                <div style="display: flex; gap: 10px;">
                    <button type="submit">Entrar</button>
                    <a href="cadastro.php" style="text-decoration: none;">
                    <button type="button">Cadastrar</button>
                    </a>
                </div>
            </form>

            
        </div>

        <!-- Coluna da imagem -->
        <div class="image-column">
            <img src="./images/login-bg.jpg" alt="Bem-vindo ao Portal de Vagas">
        </div>
    </main>

</body>
</html>
