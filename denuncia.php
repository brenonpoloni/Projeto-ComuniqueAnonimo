<?php
require __DIR__ . '/config.php';

$mensagem = '';
$protocolo = $_GET['protocolo'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = trim($_POST['tipo_ocorrencia'] ?? '');
    $empresaSetor = trim($_POST['empresa_setor'] ?? '');
    $enderecoLocal = trim($_POST['endereco_local'] ?? '');
    $descricao = trim($_POST['descricao_fatos'] ?? '');

    if (!$tipo || !$empresaSetor || !$enderecoLocal || !$descricao) {
        $mensagem = 'Preencha todos os campos obrigatórios.';
    } elseif (mb_strlen($empresaSetor) < 10 || mb_strlen($enderecoLocal) < 10 || mb_strlen($descricao) < 10) {
        $mensagem = 'Os campos de texto devem conter pelo menos 10 caracteres.';
    } else {
        $ultimoId = (int) $pdo->query('SELECT COALESCE(MAX(id), 0) FROM denuncias')->fetchColumn();
        $protocolo = 'DEN-' . date('Ymd') . '-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);

        $caminhoAnexo = null;
        if (!empty($_FILES['anexo_evidencias']['name'])) {
            $pasta = __DIR__ . '/uploads';
            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }

            $nomeOriginal = basename($_FILES['anexo_evidencias']['name']);
            $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
            $novoNome = uniqid('evidencia_', true) . '.' . $extensao;
            $destino = $pasta . '/' . $novoNome;

            if (move_uploaded_file($_FILES['anexo_evidencias']['tmp_name'], $destino)) {
                $caminhoAnexo = 'uploads/' . $novoNome;
            }
        }

        $stmt = $pdo->prepare(
            'INSERT INTO denuncias (protocolo, tipo_ocorrencia, empresa_setor, endereco_local, descricao, anexo, status) VALUES (:protocolo, :tipo, :empresa_setor, :endereco_local, :descricao, :anexo, :status)'
        );
        $stmt->execute([
            ':protocolo' => $protocolo,
            ':tipo' => $tipo,
            ':empresa_setor' => $empresaSetor,
            ':endereco_local' => $enderecoLocal,
            ':descricao' => $descricao,
            ':anexo' => $caminhoAnexo,
            ':status' => 'pendente',
        ]);

        header('Location: denuncia.php?protocolo=' . urlencode($protocolo));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo</title>
    <link rel="stylesheet" href="css/denuncia.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <main class="container-denuncia">
        <div class="card-formulario">
            <div class="tag-anonimo">🔒 Denúncia 100% Anônima</div>

            <h1 class="titulo-formulario">Registrar Ocorrência</h1>
            <p class="subtitulo-formulario">Preencha os detalhes abaixo. Nenhuma informação pessoal ou endereço IP será coletado.</p>

            <?php if ($protocolo): ?>
                <p class="mensagem-sucesso">
                    Denúncia registrada com sucesso! Protocolo: <strong><?= h($protocolo) ?></strong>
                </p>
                <p class="mensagem-alerta mensagem-login">Para mais informações, realize login ou crie uma conta no Comunique Anônimo!</p>
                <div class="area-acompanhamento-rapido">
                    <a href="acompanhar.php?protocolo=<?= urlencode($protocolo) ?>" class="btn-acompanhamento">ACOMPANHAR MINHA DENÚNCIA</a>
                </div>
            <?php elseif ($mensagem): ?>
                <p class="mensagem-erro">
                    <?= h($mensagem) ?>
                </p>
            <?php endif; ?>

            <form class="form-denuncia" method="post" action="denuncia.php" enctype="multipart/form-data">
                <div class="grupo-campo">
                    <label for="tipo-ocorrencia">Tipo de Ocorrência *</label>
                    <select id="tipo-ocorrencia" name="tipo_ocorrencia" class="campo-input" required>
                        <option value="" disabled selected>Selecione o tipo de risco ou infração...</option>
                        <option value="seguranca">Risco de Segurança</option>
                        <option value="saude">Risco de Saúde/Higiene</option>
                        <option value="assedio">Assédio ou Discriminação</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="empresa-setor">Empresa / Unidade / Setor *</label>
                    <input type="text" id="empresa-setor" name="empresa_setor" class="campo-input" placeholder="Ex: Nome da empresa, setor de produção, filial..." required minlength="10">
                </div>

                <div class="grupo-campo">
                    <label for="endereco-local">Endereço ou Local da Ocorrência *</label>
                    <input type="text" id="endereco-local" name="endereco_local" class="campo-input" placeholder="Ex: Rua, número, cidade ou setor interno" required minlength="10">
                </div>

                <div class="grupo-campo">
                    <label for="descricao-fatos">Descrição Detalhada dos Fatos *</label>
                    <textarea id="descricao-fatos" name="descricao_fatos" class="campo-input campo-textarea" placeholder="Descreva o que acontece, datas/horários aproximados e quem são os envolvidos (se aplicável)..." required minlength="10"></textarea>
                </div>

                <div class="grupo-campo">
                    <label for="anexo-evidencias">Anexar Evidências (Fotos, Vídeos ou Documentos)</label>
                    <div class="container-file-input">
                        <input type="file" id="anexo-evidencias" name="anexo_evidencias">
                    </div>
                    <span class="instrucao-anexo">Formatos aceitos: JPG, PNG, MP4, PDF, DOCX (Máx. 25MB)</span>
                </div>

                <div class="botoes-formulario">
                    <button type="button" class="btn-voltar">VOLTAR AO INÍCIO</button>
                    <button type="submit" class="btn-enviar">ENVIAR DENÚNCIA ANÔNIMA</button>
                </div>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
</body>

</html>