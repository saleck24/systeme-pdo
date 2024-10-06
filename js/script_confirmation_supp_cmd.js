function confirmDelete(id, commandeId) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Cette action est irréversible !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Rediriger vers la page de suppression si l'utilisateur confirme
            window.location.href = 'supprimer_commande.php?id=' + id + '&commande_id=' + commandeId;
        }
    });
}

