<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>
<header>
    <div class="topo"> 
        <div class="logo">
            <a href="index.php"><img src="imagem/logocomunique.png" alt="Logo Comunique Anônimo"></a>
        </div>
       
        <!-- MENU CONECTANDO TODAS AS TELAS -->
        <nav class="menu-de-denuncia">
            <a href="index.php" class="botao-menu <?= ($pagina_atual == 'index.php') ? 'ativo' : '' ?>">INÍCIO</a>
            <a href="denuncia.php" class="botao-menu <?= ($pagina_atual == 'denuncia.php') ? 'ativo' : '' ?>">DENUNCIAR</a>
            <a href="conta.php" class="botao-menu <?= ($pagina_atual == 'conta.php') ? 'ativo' : '' ?>">CONTA</a>
            <a href="suporte.php" class="botao-menu <?= ($pagina_atual == 'suporte.php') ? 'ativo' : '' ?>">SUPORTE</a>
        </nav>
    </div>
</header>