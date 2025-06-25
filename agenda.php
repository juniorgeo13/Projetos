<?php

include ('conexao.php');
session_start();

    if (!isset($_SESSION['id']) || $_SESSION['nivel'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
$msg = '';
$cpf_pesquisa = $_GET['cpf'] ?? '';

// Atualiza a próxima consulta se enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['data_proxima_consulta'])) {
    $id = $_POST['id'];
    $nova_data = $_POST['data_proxima_consulta'];

    // Verifica se já existe registro
    $check = $conn->prepare("SELECT 1 FROM prontuario2025 WHERE id=?");
    $check->bind_param("i", $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Atualiza
        $stmt = $conn->prepare("UPDATE prontuario2025 SET data_proxima_consulta=? WHERE id=?");
        $stmt->bind_param("si", $nova_data, $id);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success text-center'>Consulta atualizada com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger text-center'>Erro ao atualizar consulta!</div>";
        }
        $stmt->close();
    } else {
        // Insere
        $stmt = $conn->prepare("INSERT INTO prontuario2025 (id, data_proxima_consulta) VALUES (?, ?)");
        $stmt->bind_param("is", $id, $nova_data);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success text-center'>Consulta cadastrada com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger text-center'>Erro ao cadastrar consulta!</div>";
        }
        $stmt->close();
    }
    $check->close();
}

// Consulta paciente pelo CPF
$paciente = null;
if ($cpf_pesquisa) {
    $sql = "SELECT pc.id, pc.nome_completo, p.data_proxima_consulta
            FROM pacientecadastro pc
            LEFT JOIN prontuario2025 p ON pc.id = p.id
            WHERE pc.cpf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cpf_pesquisa);
    $stmt->execute();
    $result = $stmt->get_result();
    $paciente = $result->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVAC - Agenda</title>
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

    <main>
        <div class="container">
            <h2>Agenda de Consultas</h2>
            <!-- Exemplo de formulário de pesquisa -->
            <form method="get" action="">
                <label for="cpf">Pesquisar por CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cpf_pesquisa); ?>" required>
                <button type="submit" class="btn btn-primary">Pesquisar</button>
            </form>
            <!-- Espaço para mensagens -->
            <?php if ($msg) echo $msg; ?>
            <!-- Aqui você pode listar as consultas ou mostrar resultados da pesquisa -->
            <?php if ($cpf_pesquisa): ?>
                <?php if ($paciente): ?>
                    <form method="post" action="">
                        <input type="hidden" name="id" value="<?php echo $paciente['id']; ?>">
                        <table class="table table-bordered" style="width:100%; margin-top:20px;">
                            <tr>
                                <th>Nome</th>
                                <td><?php echo htmlspecialchars($paciente['nome_completo']); ?></td>
                            </tr>
                            <tr>
                                <th>Data da Próxima Consulta</th>
                                <td>
                                    <input type="date" name="data_proxima_consulta" value="<?php echo htmlspecialchars($paciente['data_proxima_consulta']); ?>" required>
                                    <button type="submit" class="btn btn-primary btn-sm" style="margin-left:10px;">Salvar</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                <?php else: ?>
                    <div style="text-align:center;">Nenhum paciente encontrado para o CPF informado.</div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>