/* ==========================================================================
   Hadir. — interaksi sederhana
   - Tampilkan/sembunyikan password
   ========================================================================== */
(function () {
    "use strict";

    document.querySelectorAll("[data-toggle-pass]").forEach(function (button) {
        button.addEventListener("click", function () {
            var input = document.getElementById(button.getAttribute("data-toggle-pass"));
            if (!input) return;

            var hidden = input.type === "password";
            input.type = hidden ? "text" : "password";
            button.setAttribute(
                "aria-label",
                hidden ? "Sembunyikan password" : "Tampilkan password"
            );
        });
    });
})();
