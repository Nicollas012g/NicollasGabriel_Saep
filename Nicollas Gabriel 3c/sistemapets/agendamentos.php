<?php
require 'conexao.php';
verificarAutenticacao();

$msg = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'criar') {
    $pet_id = $_POST['pet_id'];
    $data_agendamento = $_POST['data_agendamento'];
    $stmt = $pdo->prepare("INSERT INTO agendamentos (data_agendamento, pet_id) VALUES (?, ?)");
    $stmt->execute([$data_agendamento, $pet_id]);
    $msg = "Novo agendamento cadastrado!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['acao']) && $_POST['acao'] == 'atualizar') {
    $id_agendamento = $_POST['id_agendamento'];
    $nova_data = $_POST['nova_data'];
    $stmt = $pdo->prepare("UPDATE agendamentos SET data_agendamento = ? WHERE id = ?");
    $stmt->execute([$nova_data, $id_agendamento]);
    $msg = "Data do agendamento atualizada com sucesso!";
}


$sql = "SELECT a.id AS agendamento_id, a.data_agendamento, 
               p.id AS pet_id, p.nome AS pet_nome, p.especie AS pet_especie,
               t.id AS tutor_id, t.nome AS tutor_nome, t.cpf AS tutor_cpf
        FROM agendamentos a
        JOIN pets p ON a.pet_id = p.id
        JOIN tutores t ON p.tutor_id = t.id
        ORDER BY a.data_agendamento ASC";
$agendamentos = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$pets = $pdo->query("SELECT p.id, p.nome, t.nome AS tutor_nome FROM pets p JOIN tutores t ON p.tutor_id = t.id")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Agendamentos</title>
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
        <h2>Novo Agendamento</h2>
        <?php if ($msg): ?>
            <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="acao" value="criar">
            <div class="flex-row">
                <div class="form-group" style="flex: 1;">
                    <label>Pet (e Tutor):</label>
                    <select name="pet_id" required>
                        <option value="">Selecione o Pet...</option>
                        <?php foreach ($pets as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nome']) ?> (Tutor: <?= htmlspecialchars($p['tutor_nome']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Data e Hora da Consulta:</label>
                    <input type="datetime-local" name="data_agendamento" required>
                </div>
                <div class="form-group">
                    <button type="submit">Agendar</button>
                </div>
            </div>
        </form>

        <h2 style="margin-top: 30px;">Consultas Agendadas (Ordenadas por Data)</h2>
        <table>
            <thead>
                <tr>
                    <th>Data/Hora</th>
                    <th>Dados do Pet</th>
                    <th>Dados do Tutor</th>
                    <th>Reagendar Data</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($agendamentos as $ag): 
                    $dataFormatadaInput = date('Y-m-d\TH:i', strtotime($ag['data_agendamento']));
                ?>
                <tr>
                    <td><strong><?= date('d/m/Y H:i', strtotime($ag['data_agendamento'])) ?></strong></td>
                    <td><?= htmlspecialchars($ag['pet_nome']) ?> (<?= htmlspecialchars($ag['pet_especie']) ?>)</td>
                    <td><?= htmlspecialchars($ag['tutor_nome']) ?><br><small>CPF: <?= htmlspecialchars(descriptografarCPF($ag['tutor_cpf'])) ?></small></td>
                    <td>
                        <form method="POST" style="display: flex; gap: 5px;">
                            <input type="hidden" name="acao" value="atualizar">
                            <input type="hidden" name="id_agendamento" value="<?= $ag['agendamento_id'] ?>">
                            <input type="datetime-local" name="nova_data" value="<?= $dataFormatadaInput ?>" required style="padding: 5px; font-size: 0.9rem;">
                            <button type="submit" class="btn-sec" style="padding: 5px 10px; font-size: 0.9rem;">Salvar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>