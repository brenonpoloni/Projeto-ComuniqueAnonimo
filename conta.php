<?php
require __DIR__ . '/config.php';

$usuario = null;
$ultimoAcesso = '—';

if (!empty($_SESSION['usuario_id'])) {
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $_SESSION['usuario_id']]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        $pdo->prepare('UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = :id')->execute([':id' => $usuario['id']]);
        $ultimoAcesso = !empty($usuario['ultimo_acesso']) ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : '—';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo</title>
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
                <a href="logout.php" class="btn-entrar-topo">🚪 Sair</a>

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
                <span class="badge-status">• Conectado</span>
            </div>

            <?php if ($usuario): ?>
                <div class="card-perfil-usuario">
                    <div class="avatar-circle">👤</div>
                    <div class="grid-dados-usuario">
                        <div class="item-dado">
                            <span class="label-dado">Nome</span>
                            <span class="valor-dado"><?= h($usuario['nome']) ?></span>
                        </div>
                        <div class="item-dado">
                            <span class="label-dado">E-mail</span>
                            <span class="valor-dado"><?= h($usuario['email']) ?></span>
                        </div>
                        <div class="item-dado">
                            <span class="label-dado">Tipo de conta</span>
                            <span class="valor-dado"><?= h(ucfirst($usuario['tipo_conta'])) ?></span>
                        </div>
                        <div class="item-dado">
                            <span class="label-dado">Último acesso</span>
                            <span class="valor-dado"><?= h($ultimoAcesso) ?></span>
                        </div>
                        <div class="item-dado full-width">
                            <a href="logout.php" class="link-acao">Sair da minha conta &rarr;</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-perfil-usuario" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 18px;">
                    <div class="avatar-circle">🔒</div>
                    <div>
                        <h3 style="margin: 0 0 8px; color: #0b1d3d;">Você ainda não está conectado.</h3>
                        <p style="margin: 0; color: #4a5872;">Para visualizar suas informações pessoais, faça login ou crie uma conta.</p>
                    </div>
                    <a href="login.php" class="btn-entrar-topo">Entrar ou criar conta</a>
                </div>
            <?php endif; ?>
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
                <a href="areaTST.php" class="btn-acessar-tecnico">🔑 Acessar como técnico &rarr;</a>
            </div>

            <div class="grid-metricas">
                <?php
                $totalDenuncias = (int) $pdo->query('SELECT COUNT(*) FROM denuncias')->fetchColumn();
                $denunciasPendentes = (int) $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status = 'pendente'")->fetchColumn();
                $denunciasAndamento = (int) $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status = 'em_andamento'")->fetchColumn();
                $denunciasConcluidas = (int) $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status = 'concluida'")->fetchColumn();
                ?>

                <div class="card-metrica">
                    <div class="info-metrica">
                        <span>Denúncias recebidas</span>
                        <div class="linha-vazia"><?= $totalDenuncias ?></div>
                    </div>
                    <span class="icone-card-metrica">📄</span>
                </div>

                <div class="card-metrica">
                    <div class="info-metrica">
                        <span>Aguardando análise</span>
                        <div class="linha-vazia"><?= $denunciasPendentes ?></div>
                    </div>
                    <span class="icone-card-metrica">⏱️</span>
                </div>

                <div class="card-metrica">
                    <div class="info-metrica">
                        <span>Em andamento</span>
                        <div class="linha-vazia"><?= $denunciasAndamento ?></div>
                    </div>
                    <span class="icone-card-metrica">🔍</span>
                </div>

                <div class="card-metrica">
                    <div class="info-metrica">
                        <span>Concluídas</span>
                        <div class="linha-vazia"><?= $denunciasConcluidas ?></div>
                    </div>
                    <span class="icone-card-metrica">📑</span>
                </div>
            </div>

            <div class="barra-tabs">
                <div class="tabs">
                    <button class="tab-btn active" type="button">Todas</button>
                    <button class="tab-btn" type="button">Pendentes</button>
                    <button class="tab-btn" type="button">Em andamento</button>
                    <button class="tab-btn" type="button">Concluídas</button>
                </div>
                <div class="dados-protegidos">🔒 Dados protegidos</div>
            </div>

            <div class="box-bloqueio-tecnico">
                <div class="icone-cadeado-circulo">🔒</div>
                <h3>Um espaço seguro para cuidar de todos.</h3>
                <p>As denúncias estão disponíveis apenas para técnicos autorizados.<br>Entre com sua conta profissional para acessar as informações.</p>
                <a href="areaTST.php" class="btn-entrar-area-tecnica">Entrar na área técnica &rarr;</a>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>

</html>