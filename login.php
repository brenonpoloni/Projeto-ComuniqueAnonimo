<?php
require __DIR__ . '/config.php';

$erro = '';
$modo = $_GET['modo'] ?? 'login';
$redirectDestino = $_GET['redirect'] ?? 'conta.php';

if (!preg_match('/^(?:[A-Za-z0-9_\/-]+\.php|[A-Za-z0-9_\/-]+)$/', $redirectDestino)) {
    $redirectDestino = 'conta.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? 'login';
    $modo = $acao === 'cadastro' ? 'cadastro' : 'login';
    $redirectDestino = $_POST['redirect'] ?? $redirectDestino;

    if (!preg_match('/^(?:[A-Za-z0-9_\/-]+\.php|[A-Za-z0-9_\/-]+)$/', $redirectDestino)) {
        $redirectDestino = 'conta.php';
    }

    if ($acao === 'login') {
        $email = strtolower(trim($_POST['email'] ?? ''));
        $senha = $_POST['senha'] ?? '';

        if (!$email || !$senha) {
            $erro = 'Informe seu e-mail e sua senha para continuar.';
        } else {
            $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch();

            if (!$usuario) {
                $erro = 'E-mail ou senha incorretos.';
            } else {
                $senhaHashBanco = (string) $usuario['senha'];
                $senhaValida = password_verify($senha, $senhaHashBanco);
                $senhaEmTextoPuro = false;

                if (!$senhaValida && hash_equals($senhaHashBanco, $senha)) {
                    $senhaValida = true;
                    $senhaEmTextoPuro = true;
                }

                if (!$senhaValida) {
                    $erro = 'E-mail ou senha incorretos.';
                } else {
                    $infoHash = password_get_info($senhaHashBanco);
                    if ($senhaEmTextoPuro || ($infoHash['algo'] !== null && password_needs_rehash($senhaHashBanco, PASSWORD_DEFAULT))) {
                        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                        $regravarSenha = $pdo->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id');
                        $regravarSenha->execute([
                            ':senha' => $senhaHash,
                            ':id' => $usuario['id'],
                        ]);
                    }

                    $_SESSION['usuario_id'] = (int) $usuario['id'];
                    $_SESSION['usuario_nome'] = $usuario['nome'];
                    $_SESSION['usuario_email'] = $usuario['email'];
                    $_SESSION['usuario_tipo'] = $usuario['tipo_conta'];

                    $atualiza = $pdo->prepare('UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = :id');
                    $atualiza->execute([':id' => $usuario['id']]);

                    $destinoAposLogin = $redirectDestino ?: ($usuario['tipo_conta'] === 'tecnico' ? 'areaTST.php' : 'conta.php');
                    header('Location: ' . $destinoAposLogin);
                    exit;
                }
            }
        }
    } else {
        $nome = trim($_POST['nome'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $cpf = trim($_POST['cpf'] ?? '');
        $empresa = trim($_POST['empresa'] ?? '');
        $cargo = trim($_POST['cargo'] ?? '');
        $setor = trim($_POST['setor'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipoConta = in_array($_POST['tipo_conta'] ?? 'usuario', ['usuario', 'tecnico'], true) ? $_POST['tipo_conta'] : 'usuario';

        if (!$nome || !$email || !$cpf || !$empresa || !$cargo || !$setor || !$senha) {
            $erro = 'Preencha todos os campos para continuar.';
        } elseif (mb_strlen(trim($nome)) < 10 || mb_strlen(trim($empresa)) < 10 || mb_strlen(trim($cargo)) < 10 || mb_strlen(trim($setor)) < 10) {
            $erro = 'Os campos de nome, empresa, cargo e setor devem conter pelo menos 10 caracteres.';
        } else {
            $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);

            if ($stmt->fetch()) {
                $erro = 'Este e-mail já está cadastrado. Faça login com sua senha.';
            } else {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $inserir = $pdo->prepare(
                    'INSERT INTO usuarios (nome, email, cpf, empresa, cargo, setor, senha, tipo_conta, ultimo_acesso) VALUES (:nome, :email, :cpf, :empresa, :cargo, :setor, :senha, :tipo_conta, NOW())'
                );
                $inserir->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':cpf' => $cpf,
                    ':empresa' => $empresa,
                    ':cargo' => $cargo,
                    ':setor' => $setor,
                    ':senha' => $senhaHash,
                    ':tipo_conta' => $tipoConta,
                ]);

                $_SESSION['usuario_id'] = (int) $pdo->lastInsertId();
                $_SESSION['usuario_nome'] = $nome;
                $_SESSION['usuario_email'] = $email;
                $_SESSION['usuario_tipo'] = $tipoConta;

                $destinoAposCadastro = $redirectDestino ?: ($tipoConta === 'tecnico' ? 'areaTST.php' : 'conta.php');
                header('Location: ' . $destinoAposCadastro);
                exit;
            }
        }
    }
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
            <h1 class="titulo-login"><?= $modo === 'cadastro' ? 'Crie sua conta' : 'Acesso ao Sistema' ?></h1>
            <p class="subtitulo-login"><?= $modo === 'cadastro' ? 'Preencha seus dados para criar sua conta no portal.' : 'Digite seu e-mail e senha para entrar no portal.' ?></p>

            <?php if ($erro): ?>
                <p style="color: #b71c1c; background: #fdecea; padding: 10px; border-radius: 8px; margin-bottom: 15px;">
                    <?= h($erro) ?>
                </p>
            <?php endif; ?>

            <?php if ($modo === 'cadastro'): ?>
                <form class="form-login" method="post" action="login.php">
                    <input type="hidden" name="acao" value="cadastro">
                    <input type="hidden" name="redirect" value="<?= h($redirectDestino) ?>">

                    <div class="grupo-campo">
                        <label for="nome">Nome Completo:</label>
                        <input type="text" id="nome" name="nome" class="campo-input" placeholder="Digite seu nome" required minlength="10">
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
                            <input type="text" id="empresa" name="empresa" class="campo-input" placeholder="Nome da empresa" required minlength="10">
                        </div>
                    </div>

                    <div class="linha-dupla">
                        <div class="grupo-campo">
                            <label for="cargo">Função / Cargo:</label>
                            <input type="text" id="cargo" name="cargo" class="campo-input" placeholder="Ex: Técnico de Segurança" required minlength="10">
                        </div>
                        <div class="grupo-campo">
                            <label for="setor">Setor / Departamento:</label>
                            <input type="text" id="setor" name="setor" class="campo-input" placeholder="Ex: Operacional" required minlength="10">
                        </div>
                    </div>

                    <div class="grupo-campo">
                        <label for="tipo_conta">Tipo de acesso:</label>
                        <select id="tipo_conta" name="tipo_conta" class="campo-input" required>
                            <option value="usuario" selected>Usuário</option>
                            <option value="tecnico">Técnico</option>
                        </select>
                    </div>

                    <div class="grupo-campo">
                        <label for="senha">Palavra-passe:</label>
                        <input type="password" id="senha" name="senha" class="campo-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-entrar-sistema">CRIAR CONTA</button>
                </form>

                <p class="subtitulo-login" style="margin-top: 15px;">
                    Já possui uma conta?
                    <a href="login.php?redirect=<?= urlencode($redirectDestino) ?>" style="color: #f4c542; text-decoration: none; font-weight: 700;">Entrar</a>
                </p>
            <?php else: ?>
                <form class="form-login" method="post" action="login.php">
                    <input type="hidden" name="acao" value="login">
                    <input type="hidden" name="redirect" value="<?= h($redirectDestino) ?>">

                    <div class="grupo-campo">
                        <label for="email">E-mail:</label>
                        <input type="email" id="email" name="email" class="campo-input" placeholder="seu.email@gmail.com" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="senha">Senha:</label>
                        <input type="password" id="senha" name="senha" class="campo-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-entrar-sistema">ENTRAR</button>
                </form>

                <p class="subtitulo-login" style="margin-top: 15px;">
                    Ainda não tem conta?
                    <a href="login.php?modo=cadastro&redirect=<?= urlencode($redirectDestino) ?>" style="color: #f4c542; text-decoration: none; font-weight: 700;">Criar conta</a>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <script src="script.js"></script>
</body>

</html>