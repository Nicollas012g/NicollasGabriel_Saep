<?php
require 'conexao.php';
verificarAutenticacao();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $especie = $_POST['especie'];
    $tutor_id = $_POST['tutor_id'];

    $stmt = $pdo->prepare("INSERT INTO pets (nome, especie, tutor_id) VALUES (?, ?, ?)");
    $stmt->execute([$nome, $especie, $tutor_id]);
    $msg = "Pet cadastrado e associado ao tutor com sucesso!";
}

$tutores = $pdo->query("SELECT id, nome FROM tutores ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$pets = $pdo->query("SELECT p.*, t.nome AS nome_tutor FROM pets p JOIN tutores t ON p.tutor_id = t.id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Pets</title>
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
        <h2>Cadastrar Novo Pet</h2>
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="flex-row">
                <div class="form-group" style="flex: 1;">
                    <label>Nome do Pet:</label>
                    <input type="text" name="nome" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Espécie:</label>
                    <input type="text" name="especie" placeholder="Ex: Cachorro, Gato..." required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Tutor Responsável:</label>
                    <select name="tutor_id" required>
                        <option value="">Selecione um tutor...</option>
                        <?php foreach ($tutores as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit">Cadastrar Pet</button>
                </div>
            </div>
        </form>

        <h2 style="margin-top: 30px;">Pets Cadastrados</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Pet</th>
                    <th>Espécie</th>
                    <th>Tutor Responsável</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pets as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['especie']) ?></td>
                    <td><?= htmlspecialchars($p['nome_tutor']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>