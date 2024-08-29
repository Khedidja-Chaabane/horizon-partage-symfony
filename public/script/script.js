//SCRIPT POUR LA HOME
//Fonction JavaScript qui scroll vers l'élément avec l'id 'main-content'.
//Utilise scrollIntoView() avec l'option 'smooth' pour un défilement fluide.
//Appelée lors du clic sur le bouton de défilement dans la section 'hero'
function scrollToContent() {
    const mainContent = document.getElementById('main-content');
    mainContent.scrollIntoView({ behavior: 'smooth' });
    
}
//-------------------------------------------------------------------------------
//Script pour le filtre des actions
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.filtrage .btn');
    buttons.forEach(function(button) {
        button.addEventListener('click', function() {
            buttons.forEach(btn => btn.classList.remove('clicked')); // Retirer la classe active de tous les boutons
            this.classList.add('clicked'); // Ajouter la classe active au bouton cliqué
        });
    });
});

//---------
// SCRIPT POUR LES DONS
// Sélectionner tous les boutons de montant
const montantButtons = document.querySelectorAll('.montant-btn');
// Sélectionner le champ caché pour stocker le montant sélectionné
const hiddenMontantInput = document.querySelector('input[name="don[montant]"]');
// Sélectionner le champ de montant personnalisé
const customMontantInput = document.querySelector('input[name="don[montant_personnalise]"]');

// Fonction pour définir le montant sélectionné
function setMontant(montant) {
    hiddenMontantInput.value = montant; // Mettre à jour le champ caché
    customMontantInput.value = ''; // Effacer le montant personnalisé

    // Mise à jour des styles des boutons
    montantButtons.forEach(button => {
        if (parseInt(button.textContent) === montant) {
            button.classList.add('selected'); // Ajouter la classe 'selected' au bouton cliqué
        } else {
            button.classList.remove('selected'); // Enlever la classe 'selected' des autres boutons
        }
    });
}

// Ajouter un listener pour vider le champ de montant caché lorsque l'utilisateur saisit un montant personnalisé
customMontantInput.addEventListener('input', function() {
    hiddenMontantInput.value = ''; // Effacer le montant sélectionné via boutons
    montantButtons.forEach(button => button.classList.remove('selected')); // Enlever la classe 'selected' de tous les boutons
});

// Initialiser le montant par défaut (20€)
setMontant(20);