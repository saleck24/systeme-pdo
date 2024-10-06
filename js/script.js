document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('input[name="password"]');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            // Changer l'icône en fonction du type de champ
            if (type === 'password') {
                this.src = 'img/icon-eye.png'; // Icône de l'œil fermée
            } else {
                this.src = 'img/icon-eye-off.png'; // Icône de l'œil ouvert
            }
        });
    } else {
        console.error('Elements not found');
    }

// Ajout d'un loader lors de la soumission du formulaire
const form = document.querySelector('form');
form.addEventListener('submit', function(event) {
    // Afficher le loader
    document.getElementById('loader').style.display = 'block';

    // Désactiver le bouton de soumission pour éviter les doubles soumissions
    form.querySelector('button[type="submit"]').disabled = true;
    // Attendre 10 secondes avant de soumettre le formulaire
    setTimeout(function() {
        form.submit(); // Soumettre le formulaire après le délai
    }, 10000); // 10000 millisecondes = 10 secondes

});
});
