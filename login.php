<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['usuario_logado'] = true;
    $_SESSION['usuario'] = [
        'nome' => $_POST['nome'] ?? 'Usuário',
        'email' => $_POST['email'] ?? '',
        'cpf' => $_POST['cpf'] ?? '',
        'empresa' => $_POST['empresa'] ?? '',
        'cargo' => $_POST['cargo'] ?? 'Técnico de Segurança',
        'setor' => $_POST['setor'] ?? 'Operacional',
        'ultimo_acesso' => date('d/m/Y H:i')
    ];

    // REDIRECIONA PARA A TELA DE CONTA APÓS LOGAR
    header('Location: conta.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo - Acesso ao Sistema</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <main class="container-login">
        <div class="card-login">
            <h1 class="titulo-login">Acesso ao Sistema</h1>
            <p class="subtitulo-login">Preencha seus dados para aceder ao portal.</p>

            <form class="form-login" action="login.php" method="POST">
                <div class="grupo-campo">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" class="campo-input" placeholder="Digite seu nome" required>
                </div>

                <div class="grupo-campo">
                    <label for="email">E-mail (Gmail / Corporativo):</label>
                    <input type="email" id="email" name="email" class="campo-input" placeholder="seu.email@gmail.com" required>
                </div>

                <div class="linha-dupla">
                    <div class="grupo-campo">
                        <label for="cpf">CPF:</label>
                        <input type="text" id="cpf" name="cpf" class="campo-input" placeholder="000.000.000-00" required>
                    </div>
                    <div class="grupo-campo">
                        <label for="empresa">Empresa:</label>
                        <input type="text" id="empresa" name="empresa" class="campo-input" placeholder="Nome da empresa" required>
                    </div>
                </div>

                <div class="linha-dupla">
                    <div class="grupo-campo">
                        <label for="cargo">Função / Cargo:</label>
                        <input type="text" id="cargo" name="cargo" class="campo-input" placeholder="Ex: Técnico de Segurança" required>
                    </div>
                    <div class="grupo-campo">
                        <label for="setor">Setor / Departamento:</label>
                        <input type="text" id="setor" name="setor" class="campo-input" placeholder="Ex: Operacional" required>
                    </div>
                </div>

                <div class="grupo-campo">
                    <label for="senha">Palavra-passe:</label>
                    <input type="password" id="senha" name="senha" class="campo-input" placeholder="••••••••" required>
                </div>

                <!-- SUBMETE E VAI PARA CONTA.PHP -->
                <button type="submit" class="btn-entrar-sistema">ENTRAR NO SISTEMA</button>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>