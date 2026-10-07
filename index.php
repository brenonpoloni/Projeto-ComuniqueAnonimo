<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo - Início</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="banner-principal">
        <div class="conteudo-banner">
            <div class="texto-principal">
                <h1>SUA VOZ!<br><span class="destaque-amarelo">SEM RISCOS!</span></h1>
                <p>Denuncie riscos de segurança e saúde no trabalho de forma completamente anônima. Protegemos sua identidade em todas as etapas.</p>
                
                <!-- BOTÕES CONECTADOS -->
                <div class="botoes-acao">
                    <a href="denuncia.php" class="btn-amarelo" style="text-decoration:none; display:inline-block;">FAZER DENÚNCIA</a>
                    <a href="suporte.php" class="btn-branco" style="text-decoration:none; display:inline-block;">SAIBA MAIS</a>
                </div>
            </div>

            <div class="card-beneficios">
                <ul>
                    <li><span class="icone-check">✅</span> Protege você e seus colegas de trabalho</li>
                    <li><span class="icone-check">✅</span> Contribui para um ambiente mais seguro</li>
                    <li><span class="icone-check">✅</span> É um direito garantido por lei</li>
                    <li><span class="icone-check">✅</span> Sua identidade jamais será revelada</li>
                    <li><span class="icone-check">✅</span> Acompanhe o andamento da denúncia</li>
                    <li><span class="icone-check">✅</span> Suporte jurídico disponível 24h</li>
                </ul>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>