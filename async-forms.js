/**
 * async-forms.js — Soumission asynchrone des formulaires via fetch().
 *
 * Tout formulaire portant l'attribut [data-async] est intercepté :
 *  - Affiche un état de chargement sur le bouton submit
 *  - Envoie les données via fetch() avec le header X-Requested-With: fetch
 *  - Sur succès : toast + fermeture modale + redirection douce
 *  - Sur erreur : toast d'erreur, formulaire réactivé
 *
 * Le serveur doit répondre en JSON :
 *   { success: true,  message: "…", redirect: "url" }
 *   { success: false, error:   "…" }
 */

(function () {
    'use strict';

    // ── État de chargement du bouton ──────────────────────────────────────────

    function setLoading(btn, loading) {
        if (!btn) return;
        if (loading) {
            btn.dataset.originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML =
                '<span class="btn-spinner" aria-hidden="true"></span>' +
                '<span>Enregistrement…</span>';
        } else {
            btn.disabled = false;
            btn.innerHTML = btn.dataset.originalHtml || btn.innerHTML;
        }
    }

    // ── Gestionnaire principal ────────────────────────────────────────────────

    document.addEventListener('submit', async function (e) {
        const form = e.target;
        if (!form.matches('[data-async]')) return;
        e.preventDefault();

        const submitBtn = form.querySelector('[type="submit"]');
        setLoading(submitBtn, true);

        try {
            const resp = await fetch(form.action || window.location.pathname, {
                method: 'POST',
                headers: { 'X-Requested-With': 'fetch' },
                // FormData gère automatiquement multipart/form-data (upload photo inclus)
                body: new FormData(form),
            });

            let data;
            try {
                data = await resp.json();
            } catch {
                throw new Error('Réponse serveur invalide. Veuillez réessayer.');
            }

            if (!resp.ok || !data.success) {
                toast(data.error || 'Une erreur est survenue.', 'error');
                // Session expirée ou accès refusé avec redirect → naviguer après le toast
                if (data.redirect && (resp.status === 401 || resp.status === 403)) {
                    setTimeout(() => { window.location.href = data.redirect; }, 1500);
                } else {
                    setLoading(submitBtn, false);
                }
                return;
            }

            // Succès ──────────────────────────────────────────────────────────
            toast(data.message || 'Enregistré !', 'success');

            // Fermer la modale parente si applicable
            const modal = form.closest('.modal');
            if (modal) {
                hideModal(modal.id);
            }

            // Réinitialiser les champs (sauf hidden)
            form.reset();

            // Naviguer vers la destination après que le toast soit visible
            if (data.redirect) {
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 700);
            }

        } catch (err) {
            toast(err.message || 'Erreur réseau. Vérifiez votre connexion.', 'error');
            setLoading(submitBtn, false);
        }
    });

    // ── Suppression photo : stoppe la propagation au clic confirm ────────────
    // Les formulaires delete_photo sont dans des .photo-item avec onclick lightbox ;
    // le stopPropagation est déjà dans le HTML, mais on s'assure que l'async
    // intercepte bien le submit avant que le confirm ne soit annulé.

})();
