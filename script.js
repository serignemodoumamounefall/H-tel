/* ============================================================
   script.js — Comportements interactifs côté client
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

  // ---- Activation automatique des info-bulles Bootstrap ----
 let infobulles = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  infobulles.forEach(function (el) { new bootstrap.Tooltip(el); });

  // ---- Sélecteur "nombre de chambres" (boutons +/-) ----
  document.querySelectorAll('.selecteur-nombre').forEach(function (groupe) {
   let champ = groupe.querySelector('input[type="number"]');
    groupe.querySelectorAll('.btn-moins').forEach(b => b.addEventListener('click', () => {
      champ.value = Math.max(parseInt(champ.min || 1), parseInt(champ.value) - 1);
      champ.dispatchEvent(new Event('change'));
    }));
    groupe.querySelectorAll('.btn-plus').forEach(b => b.addEventListener('click', () => {
      champ.value = parseInt(champ.value) + 1;
      champ.dispatchEvent(new Event('change'));
    }));
  });

  // ---- Sélection visuelle du type de chambre ----
  document.querySelectorAll('.choix-chambre').forEach(function (carte) {
    carte.addEventListener('click', function () {
      document.querySelectorAll('.choix-chambre').forEach(c => c.classList.remove('selectionne'));
      this.classList.add('selectionne');
     let radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  // ---- Sélection de la méthode de paiement ----
  document.querySelectorAll('.choix-paiement').forEach(function (carte) {
    carte.addEventListener('click', function () {
      document.querySelectorAll('.choix-paiement').forEach(c => c.classList.remove('selectionne'));
      this.classList.add('selectionne');
     let radio = this.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });

  // ---- Filtre des plats du restaurant par catégorie ----
  document.querySelectorAll('.filtre-categorie').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.filtre-categorie').forEach(b => b.classList.remove('actif'));
      this.classList.add('actif');
     let cat = this.dataset.categorie;
      document.querySelectorAll('.plat-item').forEach(function (item) {
        item.style.display = (cat === 'tout' || item.dataset.categorie === cat) ? '' : 'none';
      });
    });
  });

  // ---- Confirmation avant suppression (admin) ----
  document.querySelectorAll('.confirmer-suppression').forEach(function (lien) {
    lien.addEventListener('click', function (e) {
      if (!confirm('Voulez-vous vraiment supprimer cet élément ?')) {
        e.preventDefault();
      }
    });
  });

});
