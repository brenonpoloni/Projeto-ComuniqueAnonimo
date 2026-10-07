<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo - Suporte</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container-principal-conta" style="padding-top: 40px;">
        <h1>Suporte e Perguntas Frequentes</h1>
        <p>Tire suas dúvidas sobre o funcionamento do canal de denúncias.</p>

        <section style="margin-top: 30px;">
            <div class="item-faq" style="background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 6px;">
                <h3>A minha denúncia é realmente anônima?</h3>
                <p class="resposta-faq">Sim! O sistema não grava o seu endereço IP nem dados pessoais na denúncia.</p>
            </div>
            <div class="item-faq" style="background: #f8f9fa; padding: 15px; margin-bottom: 10px; border-radius: 6px;">
                <h3>Como acompanho o andamento?</h3>
                <p class="resposta-faq">Guarde o número do protocolo gerado ao enviar a denúncia para verificar a evolução.</p>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>