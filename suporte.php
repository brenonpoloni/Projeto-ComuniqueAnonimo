<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comunique Anônimo - Suporte</title>
    <link rel="stylesheet" href="css/suporte.css">
</head>
<body>

    <?php include 'header.php'; ?>

    <main class="container-suporte">
        <div class="grid-suporte">
            <section class="card-suporte">
                <h1 class="titulo-secao">Suporte e Perguntas Frequentes</h1>
                <p class="subtitulo-secao">Tire suas dúvidas sobre o funcionamento do canal de denúncias.</p>

                <div class="lista-faq">
                    <div class="item-faq" data-aberto="false">
                        <div class="faq-header">
                            <span>A minha denúncia é realmente anônima?</span>
                            <span class="icone-expandir">+</span>
                        </div>
                        <div class="faq-resposta">
                            Sim. O sistema não registra seu nome, endereço IP ou outros dados pessoais na denúncia. A identidade do autor é preservada para manter o canal confidencial.
                        </div>
                    </div>

                    <div class="item-faq" data-aberto="false">
                        <div class="faq-header">
                            <span>Como acompanho o andamento?</span>
                            <span class="icone-expandir">+</span>
                        </div>
                        <div class="faq-resposta">
                            Após enviar a denúncia, você recebe um protocolo exclusivo. Com esse número, você pode verificar o status da ocorrência na página de acompanhamento.
                        </div>
                    </div>

                    <div class="item-faq" data-aberto="false">
                        <div class="faq-header">
                            <span>Como posso entrar em contato?</span>
                            <span class="icone-expandir">+</span>
                        </div>
                        <div class="faq-resposta">
                            Você pode enviar uma mensagem pelo formulário ao lado direito da página. Também é possível usar o e-mail institucional do suporte, caso seja necessário complementar a solicitação.
                        </div>
                    </div>
                </div>
            </section>

            <section class="card-suporte">
                <h2 class="titulo-secao">Fale com o suporte</h2>
                <p class="subtitulo-secao">Envie uma mensagem e responderemos o mais rápido possível.</p>

                <form class="form-suporte" method="post" action="suporte.php">
                    <div class="grupo-campo">
                        <label for="assunto">Assunto</label>
                        <input type="text" id="assunto" name="assunto" class="campo-input" placeholder="Ex: Dúvida sobre denúncia" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="email">E-mail</label>
                        <input type="email" id="email" name="email" class="campo-input" placeholder="seu.email@exemplo.com" required>
                    </div>

                    <div class="grupo-campo">
                        <label for="mensagem">Mensagem</label>
                        <textarea id="mensagem" name="mensagem" class="campo-input campo-textarea" placeholder="Descreva sua dúvida ou problema..." required></textarea>
                    </div>

                    <button type="submit" class="btn-enviar-suporte">ENVIAR MENSAGEM</button>
                </form>
            </section>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>