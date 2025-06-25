<?php
function usuarioExiste($pdo, $usuario) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE usuario = ?");
    $stmt->execute([$usuario]);
    return $stmt->fetchColumn() > 0;
}

function cadastrarUsuario($pdo, $usuario, $senha, $nivel = 'usuario') {
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuario (usuario, senha_hash, nivel) VALUES (?, ?, ?)");
    return $stmt->execute([$usuario, $senhaHash, $nivel]);
}
