<?php
// NÃO INCLUIR verificar.php aqui!!!
include ('conexao.php');

// Mensagem de sucesso ou erro
$msg = " ";

// Salvar novo cadastro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_completo'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $data = $_POST['data_nascimento'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';

    if ($nome && $cpf && $data) {
        $stmt = $conn->prepare("INSERT INTO pacientecadastro (nome_completo, cpf, data_nascimento, email, telefone, endereco) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $nome, $cpf, $data, $email, $telefone, $endereco);
        if ($stmt->execute()) {
            $msg = "<div class='msg-success'>Cadastro realizado com sucesso!</div>";
        } else {
            $msg = "<div class='msg-success' style='color:red;'>Erro ao cadastrar: " . $conn->error . "</div>";
        }
        $stmt->close();
    } else {
        $msg = "<div class='msg-success' style='color:red;'>Preencha os campos obrigatórios!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVAC - Cadastro</title>
    <link rel="stylesheet" href="css/coluna.css">
</head>
<body>
    <header>
        <nav id="menuHorizontal">
            <ul>
                <li><a href="index.php">Início</a></li>    
                <li><a href="agenda.php">Agenda</a></li>
                <li><a href="prontuario.php">Prontuário</a></li>
                <li><a href="cadastro.php">Cadastro de Usuário</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" style="max-width:500px;">
        <h2 style="text-align:center; margin-top:30px;">Cadastro de Paciente</h2>
        <?php if ($msg) echo $msg; ?>
        <form method="post" action="">
            <label for="nome_completo">Nome Completo:</label>
            <input type="text" id="nome_completo" name="nome_completo" required>

            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required>

            <label for="data_nascimento">Data de Nascimento:</label>
            <input type="date" id="data_nascimento" name="data_nascimento" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email">

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone">

            <label for="endereco">Endereço:</label>
            <input type="text" id="endereco" name="endereco">

            <button type="submit" class="btn btn-primary" style="margin-top:15px;">Salvar</button>
        </form>
    </div>
</body>
</html>
