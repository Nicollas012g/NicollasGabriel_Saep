<?php
require 'conexao.php';

$erro = '';
if (isset($_GET['erro']) && $_GET['erro'] == 'expirado') {
    $erro = "Sua sessão expirou ou você não está logado. Faça login novamente.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && $usuario['senha'] === $senha) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['expira_em'] = time() + 3600; // Expira em 1 hora
        header("Location: principal.php");
        exit;
    } else {
        $erro = "Falha na autenticação: E-mail ou senha incorretos!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Login - Clínica Veterinária</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center;">🐾 Clínica Vet - Login</h2>
        <?php if ($erro): ?>
            <div class="alert alert-error"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>E-mail:</label>
                <input type="email" name="email" placeholder="admin@clinica.com" required>
            </div>
            <div class="form-group">
                <label>Senha:</label>
                <input type="password" name="senha" placeholder="123" required>
            </div>
            <button type="submit" style="width: 100%;">Entrar no Sistema</button>
        </form>
    </div>
</body>
</html>