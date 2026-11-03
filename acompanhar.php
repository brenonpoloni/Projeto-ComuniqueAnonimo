<?php
require __DIR__ . '/config.php';

$protocoloBusca = trim($_GET['protocolo'] ?? '');
$mensagem = '';
$denuncia = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $protocoloBusca = trim($_POST['protocolo'] ?? '');
}

if ($protocoloBusca !== '') {
    $stmt = $pdo->prepare('SELECT * FROM denuncias WHERE protocolo = :protocolo LIMIT 1');
    $stmt->execute([':protocolo' => strtoupper($protocoloBusca)]);
    $denuncia = $stmt->fetch();

    if (!$denuncia) {
        $mensagem = 'Nenhuma denúncia foi encontrada para este protocolo. Verifique se o número foi digitado corretamente.';
    }
}

$statusTexto = [
    'pendente' => [
        'titulo' => 'Recebida e em análise',
        'texto' => 'Sua denúncia foi registrada com sucesso e está sendo avaliada pela equipe responsável. Assim que houver movimentação, o status será atualizado.'
    ],
    'em_andamento' => [
        'titulo' => 'Em andamento',
        'texto' => 'Sua denúncia está em investigação. A equipe está verificando as informações e tomando as providências necessárias para a análise da situação relatada.'
    ],
    'concluida' => [
        'titulo' => 'Concluída',
        'texto' => 'A análise da denúncia foi concluída. Os encaminhamentos e a resposta final já foram registrados no sistema.'
    ],
];

$nivelTexto = [
    'grave' => ['nome' => 'Grave', 'descricao' => 'A situação foi classificada como grave e exige atenção prioritária.'],
    'intermediario' => ['nome' => 'Intermediário', 'descricao' => 'A situação foi classificada como intermediária e segue em análise.'],
    'estavel' => ['nome' => 'Estável', 'descricao' => 'A situação foi classificada como estável, com acompanhamento contínuo.'],
];

$analise = null;
if ($denuncia && isset($statusTexto[$denuncia['status']])) {
    $analise = $statusTexto[$denuncia['status']];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acompanhar Denúncia</title>
    <link rel="stylesheet" href="css/denuncia.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <main class="container-denuncia">
        <div class="card-formulario">
            <div class="tag-anonimo">🔎 Acompanhar denúncia</div>

            <h1 class="titulo-formulario">Consultar Protocolo</h1>
            <p class="subtitulo-formulario">Digite o número do seu protocolo para acompanhar a situação da denúncia e ver o andamento atual.</p>

            <form class="form-denuncia form-acompanhamento" method="post" action="acompanhar.php">
                <div class="grupo-campo">
                    <label for="protocolo">Número do protocolo</label>
                    <input type="text" id="protocolo" name="protocolo" class="campo-input" value="<?= h($protocoloBusca) ?>" placeholder="Ex.: DEN-20261026-0001" required>
                </div>

                <div class="botoes-formulario">
                    <button type="button" class="btn-voltar" onclick="window.location.href='index.php'">VOLTAR AO INÍCIO</button>
                    <button type="submit" class="btn-enviar">CONSULTAR</button>
                </div>
            </form>

            <?php if ($mensagem): ?>
                <p class="mensagem-alerta"><?= h($mensagem) ?></p>
            <?php endif; ?>

            <?php if ($denuncia && $analise): ?>
                <?php $nivelAtual = $denuncia['nivel_gravidade'] ?? 'intermediario'; ?>
                <div class="resultado-acompanhamento">
                    <div class="linha-status">
                        <span class="status-badge <?= h($denuncia['status']) ?>"><?= h($analise['titulo']) ?></span>
                        <span class="nivel-badge <?= h($nivelAtual) ?>"><?= h($nivelTexto[$nivelAtual]['nome'] ?? 'Intermediário') ?></span>
                    </div>

                    <h2>Detalhes da denúncia</h2>

                    <div class="grid-detalhes">
                        <div>
                            <span>Protocolo</span>
                            <strong><?= h($denuncia['protocolo']) ?></strong>
                        </div>
                        <div>
                            <span>Tipo</span>
                            <strong><?= h($denuncia['tipo_ocorrencia']) ?></strong>
                        </div>
                        <div>
                            <span>Local</span>
                            <strong><?= h($denuncia['endereco_local']) ?></strong>
                        </div>
                        <div>
                            <span>Data</span>
                            <strong><?= h(date('d/m/Y H:i', strtotime($denuncia['criado_em']))) ?></strong>
                        </div>
                    </div>

                    <div class="box-analise">
                        <h3>Análise da situação</h3>
                        <p><?= nl2br(h($analise['texto'])) ?></p>
                        <p class="nivel-descricao <?= h($nivelAtual) ?>"><?= h($nivelTexto[$nivelAtual]['descricao'] ?? 'A gravidade da denúncia foi atualizada pela equipe técnica.') ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="script.js"></script>
</body>

</html>
