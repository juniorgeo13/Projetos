<?php

include ('conexao.php');
session_start();

    if (!isset($_SESSION['id']) || $_SESSION['nivel'] !== 'admin') {
        header('Location: login.php');
        exit;
    }
// Busca por CPF
$cpf = $_GET['cpf'] ?? '';
$id = null;
$nome = $data_nascimento = '';
$peso = $altura = $vacinas_aplicadas = $data_proxima_vacina = $data_proxima_consulta = $receita = $observacoes = '';
$msg = '';

// Se pesquisou por CPF, busca paciente e prontuário
if ($cpf) {
    // Busca paciente pelo CPF
    $stmt = $conn->prepare("SELECT id, nome_completo, data_nascimento FROM pacientecadastro WHERE cpf = ?");
    $stmt->bind_param("s", $cpf);
    $stmt->execute();
    $stmt->bind_result($id, $nome, $data_nascimento);
    $stmt->fetch();
    $stmt->close();

    // Se encontrou paciente, busca prontuário
    if ($id) {
        $stmt = $conn->prepare("SELECT peso, altura, vacinas_aplicadas, data_proxima_vacina, data_proxima_consulta, receita, observacoes FROM prontuario2025 WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($peso, $altura, $vacinas_aplicadas, $data_proxima_vacina, $data_proxima_consulta, $receita, $observacoes);
        $stmt->fetch();
        $stmt->close();
    }
}

// Calcula idade em anos e meses, se houver data de nascimento
$idade = '';
if ($data_nascimento) {
    $data_nasc = new DateTime($data_nascimento);
    $hoje = new DateTime();
    $intervalo = $hoje->diff($data_nasc);
    $anos = $intervalo->y;
    $meses = $intervalo->m;
    $idade = $anos . ' anos';
    if ($meses > 0) {
        $idade .= ' e ' . $meses . ' meses';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVAC - Prontuário (Leitura)</title>
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
                <li><a href="login.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <div class="container" style="max-width:500px;">
        <h2 style="text-align:center; margin-top:30px;">Prontuário (Leitura)</h2>
        
        <!-- Campo de pesquisa de usuário por CPF -->
        <form method="get" action="" style="margin-bottom:20px;">
            <label for="cpf">Pesquisar por CPF:</label>
            <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($cpf); ?>" required>
            <button type="submit" class="btn btn-secondary">Pesquisar</button>
        </form>

        <!-- Exibe nome e idade se encontrados -->
        <?php if ($nome): ?>
            <div style="margin-bottom:15px;">
                <strong>Nome:</strong> <?php echo htmlspecialchars($nome); ?><br>
                <strong>Idade:</strong> <?php echo htmlspecialchars($idade); ?>
            </div>

            <table class="table table-bordered tabela-paciente" style="width:100%;">
                <tr><th>Peso (kg)</th><td><?php echo htmlspecialchars($peso); ?></td></tr>
                <tr><th>Altura (cm)</th><td><?php echo htmlspecialchars($altura); ?></td></tr>
                <tr><th>Vacinas Aplicadas</th><td><?php echo nl2br(htmlspecialchars($vacinas_aplicadas)); ?></td></tr>
                <tr>
                    <th>Próx. Vacina</th>
                    <td>
                        <?php
                        if ($data_proxima_vacina && $data_proxima_vacina != '0000-00-00') {
                            echo date('d/m/Y', strtotime($data_proxima_vacina));
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Próx. Consulta</th>
                    <td>
                        <?php
                        if ($data_proxima_consulta && $data_proxima_consulta != '0000-00-00') {
                            echo date('d/m/Y', strtotime($data_proxima_consulta));
                        }
                        ?>
                    </td>
                </tr>
                <tr><th>Receita</th><td><?php echo nl2br(htmlspecialchars($receita)); ?></td></tr>
                <tr><th>Observações</th><td><?php echo nl2br(htmlspecialchars($observacoes)); ?></td></tr>
            </table>
        
        <?php endif; ?>
    </div>
</body>
</html>
