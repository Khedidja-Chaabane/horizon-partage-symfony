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
document.addEventListener('DOMContentLoaded', function () {
    //DOMContentLoaded est un événement en JavaScript qui se déclenche lorsque le document HTML initial a été complètement chargé et analysé par le navigateur
    // Ce code s'exécute lorsque le DOM est complètement chargé
    const buttons = document.querySelectorAll('.filtrage .btn');
    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            buttons.forEach(btn => btn.classList.remove('clicked')); // Retirer la classe active de tous les boutons
            this.classList.add('clicked'); // Ajouter la classe active au bouton cliqué
        });
    });
});

//-------------------------------------------------------------------------------------------------------------------

// SCRIPT POUR LES DONS

// Sélectionner tous les boutons de montant
const montantButtons = document.querySelectorAll('.montant-btn');
// Sélectionner le champ caché pour stocker le montant sélectionné
const hiddenMontantInput = document.querySelector('input[name="don[montant]"]');
// Sélectionner le champ de montant personnalisé
const customMontantInput = document.querySelector('input[name="don[montant_personnalise]"]');
// Sélectionner le bouton "Autre montant" et le conteneur du champ personnalisé
const autreMontantBtn = document.getElementById('autre-montant-btn');
const montantPersonnaliseContainer = document.getElementById('montant-personnalise-container');
// Sélectionner le message d'erreur
const montantErrorMessage = document.getElementById('montant-error-message');

// Fonction pour définir le montant sélectionné
function setMontant(montant) {
    hiddenMontantInput.value = montant; // Mettre à jour le champ caché
    customMontantInput.value = ''; // Effacer le montant personnalisé

    // Mise à jour des styles des boutons
    montantButtons.forEach(button => {
        //parseInt est une fonction en JavaScript qui convertit une chaîne de caractères en un entier
        //elle est utile ici pour faire une comparaison logique
        //exemple: montant selectionné 50€ sera convertit en nombre entier 50 tout en ignorant le caractère €.
        if (parseInt(button.textContent) === montant) {
            button.classList.add('selected'); // Ajouter la classe 'selected' au bouton cliqué
        } else {
            button.classList.remove('selected'); // Enlever la classe 'selected' des autres boutons
        }
    });

    // Masquer le champ de montant personnalisé si un montant pré-défini est sélectionné
    montantPersonnaliseContainer.classList.add('d-none');
}

// Ajouter un listener pour le bouton "Autre montant"
autreMontantBtn.addEventListener('click', function () {
    montantPersonnaliseContainer.classList.remove('d-none'); // Afficher le champ de montant personnalisé
    customMontantInput.focus(); // Focus sur le champ de saisie
    hiddenMontantInput.value = ''; // Effacer le montant sélectionné via les boutons
    montantButtons.forEach(button => button.classList.remove('selected')); // Enlever la classe 'selected' de tous les boutons
    montantErrorMessage.classList.add('d-none'); // Cacher le message d'erreur

});

// Ajouter un listener pour vider le champ de montant caché lorsque l'utilisateur saisit un montant personnalisé
customMontantInput.addEventListener('input', function () {
    hiddenMontantInput.value = ''; // Effacer le montant sélectionné via boutons
    montantButtons.forEach(button => button.classList.remove('selected')); // Enlever la classe 'selected' de tous les boutons
});
// Validation avant la soumission du formulaire
document.querySelector('form').addEventListener('submit', function (event) {
    // Vérifier si un montant est sélectionné ou saisi
    const montant = hiddenMontantInput.value;
    const montantPersonnalise = customMontantInput.value;

    if (!montant && !montantPersonnalise) {
        event.preventDefault(); // Empêcher la soumission du formulaire
        montantErrorMessage.classList.remove('d-none'); // Afficher le message d'erreur
    }
});
// Initialiser le montant par défaut (50€)
setMontant(50);
