<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'clinica_veterinaria';
$porta = "3307";

try {
    $pdo = new PDO("mysql:host=$servidor;port=$porta;dbname=$banco;charset=utf8", $usuario, $senha);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}


function verificarAutenticacao() {
    if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['expira_em']) || time() > $_SESSION['expira_em']) {
        session_destroy();
        header("Location: index.php?erro=expirado");
        exit;
    }

    $_SESSION['expira_em'] = time() + 3600;
}


define('CHAVE_LGPD', str_pad('clinica_vet_lgpd_2026', 32, '*'));
define('METODO_CRIPTO', 'AES-256-CBC');

function criptografarCPF($cpf) {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(METODO_CRIPTO));
    $cripto = openssl_encrypt($cpf, METODO_CRIPTO, CHAVE_LGPD, 0, $iv);
    return base64_encode($iv . $cripto);
}

function descriptografarCPF($cpfBase64) {
    $dados = base64_decode($cpfBase64);
    $ivLen = openssl_cipher_iv_length(METODO_CRIPTO);
    if (strlen($dados) <= $ivLen) return $cpfBase64; // Caso seja dado de teste do SQL
    $iv = substr($dados, 0, $ivLen);
    $cripto = substr($dados, $ivLen);
    $dec = openssl_decrypt($cripto, METODO_CRIPTO, CHAVE_LGPD, 0, $iv);
    return $dec !== false ? $dec : $cpfBase64;
}
?>