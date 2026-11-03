<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pagina_atual = basename($_SERVER['PHP_SELF']);
$usuario_logado = !empty($_SESSION['usuario_id']);
$texto_acesso = $usuario_logado ? 'SAIR' : 'ENTRAR';
$link_acesso = $usuario_logado ? 'logout.php' : 'login.php';
$link_denuncia = 'denuncia.php';
?>
<header>
    <div class="topo">
        <div class="logo">
            <a href="index.php"><img src="imagem/logocomunique.png" alt="Logo Comunique Anônimo"></a>
        </div>

        <nav class="menu-de-denuncia">
            <a href="index.php" class="botao-menu <?= ($pagina_atual == 'index.php') ? 'ativo' : '' ?>">INÍCIO</a>
            <a href="<?= $link_denuncia ?>" class="botao-menu <?= ($pagina_atual == 'denuncia.php') ? 'ativo' : '' ?>">DENUNCIAR</a>
            <a href="acompanhar.php" class="botao-menu <?= ($pagina_atual == 'acompanhar.php') ? 'ativo' : '' ?>">ACOMPANHAR</a>
            <a href="conta.php" class="botao-menu <?= ($pagina_atual == 'conta.php') ? 'ativo' : '' ?>">CONTA</a>
            <a href="suporte.php" class="botao-menu <?= ($pagina_atual == 'suporte.php') ? 'ativo' : '' ?>">SUPORTE</a>
            <a href="<?= $link_acesso ?>" class="botao-menu btn-entrar-menu"><?= $texto_acesso ?></a>
        </nav>
    </div>
</header>