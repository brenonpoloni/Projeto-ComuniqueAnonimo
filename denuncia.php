<?php
session_start();
$mensagemSucesso = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $protocolo = rand(100000, 999999);
    $mensagemSucesso = "Denúncia enviada com sucesso e de forma 100% anônima! Guarde seu protocolo: #" . $protocolo;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo - Registrar Denúncia</title>
    <link rel="stylesheet" href="css/denuncia.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container-denuncia">
        <div class="card-formulario">
            <div class="tag-anonimo">🔒 Denúncia 100% Anônima</div>

            <h1 class="titulo-formulario">Registrar Ocorrência</h1>
            <p class="subtitulo-formulario">Preencha os detalhes abaixo. Nenhuma informação pessoal ou endereço IP será coletado.</p>

            <?php if ($mensagemSucesso): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;">
                    <?= $mensagemSucesso ?>
                </div>
            <?php endif; ?>

            <form class="form-denuncia" action="denuncia.php" method="POST">
                <div class="grupo-campo">
                    <label for="tipo-ocorrencia">Tipo de Ocorrência *</label>
                    <select id="tipo-ocorrencia" name="tipo-ocorrencia" class="campo-input" required>
                        <option value="" disabled selected>Selecione o tipo de risco ou infração...</option>
                        <option value="seguranca">Risco de Segurança</option>
                        <option value="saude">Risco de Saúde/Higiene</option>
                        <option value="assedio">Assédio ou Discriminação</option>
                        <option value="outros">Outros</option>
                    </select>
                </div>

                <div class="grupo-campo">
                    <label for="empresa-setor">Empresa / Unidade / Setor *</label>
                    <input type="text" id="empresa-setor" name="empresa-setor" class="campo-input" placeholder="Ex: Nome da empresa, setor de produção..." required>
                </div>

                <div class="grupo-campo">
                    <label for="endereco-local">Endereço ou Local da Ocorrência *</label>
                    <input type="text" id="endereco-local" name="endereco-local" class="campo-input" placeholder="Ex: Rua, número, cidade ou setor interno" required>
                </div>

                <div class="grupo-campo">
                    <label for="descricao-fatos">Descrição Detalhada dos Fatos *</label>
                    <textarea id="descricao-fatos" name="descricao-fatos" class="campo-input campo-textarea" placeholder="Descreva o que acontece, datas/horários aproximados..." required></textarea>
                </div>

                <!-- BOTÕES CONECTADOS DO FORMULÁRIO -->
                <div class="botoes-formulario">
                    <a href="index.php" class="btn-voltar" style="text-decoration:none; display:inline-block; text-align:center;">VOLTAR AO INÍCIO</a>
                    <button type="submit" class="btn-enviar">ENVIAR DENÚNCIA ANÔNIMA</button>
                </div>
            </form>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>