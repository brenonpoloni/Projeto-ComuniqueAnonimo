<?php
require __DIR__ . '/config.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$stmtUsuario = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
$stmtUsuario->execute([':id' => $_SESSION['usuario_id']]);
$usuario = $stmtUsuario->fetch();

if (!$usuario || $usuario['tipo_conta'] !== 'tecnico') {
    header('Location: conta.php');
    exit;
}

$mensagemEnvio = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id_denuncia'])) {
    $idDenuncia = (int) $_POST['id_denuncia'];
    $updates = [];
    $parametros = [':id' => $idDenuncia];

    if (!empty($_POST['novo_status'])) {
        $novoStatus = in_array($_POST['novo_status'], ['pendente', 'em_andamento', 'concluida'], true) ? $_POST['novo_status'] : 'pendente';
        $updates[] = 'status = :status';
        $parametros[':status'] = $novoStatus;
    }

    if (!empty($_POST['novo_nivel'])) {
        $novoNivel = in_array($_POST['novo_nivel'], ['grave', 'intermediario', 'estavel'], true) ? $_POST['novo_nivel'] : 'intermediario';
        $updates[] = 'nivel_gravidade = :nivel_gravidade';
        $parametros[':nivel_gravidade'] = $novoNivel;
    }

    if (!empty($_POST['encaminhar_denuncia'])) {
        $responsavelNome = trim((string) ($_POST['responsavel_nome'] ?? ''));
        $responsavelEmail = trim((string) ($_POST['responsavel_email'] ?? ''));
        $responsavelSetor = trim((string) ($_POST['responsavel_setor'] ?? ''));
        $mensagemEncaminhamento = trim((string) ($_POST['mensagem_encaminhamento'] ?? ''));

        if ($responsavelEmail !== '') {
            if ($mensagemEncaminhamento !== '' && mb_strlen($mensagemEncaminhamento) < 10) {
                $mensagemEnvio = 'A mensagem para o responsável deve conter pelo menos 10 caracteres.';
            } else {
                $stmtDenunciaAtual = $pdo->prepare('SELECT * FROM denuncias WHERE id = :id LIMIT 1');
                $stmtDenunciaAtual->execute([':id' => $idDenuncia]);
                $denunciaAtual = $stmtDenunciaAtual->fetch();

                if ($denunciaAtual) {
                    $nivelExibicao = ($denunciaAtual['nivel_gravidade'] ?? 'intermediario');
                    $nivelTextoLocal = $nivelExibicao === 'grave' ? 'Grave' : ($nivelExibicao === 'estavel' ? 'Estável' : 'Intermediário');
                    $mensagemTecnico = $mensagemEncaminhamento !== '' ? $mensagemEncaminhamento : 'Nenhuma observação adicional foi registrada.';
                    $assuntoEmail = 'Denúncia encaminhada - ' . $denunciaAtual['protocolo'];
                    $textoEmail = "Olá,\n\nA denúncia foi encaminhada para sua análise e resolução.\n\nDetalhes da denúncia:\n- Protocolo: {$denunciaAtual['protocolo']}\n- Tipo de ocorrência: {$denunciaAtual['tipo_ocorrencia']}\n- Empresa / setor: {$denunciaAtual['empresa_setor']}\n- Local: {$denunciaAtual['endereco_local']}\n- Nível de gravidade: {$nivelTextoLocal}\n- Status atual: " . str_replace('_', ' ', $denunciaAtual['status']) . "\n\nDescrição dos fatos:\n{$denunciaAtual['descricao']}\n\nMensagem do técnico:\n{$mensagemTecnico}";

                        $updates[] = 'responsavel_nome = :responsavel_nome';
                    $updates[] = 'responsavel_email = :responsavel_email';
                    $updates[] = 'responsavel_setor = :responsavel_setor';
                    $updates[] = 'mensagem_encaminhamento = :mensagem_encaminhamento';
                    $updates[] = 'encaminhado_em = NOW()';
                    $updates[] = 'status = :status_encaminhamento';

                    $parametros[':responsavel_nome'] = $responsavelNome ?: 'Responsável não informado';
                    $parametros[':responsavel_email'] = $responsavelEmail;
                    $parametros[':responsavel_setor'] = $responsavelSetor ?: 'Não informado';
                    $parametros[':mensagem_encaminhamento'] = $mensagemEncaminhamento ?: 'Denúncia encaminhada para análise.';
                    $parametros[':status_encaminhamento'] = 'em_andamento';

                    $resultadoEnvio = enviarEmailLocal($responsavelEmail, $assuntoEmail, $textoEmail);
                    if ($resultadoEnvio) {
                        $mensagemEnvio = 'E-mail enviado com sucesso para o responsável.';
                    } else {
                        $mensagemEnvio = 'A denúncia foi registrada, mas o envio do e-mail falhou no ambiente local. Verifique o servidor de e-mail do sistema.';
                    }
                }
            }
        }
    }

    if (!empty($updates)) {
        $sql = 'UPDATE denuncias SET ' . implode(', ', $updates) . ' WHERE id = :id';
        $stmtAtualiza = $pdo->prepare($sql);
        $stmtAtualiza->execute($parametros);
    }
}

$denuncias = $pdo->query('SELECT * FROM denuncias ORDER BY criado_em DESC')->fetchAll();
$pendentes = (int) $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status = 'pendente'")->fetchColumn();
$emAndamento = (int) $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status = 'em_andamento'")->fetchColumn();
$concluidas = (int) $pdo->query("SELECT COUNT(*) FROM denuncias WHERE status = 'concluida'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Técnica | Comunique Anônimo</title>
    <link rel="stylesheet" href="css/areaTST.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <main class="area-tecnica">
        <section class="card-area-tecnica">
            <div class="cabecalho-area">
                <div>
                    <h1>Área Técnica</h1>
                    <p>Gestão de denúncias e acompanhamento de status.</p>
                </div>
                <span class="badge-area">🔒 Acesso restrito</span>
            </div>

            <div class="resumo-metricas">
                <div class="metrica">
                    <span class="label">Pendentes</span>
                    <span class="valor"><?= $pendentes ?></span>
                </div>
                <div class="metrica">
                    <span class="label">Em andamento</span>
                    <span class="valor"><?= $emAndamento ?></span>
                </div>
                <div class="metrica">
                    <span class="label">Concluídas</span>
                    <span class="valor"><?= $concluidas ?></span>
                </div>
                <div class="metrica">
                    <span class="label">Total</span>
                    <span class="valor"><?= count($denuncias) ?></span>
                </div>
            </div>

            <?php if ($mensagemEnvio): ?>
                <div class="mensagem-envio" role="status">
                    <?= h($mensagemEnvio) ?>
                </div>
            <?php endif; ?>

            <div class="lista-denuncias">
                <?php if (empty($denuncias)): ?>
                    <div class="vazio">
                        Nenhuma denúncia registrada até o momento.
                    </div>
                <?php else: ?>
                    <?php foreach ($denuncias as $denuncia): ?>
                        <?php $nivelAtual = $denuncia['nivel_gravidade'] ?? 'intermediario'; ?>
                        <article class="item-denuncia">
                            <div class="topo-denuncia">
                                <h3><?= h($denuncia['protocolo']) ?></h3>
                                <div class="topo-badges">
                                    <span class="status <?= h($denuncia['status']) ?>"><?= h(str_replace('_', ' ', $denuncia['status'])) ?></span>
                                    <span class="nivel <?= h($nivelAtual) ?>"><?= h($nivelAtual === 'grave' ? 'grave' : ($nivelAtual === 'estavel' ? 'estável' : 'intermediário')) ?></span>
                                </div>
                            </div>

                            <div class="detalhes-denuncia">
                                <strong>Tipo:</strong> <?= h($denuncia['tipo_ocorrencia']) ?><br>
                                <strong>Empresa / Setor:</strong> <?= h($denuncia['empresa_setor']) ?><br>
                                <strong>Local:</strong> <?= h($denuncia['endereco_local']) ?><br>
                                <strong>Descrição:</strong> <?= nl2br(h($denuncia['descricao'])) ?><br>
                                <strong>Data:</strong> <?= h(date('d/m/Y H:i', strtotime($denuncia['criado_em']))) ?>
                            </div>

                            <form method="post" class="form-encaminhamento">
                                <input type="hidden" name="id_denuncia" value="<?= (int) $denuncia['id'] ?>">
                                <input type="hidden" name="encaminhar_denuncia" value="1">

                                <div class="campos-encaminhamento">
                                    <input type="text" name="responsavel_nome" value="<?= h($denuncia['responsavel_nome'] ?? '') ?>" placeholder="Nome do responsável" minlength="10">
                                    <input type="email" name="responsavel_email" value="<?= h($denuncia['responsavel_email'] ?? '') ?>" placeholder="E-mail do responsável" required>
                                    <input type="text" name="responsavel_setor" value="<?= h($denuncia['responsavel_setor'] ?? '') ?>" placeholder="Setor / área responsável" minlength="10">
                                    <textarea name="mensagem_encaminhamento" placeholder="Mensagem para o responsável" minlength="10"><?= h($denuncia['mensagem_encaminhamento'] ?? '') ?></textarea>
                                </div>

                                <div class="acoes-encaminhamento">
                                    <button type="submit">Enviar E-mail</button>
                                    <?php
                                    $emailResponsavel = trim((string) ($denuncia['responsavel_email'] ?? ''));
                                    if ($emailResponsavel !== '') {
                                        $nivelExibicao = ($denuncia['nivel_gravidade'] ?? 'intermediario');
                                        $nivelTextoLocal = $nivelExibicao === 'grave' ? 'Grave' : ($nivelExibicao === 'estavel' ? 'Estável' : 'Intermediário');
                                        $mensagemTecnico = trim((string) ($denuncia['mensagem_encaminhamento'] ?? '')) ?: 'Nenhuma observação adicional foi registrada.';
                                        $textoAssunto = 'Denúncia encaminhada - ' . $denuncia['protocolo'];
                                        $textoMensagem = "Olá,\n\nA denúncia foi encaminhada para sua análise e resolução.\n\nDetalhes da denúncia:\n- Protocolo: {$denuncia['protocolo']}\n- Tipo de ocorrência: {$denuncia['tipo_ocorrencia']}\n- Empresa / setor: {$denuncia['empresa_setor']}\n- Local: {$denuncia['endereco_local']}\n- Nível de gravidade: {$nivelTextoLocal}\n- Status atual: " . str_replace('_', ' ', $denuncia['status']) . "\n\nDescrição dos fatos:\n{$denuncia['descricao']}\n\nMensagem do técnico:\n{$mensagemTecnico}";
                                    ?>
                                        <div class="email-local-preview">
                                            <details class="detalhes-email-local">
                                                <summary>
                                                    <span class="summary-label">
                                                        <span class="summary-icon">✉️</span>
                                                        <span class="titulo-email-local">Visualizar e-mail do responsável</span>
                                                    </span>
                                                    <span class="badge-email-local"><?= !empty($denuncia['encaminhado_em']) ? 'Simulação local' : 'Mensagem pronta' ?></span>
                                                </summary>
                                                <div class="conteudo-email-local">
                                                    <div class="linha-email-local"><strong>Para:</strong> <?= h($emailResponsavel) ?></div>
                                                    <div class="linha-email-local"><strong>Assunto:</strong> <?= h($textoAssunto) ?></div>
                                                    <div class="linha-email-local"><strong>Mensagem:</strong></div>
                                                    <pre class="texto-email-local"><?= h($textoMensagem) ?></pre>
                                                </div>
                                            </details>
                                        </div>
                                    <?php } ?>
                                </div>
                            </form>

                            <form method="post" class="form-status">
                                <input type="hidden" name="id_denuncia" value="<?= (int) $denuncia['id'] ?>">
                                <select name="novo_status" aria-label="Atualizar status da denúncia">
                                    <option value="pendente" <?= $denuncia['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="em_andamento" <?= $denuncia['status'] === 'em_andamento' ? 'selected' : '' ?>>Em andamento</option>
                                    <option value="concluida" <?= $denuncia['status'] === 'concluida' ? 'selected' : '' ?>>Concluída</option>
                                </select>
                                <select name="novo_nivel" aria-label="Atualizar nível de gravidade da denúncia">
                                    <option value="grave" <?= $nivelAtual === 'grave' ? 'selected' : '' ?>>Grave</option>
                                    <option value="intermediario" <?= $nivelAtual === 'intermediario' ? 'selected' : '' ?>>Intermediário</option>
                                    <option value="estavel" <?= $nivelAtual === 'estavel' ? 'selected' : '' ?>>Estável</option>
                                </select>
                                <button type="submit">Salvar</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

</body>

</html>
