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

// Atualiza o prontuário se enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['cpf']) && $id) {
    $peso = $_POST['peso'] ?? '';
    $altura = $_POST['altura'] ?? '';
    $vacinas_aplicadas = $_POST['vacinas_aplicadas'] ?? '';
    $data_proxima_vacina = $_POST['data_proxima_vacina'] ?? '';
    $data_proxima_consulta = $_POST['data_proxima_consulta'] ?? '';
    $receita = $_POST['receita'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';

    $stmt = $conn->prepare("UPDATE prontuario2025 SET peso=?, altura=?, vacinas_aplicadas=?, data_proxima_vacina=?, data_proxima_consulta=?, receita=?, observacoes=? WHERE id=?");
    $stmt->bind_param("ddsssssi", $peso, $altura, $vacinas_aplicadas, $data_proxima_vacina, $data_proxima_consulta, $receita, $observacoes, $id);
    $stmt->execute();
    $stmt->close();
    $msg = "<div class='msg-success' style='text-align:center;'>Prontuário atualizado com sucesso!</div>";
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
    <title>SIVAC - Prontuário</title>
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

    <?php if ($msg) echo $msg; ?>

    <div class="container" style="max-width:500px;">
        <h2 style="text-align:center; margin-top:30px;">Prontuário</h2>
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
        <?php endif; ?>
        <form method="post">
            <label for="peso">Peso (kg):</label>
            <input type="number" step="0.01" id="peso" name="peso" value="<?php echo htmlspecialchars($peso); ?>" required>

            <label for="altura">Altura (cm):</label>
            <input type="number" step="0.01" id="altura" name="altura" value="<?php echo htmlspecialchars($altura); ?>" required>

            <label for="vacinas_aplicadas">Vacinas Aplicadas:</label>
            <textarea id="vacinas_aplicadas" name="vacinas_aplicadas"><?php echo htmlspecialchars($vacinas_aplicadas); ?></textarea>

            <label for="data_proxima_vacina">Próx. Vacina:</label>
            <input type="date" id="data_proxima_vacina" name="data_proxima_vacina" value="<?php echo htmlspecialchars($data_proxima_vacina); ?>">

            <label for="data_proxima_consulta">Próx. Consulta:</label>
            <input type="date" id="data_proxima_consulta" name="data_proxima_consulta" value="<?php echo htmlspecialchars($data_proxima_consulta); ?>">

            <label for="receita">Receita:</label>
            <textarea id="receita" name="receita"><?php echo htmlspecialchars($receita); ?></textarea>

            <label for="observacoes">Observações:</label>
            <textarea id="observacoes" name="observacoes"><?php echo htmlspecialchars($observacoes); ?></textarea>

            <button type="submit" class="btn btn-primary" style="margin-top:15px;">Salvar</button>
        </form>
    </div>
</body>
</html>