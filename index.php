<?php
    include 'conexao.php'; // Conexão com o banco

    // Buscar pacientes cadastrados na tabela pacientecadastro
    $pacientes = [];
    $sql = "SELECT nome_completo, cpf, data_nascimento FROM pacientecadastro";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Calcular idade (anos e meses)
            $idade = '';
            if (!empty($row['data_nascimento'])) {
                $data_nasc = new DateTime($row['data_nascimento']);
                $hoje = new DateTime();
                $intervalo = $hoje->diff($data_nasc);
                $anos = $intervalo->y;
                $meses = $intervalo->m;
                $idade = "{$anos} anos";
                if ($meses > 0) {
                    $idade .= " e {$meses} meses";
                }
            }
            $row['idade'] = $idade;
            $pacientes[] = $row;
        }
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cadastro de Paciente</title>
            <link rel="stylesheet" href="css/coluna.css">
        </head>
        <body>

            <header>
                <nav id="menuHorizontal">
                    <ul>
                        <li><a href="agenda.php">Agenda</a></li>    
                        <li><a href="index.php">Início</a></li>
                        <li><a href="prontuario.php">Prontuário</a></li>
                        <li><a href="paciente-cadastro.php">Cadastro de Paciente</a></li>
                        <li><a href="login.html">Sair</a></li>
                    </ul>
                </nav>
            </header>

            <main>
                <div style="background: #fff; max-width: 500px; margin: 40px auto; padding: 32px 24px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.07);">
                    <h2 style="text-align:center; margin-bottom: 24px;">Lista de Pacientes</h2>
                    <ul style="list-style: none; padding: 0;">
                        <?php if (count($pacientes) > 0): ?>
                            <?php foreach ($pacientes as $paciente): ?>
                                <li style="margin-bottom: 24px; border-bottom: 1px solid #eee; padding-bottom: 16px;">
                                    <strong>Nome:</strong> <?php echo htmlspecialchars($paciente['nome_completo']); ?><br>
                                    <strong>CPF:</strong> <?php echo htmlspecialchars($paciente['cpf']); ?><br>
                                    <strong>Idade:</strong> <?php echo ($paciente['idade'] !== '') ? $paciente['idade'] : 'Não informado'; ?><br>
                                    <span style="display:inline-block; background:#e0ffe0; color:#217a2b; padding:4px 12px; border-radius:8px; font-weight:bold; margin-top:6px;">Cadastrado</span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>Nenhum paciente cadastrado.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </main>

    </body>
</html>
