<?php
session_start();
require_once("funcoes.php"); // Funções e conexão
$pdo = conectarBanco();      // Conecta ao banco sistema_login

// Teste de conexão
if ($pdo) {
    echo "<div style='color: green; text-align: center;'>Conexão com o banco realizada com sucesso!</div>";
} else {
    echo "<div style='color: red; text-align: center;'>Falha na conexão com o banco de dados.</div>";
}

$mensagem = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $nivel = $_POST['nivel'] ?? 'usuario';

    if (usuarioExiste($pdo, $usuario)) {
        $mensagem = "Nome de usuário já existe. Escolha outro.";
    } else {
        if (cadastrarUsuario($pdo, $usuario, $senha, $nivel)) {
            $mensagem = " Usuário cadastrado com sucesso!";
        } else {
            $mensagem = " Erro ao cadastrar usuário.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 400px;
            margin-top: 80px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Cadastrar Novo Usuário</h2>
    <?php if (!empty($mensagem)): ?>
        <div class="alert alert-info"><?php echo $mensagem; ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>Usuário</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Nível de Acesso</label>
            <select name="nivel" class="form-control">
                <option value="usuario">Usuário</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Cadastrar</button>
        <a href="login.php" class="btn btn-secondary btn-block mt-2">Voltar ao Login</a>
    </form>
</div>
</body>
</html>
