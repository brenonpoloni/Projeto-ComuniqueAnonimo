/* ============================================================
   COMUNIQUE ANÔNIMO - SCRIPTS COMPLEMENTARES FRONT-END
   ============================================================ */

document.addEventListener("DOMContentLoaded", () => {
    // FAQ Interativo na página de suporte
    const itensFAQ = document.querySelectorAll(".item-faq");

    itensFAQ.forEach(item => {
        item.style.cursor = "pointer";
        item.addEventListener("click", () => {
            const resposta = item.querySelector(".resposta-faq");
            if (resposta) {
                const visivel = resposta.style.display === "block";
                resposta.style.display = visivel ? "none" : "block";
            }
        });
    });
});