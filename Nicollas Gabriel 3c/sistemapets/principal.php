<?php
require 'conexao.php';
verificarAutenticacao();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel Principal - Clínica Veterinária</title>
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
        <h2>Bem-vindo ao Sistema de Gestão</h2>
        <p>Selecione um dos módulos abaixo para gerenciar os atendimentos da clínica:</p>
        
        <div class="cards">
            <div class="card">
                <h3>👤 Tutores</h3>
                <p>Cadastro, busca e edição de tutores com proteção LGPD.</p><br>
                <a href="tutores.php"><button>Acessar Tutores</button></a>
            </div>
            <div class="card">
                <h3>🐶 Pets</h3>
                <p>Cadastro de animais associados aos seus respectivos tutores.</p><br>
                <a href="pets.php"><button>Acessar Pets</button></a>
            </div>
            <div class="card">
                <h3>📅 Agendamentos</h3>
                <p>Listagem ordenada de consultas e reagendamento de datas.</p><br>
                <a href="agendamentos.php"><button>Acessar Agendamentos</button></a>
            </div>
        </div>
    </div>
</body>
</html>