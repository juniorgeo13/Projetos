<?php

include ('conexao.php');
session_start();

    if (!isset($_SESSION['id']) || $_SESSION['nivel'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
$msg = '';

// Atualiza ou exclui paciente se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Se clicou em excluir
    if (isset($_POST['excluir'])) {
        $stmt = $conn->prepare("DELETE FROM pacientecadastro WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $msg = "<div class='alert alert-danger text-center'>Usuário excluído com sucesso!</div>";
    } else {
        // Atualiza normalmente
        $nome = $_POST['nome_completo'] ?? '';
        $cpf = $_POST['cpf'] ?? '';
        $data_nascimento = $_POST['data_nascimento'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $endereco = $_POST['endereco'] ?? '';

        $stmt = $conn->prepare("UPDATE pacientecadastro SET nome_completo=?, cpf=?, data_nascimento=?, email=?, telefone=?, endereco=? WHERE id=?");
        $stmt->bind_param("ssssssi", $nome, $cpf, $data_nascimento, $email, $telefone, $endereco, $id);
        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success text-center'>Usuário salvo com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger text-center'>Erro ao salvar paciente!</div>";
        }
        $stmt->close();
    }
}

// Consulta todos os pacientes cadastrados
$sql = "SELECT id, nome_completo, cpf, data_nascimento, email, telefone, endereco FROM pacientecadastro ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVAC - Editar Cadastro</title>
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

    <div class="container">
        <h2 style="text-align:center; margin-top:30px;">Editar Cadastro</h2>
        <?php if ($msg) echo $msg; ?>
        <table class="table table-bordered table-striped tabela-paciente">
            <thead>
                <tr>
                    <th>Nome Completo</th>
                    <th>CPF</th>
                    <th>Data de Nascimento</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <form method="post" action="editarcadastro.php">
                        <tr>
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <td><input type="text" name="nome_completo" value="<?php echo htmlspecialchars($row['nome_completo']); ?>" class="form-control" required></td>
                            <td><input type="text" name="cpf" value="<?php echo htmlspecialchars($row['cpf']); ?>" class="form-control" required></td>
                            <td><input type="date" name="data_nascimento" value="<?php echo htmlspecialchars($row['data_nascimento']); ?>" class="form-control" required></td>
                            <td><input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" class="form-control"></td>
                            <td><input type="text" name="telefone" value="<?php echo htmlspecialchars($row['telefone']); ?>" class="form-control"></td>
                            <td><input type="text" name="endereco" value="<?php echo htmlspecialchars($row['endereco']); ?>" class="form-control"></td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                                <button type="submit" name="excluir" value="1" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este paciente?');">Excluir</button>
                            </td>
                        </tr>
                        </form>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center;">Nenhum paciente cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
