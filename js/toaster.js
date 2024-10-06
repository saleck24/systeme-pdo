// Fonction pour afficher un message de toast
function showToast(message, type = 'success') {
    // Créer un élément div pour le toast
    const toast = document.createElement('div');
    toast.classList.add('toast', 'show');

    // Ajouter une icône en fonction du type de toast
    const icon = document.createElement('span');
    icon.classList.add('toast-icon');
    icon.innerHTML = (type === 'success') ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';
    //icon.innerHTML = (type === 'success') ? '✔️' : '❌';

    // Appliquer les styles en fonction du type (success, error)
    if (type === 'success') {
        toast.classList.add('toast-success');
    } else if (type === 'error') {
        toast.classList.add('toast-error');
    }

    // Créer un bouton de fermeture
    const closeButton = document.createElement('button');
    closeButton.innerHTML = '&times;';
    closeButton.classList.add('close-btn');
    closeButton.onclick = () => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 500);  // Supprimer après l'animation de sortie
    };

    // Insérer l'icône et le message dans le toast
    toast.appendChild(icon);
    toast.append(message);

    // Ajouter le bouton de fermeture au toast
    toast.appendChild(closeButton);

    // Ajouter le toast au conteneur de toasts
    const toastContainer = document.getElementById('toast-container');
    toastContainer.appendChild(toast);

    // Retirer automatiquement le toast après un certain délai (10 secondes)
    setTimeout(() => {
        toast.classList.remove('show');
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 500);  // Supprimer après l'animation de sortie
    }, 10000);
}
