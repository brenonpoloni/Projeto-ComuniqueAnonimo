<?php
session_start();
$estaLogado = isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
$usuario = $estaLogado ? $_SESSION['usuario'] : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo - Conta</title>
    <link rel="stylesheet" href="css/conta.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <section class="hero-conta">
        <div class="conteudo-hero-conta">
            <div class="lado-esquerdo-hero">
                <p class="subtitulo-hero">UM AMBIENTE MAIS SEGURO COMEÇA COM VOCÊ</p>
                <h1>SUA CONTA.<br><span class="destaque-amarelo">SUA SEGURANÇA.</span></h1>
                <p class="descricao-hero">Gerencie suas informações e acesse o espaço dedicado à segurança e à saúde no trabalho.</p>
            </div>

            <div class="lado-direito-hero">
                <!-- BOTÃO DE ENTRAR/SAIR DO HERO -->
                <?php if ($estaLogado): ?>
                    <a href="logout.php" class="btn-entrar-topo" style="text-decoration:none;">🚪 Sair</a>
                <?php else: ?>
                    <a href="login.php" class="btn-entrar-topo" style="text-decoration:none;">➡️ Entrar</a>
                <?php endif; ?>

                <div class="box-protecao">
                    <div class="icone-escudo">🛡️</div>
                    <div class="texto-escudo">
                        <h3>Sua identidade, protegida.</h3>
                        <p>Os dados da sua conta não são exibidos nas denúncias anônimas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="container-principal-conta">
        <section class="secao-minha-conta">
            <div class="info-cabecalho-conta">
                <div class="titulo-com-icone">
                    <span class="icone-secao">👤</span>
                    <h2>Minha conta</h2>
                </div>
                <p>Suas informações pessoais e de acesso, em um só lugar.</p>
                <span class="badge-status" style="color: <?= $estaLogado ? '#1f7a3f' : '#4b5563' ?>;">
                    <?= $estaLogado ? '• Conectado' : '• Não conectado' ?>
                </span>
            </div>

            <div class="card-perfil-usuario">
                <div class="avatar-circle">👤</div>
                <div class="grid-dados-usuario">
                    <div class="item-dado">
                        <span class="label-dado">Nome</span>
                        <span class="valor-dado"><?= $estaLogado ? htmlspecialchars($usuario['nome']) : 'Não informado' ?></span>
                    </div>
                    <div class="item-dado">
                        <span class="label-dado">E-mail</span>
                        <span class="valor-dado"><?= $estaLogado ? htmlspecialchars($usuario['email']) : 'Não informado' ?></span>
                    </div>
                    <div class="item-dado">
                        <span class="label-dado">Tipo de conta</span>
                        <span class="valor-dado"><?= $estaLogado ? htmlspecialchars($usuario['cargo']) : 'Não identificado' ?></span>
                    </div>
                    <div class="item-dado">
                        <span class="label-dado">Último acesso</span>
                        <span class="valor-dado"><?= $estaLogado ? htmlspecialchars($usuario['ultimo_acesso']) : '—' ?></span>
                    </div>
                    
                    <!-- LINK DE AÇÃO DO PERFIL -->
                    <div class="item-dado full-width">
                        <?php if ($estaLogado): ?>
                            <a href="logout.php" class="link-acao">Sair da minha conta &rarr;</a>
                        <?php else: ?>
                            <a href="login.php" class="link-acao">Entrar na minha conta &rarr;</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <hr class="divisor-secao">

        <section class="secao-tecnico">
            <div class="cabecalho-tecnico">
                <div>
                    <div class="titulo-com-icone">
                        <span class="icone-secao">🪖</span>
                        <h2>Área do técnico</h2>
                        <span class="badge-restrito">🔒 ACESSO RESTRITO</span>
                    </div>
                    <p>Gestão de denúncias de segurança e saúde no trabalho.</p>
                </div>
                <!-- BOTÃO TÉCNICO NO CABEÇALHO -->
                <a href="login.php" class="btn-acessar-tecnico" style="text-decoration:none;">🔑 Acessar como técnico &rarr;</a>
            </div>

            <div class="box-bloqueio-tecnico">
                <div class="icone-cadeado-circulo">🔒</div>
                <h3>Um espaço seguro para cuidar de todos.</h3>
                <p>As denúncias estão disponíveis apenas para técnicos autorizados.<br>Entre com sua conta profissional para acessar as informações.</p>
                <!-- BOTÃO TÉCNICO NO CARD -->
                <a href="login.php" class="btn-entrar-area-tecnica" style="text-decoration:none;">Entrar na área técnica &rarr;</a>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>