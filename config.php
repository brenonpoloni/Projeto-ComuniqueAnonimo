<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

define('DB_SERVIDOR', getenv('DB_SERVIDOR') ?: 'localhost');
define('DB_USUARIO', getenv('DB_USUARIO') ?: 'root');
define('DB_SENHA', getenv('DB_SENHA') ?: '');
define('DB_NOME', getenv('DB_NOME') ?: 'comunique_anonimo');
define('MAIL_FROM', getenv('MAIL_FROM') ?: 'seu_email@gmail.com');
define('MAIL_FROM_NAME', getenv('MAIL_FROM_NAME') ?: 'Comunique Anônimo');
define('MAIL_SMTP_HOST', getenv('MAIL_SMTP_HOST') ?: 'smtp.gmail.com');
define('MAIL_SMTP_PORT', getenv('MAIL_SMTP_PORT') ?: '587');
define('MAIL_SMTP_SECURE', getenv('MAIL_SMTP_SECURE') ?: 'tls');
define('MAIL_SMTP_USERNAME', getenv('MAIL_SMTP_USERNAME') ?: 'seu_email@gmail.com');
define('MAIL_SMTP_PASSWORD', getenv('MAIL_SMTP_PASSWORD') ?: '');

function h($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function enviarEmailLocal($para, $assunto, $mensagem) {
    $smtpUsuario = trim((string) MAIL_SMTP_USERNAME);
    $smtpSenha = (string) MAIL_SMTP_PASSWORD;
    $smtpHost = trim((string) MAIL_SMTP_HOST);
    $smtpPort = (int) MAIL_SMTP_PORT;
    $smtpSecure = strtolower(trim((string) MAIL_SMTP_SECURE));

    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $smtpHost !== '' ? $smtpHost : 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUsuario !== '' ? $smtpUsuario : MAIL_FROM;
            $mail->Password = $smtpSenha;
            $mail->SMTPSecure = $smtpSecure === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $smtpPort > 0 ? $smtpPort : 587;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($para);
            $mail->isHTML(true);
            $mail->Subject = $assunto;
            $mail->Body = nl2br($mensagem, false);
            $mail->AltBody = $mensagem;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('Erro ao enviar e-mail com PHPMailer: ' . $e->getMessage());
        }
    }

    $pastaEmails = __DIR__ . '/uploads/emails';
    if (!is_dir($pastaEmails) && !mkdir($pastaEmails, 0775, true) && !is_dir($pastaEmails)) {
        error_log('Não foi possível criar a pasta de e-mails locais: ' . $pastaEmails);
        return false;
    }

    $nomeArquivo = 'email_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.eml';
    $caminhoArquivo = $pastaEmails . '/' . $nomeArquivo;
    $conteudo = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n" .
        "Reply-To: " . MAIL_FROM . "\r\n" .
        "To: " . $para . "\r\n" .
        "Subject: " . $assunto . "\r\n" .
        "MIME-Version: 1.0\r\n" .
        "Content-Type: text/plain; charset=UTF-8\r\n\r\n" .
        $mensagem . "\r\n";

    $gravado = @file_put_contents($caminhoArquivo, $conteudo);
    if ($gravado === false) {
        error_log('Não foi possível salvar o e-mail em arquivo local: ' . $caminhoArquivo);
        return false;
    }

    return true;
}

try {
    $pdo = new PDO(
        'mysql:host=' . DB_SERVIDOR . ';dbname=' . DB_NOME . ';charset=utf8mb4',
        DB_USUARIO,
        DB_SENHA,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $excecao) {
    die('Não foi possível conectar ao MySQL. ' . $excecao->getMessage());
}

ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function garantirEstruturaDenuncias() {
    global $pdo;

    try {
        $colunasNecessarias = [
            'nivel_gravidade' => "ALTER TABLE denuncias ADD COLUMN nivel_gravidade ENUM('grave', 'intermediario', 'estavel') NOT NULL DEFAULT 'intermediario' AFTER status",
            'responsavel_nome' => "ALTER TABLE denuncias ADD COLUMN responsavel_nome VARCHAR(150) NULL AFTER nivel_gravidade",
            'responsavel_email' => "ALTER TABLE denuncias ADD COLUMN responsavel_email VARCHAR(150) NULL AFTER responsavel_nome",
            'responsavel_setor' => "ALTER TABLE denuncias ADD COLUMN responsavel_setor VARCHAR(120) NULL AFTER responsavel_email",
            'mensagem_encaminhamento' => "ALTER TABLE denuncias ADD COLUMN mensagem_encaminhamento TEXT NULL AFTER responsavel_setor",
            'encaminhado_em' => "ALTER TABLE denuncias ADD COLUMN encaminhado_em DATETIME NULL AFTER mensagem_encaminhamento",
        ];

        foreach ($colunasNecessarias as $coluna => $sql) {
            $existente = $pdo->query("SHOW COLUMNS FROM denuncias LIKE '" . $coluna . "'")->fetch();
            if (!$existente) {
                $pdo->exec($sql);
            }
        }
    } catch (PDOException $excecao) {
        error_log('Erro ao verificar estrutura da tabela denuncias: ' . $excecao->getMessage());
    }
}

garantirEstruturaDenuncias();

function exigirLogin() {
    if (empty($_SESSION['usuario_id'])) {
        header('Location: login.php');
        exit;
    }
}