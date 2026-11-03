document.addEventListener("DOMContentLoaded", () => {
    const botoes = document.querySelectorAll(".botao-menu");
    const caminhoAtual = window.location.pathname.toLowerCase();

    botoes.forEach((btn) => {
        const texto = btn.textContent.trim().toUpperCase();

        if (
            (texto === "INÍCIO" && (caminhoAtual.includes("index.php") || caminhoAtual.endsWith("/"))) ||
            (texto === "DENUNCIAR" && caminhoAtual.includes("denuncia.php")) ||
            (texto === "ACOMPANHAR" && caminhoAtual.includes("acompanhar.php")) ||
            (texto === "CONTA" && caminhoAtual.includes("conta.php")) ||
            (texto === "SUPORTE" && caminhoAtual.includes("suporte.php"))
        ) {
            btn.style.color = "#FFCD00";
            btn.style.borderBottom = "2px solid #FFCD00";
        }
    });

    const faqItems = document.querySelectorAll('.item-faq');
    faqItems.forEach((item) => {
        const header = item.querySelector('.faq-header');

        if (header) {
            header.addEventListener('click', () => {
                const estaAberto = item.dataset.aberto === 'true';

                faqItems.forEach((outroItem) => {
                    outroItem.dataset.aberto = 'false';
                });

                item.dataset.aberto = String(!estaAberto);
            });
        }
    });

    document.querySelectorAll('input[minlength], textarea[minlength]').forEach((campo) => {
        const minimo = Number.parseInt(campo.getAttribute('minlength') || '0', 10);

        campo.addEventListener('input', () => {
            const texto = campo.value.trim();
            if (texto.length >= minimo) {
                campo.setCustomValidity('');
            } else {
                campo.setCustomValidity(`Digite pelo menos ${minimo} caracteres.`);
            }
        });

        campo.addEventListener('blur', () => {
            const texto = campo.value.trim();
            if (texto.length < minimo && campo.hasAttribute('required')) {
                campo.setCustomValidity(`Digite pelo menos ${minimo} caracteres.`);
            }
        });
    });

    const btnFazerDenuncia = document.querySelector(".btn-amarelo");
    const btnSaibaMais = document.querySelector(".btn-branco");
    const btnVoltar = document.querySelector(".btn-voltar");

    if (btnFazerDenuncia) {
        const destinoDenuncia = window.__redirectDenuncia || "denuncia.php";
        btnFazerDenuncia.addEventListener("click", () => window.location.href = destinoDenuncia);
    }

    if (btnSaibaMais) {
        btnSaibaMais.addEventListener("click", () => window.location.href = "suporte.php");
    }

    if (btnVoltar) {
        btnVoltar.addEventListener("click", () => window.location.href = "index.php");
    }
});