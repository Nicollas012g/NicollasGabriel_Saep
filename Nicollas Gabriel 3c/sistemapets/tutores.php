<?php
require 'conexao.php';
verificarAutenticacao();

$msg = '';
$tutor_editar = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'cadastrar') {
    $nome = $_POST['nome'];
    $cpfCripto = criptografarCPF($_POST['cpf']);
    
    $stmt = $pdo->prepare("INSERT INTO tutores (nome, cpf) VALUES (?, ?)");
    $stmt->execute([$nome, $cpfCripto]);
    $msg = "Tutor cadastrado com sucesso (CPF criptografado no banco)!";
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'editar') {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cpfCripto = criptografarCPF($_POST['cpf']);
    
    $stmt = $pdo->prepare("UPDATE tutores SET nome = ?, cpf = ? WHERE id = ?");
    $stmt->execute([$nome, $cpfCripto, $id]);
    $msg = "Dados do tutor atualizados com sucesso!";
}
 
if (isset($_GET['editar'])) {
    $stmt = $pdo->prepare("SELECT * FROM tutores WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $tutor_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}


$busca = $_GET['busca'] ?? '';
if ($busca !== '') {
    $stmt = $pdo->prepare("SELECT * FROM tutores WHERE nome LIKE ?");
    $stmt->execute(["%$busca%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM tutores");
}
$tutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Tutores</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>🐾 Sistema Clínica Veterinária</h1>
        <nav>
            <span>Olá, <strong><?= htmlspecialchars($_SESSION['usuario_nome']) ?></strong></span>
            <a href="principal.php">Início</a>
            <a href="tutores.php">Tutores</a>
            <a href="pets.php">Pets</a>
            <a href="agendamentos.php">Agendamentos</a>
            <a href="logout.php" class="btn-sair">Sair</a>
        </nav>
    </header>

    <div class="container">
        <h2><?= $tutor_editar ? 'Editar Tutor' : 'Cadastrar Novo Tutor' ?></h2>
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="tutores.php">
            <input type="hidden" name="acao" value="<?= $tutor_editar ? 'editar' : 'cadastrar' ?>">
            <?php if ($tutor_editar): ?>
                <input type="hidden" name="id" value="<?= $tutor_editar['id'] ?>">
            <?php endif; ?>

            <div class="flex-row">
                <div class="form-group" style="flex: 2;">
                    <label>Nome Completo do Tutor:</label>
                    <input type="text" name="nome" value="<?= $tutor_editar ? htmlspecialchars($tutor_editar['nome']) : '' ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>CPF (Será criptografado):</label>
                    <input type="text" name="cpf" placeholder="000.000.000-00" value="<?= $tutor_editar ? htmlspecialchars(descriptografarCPF($tutor_editar['cpf'])) : '' ?>" required>
                </div>
                <div class="form-group">
                    <button type="submit"><?= $tutor_editar ? 'Salvar Alterações' : 'Cadastrar' ?></button>
                    <?php if ($tutor_editar): ?>
                        <a href="tutores.php"><button type="button" class="btn-sec">Cancelar</button></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>

        <h2 style="margin-top: 30px;">Lista de Tutores</h2>
        <form method="GET" action="tutores.php" class="flex-row">
            <div class="form-group" style="flex: 1; margin-bottom: 0;">
                <input type="text" name="busca" placeholder="Buscar tutor pelo nome..." value="<?= htmlspecialchars($busca) ?>">
            </div>
            <button type="submit">Buscar</button>
            <?php if ($busca): ?>
                <a href="tutores.php"><button type="button" class="btn-sec">Limpar</button></a>
            <?php endif; ?>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF (Descriptografado na Tela)</th>
                    <th>Hash no Banco (LGPD)</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tutores as $t): ?>
                <tr>
                    <td><?= $t['id'] ?></td>
                    <td><?= htmlspecialchars($t['nome']) ?></td>
                    <td><?= htmlspecialchars(descriptografarCPF($t['cpf'])) ?></td>
                    <td style="font-size: 0.8rem; color: #666; max-width: 150px; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($t['cpf']) ?></td>
                    <td><a href="tutores.php?editar=<?= $t['id'] ?>"><button type="button" class="btn-sec">Editar</button></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>