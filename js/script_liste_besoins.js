// Ouvre la popup pour la validation de l'expression des besoins.
function openPopup(commandeId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'Validation_exp_besoins.php?commande_id=' + commandeId, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('popup-content').innerHTML = xhr.responseText;
            document.getElementById('popup').classList.add('show');
        }
    };
    xhr.send();
}

// Ferme la popup de validation de l'expression des besoins
function closePopup() {
    document.getElementById('popup').classList.remove('show');
}

// Fonction qui imprime la Validation de l'Expression de besoins
function printCommande() {
    var printContents = document.getElementById('popup-content').innerHTML;
    var originalContents = document.body.innerHTML;

    var printWindow = window.open('', '', 'height=800, width=1000');
    printWindow.document.write('<html><head><title>Fiche de la Commande</title>');
    printWindow.document.write('<link rel="stylesheet" href="../css/styles_fiche-commande.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}

// Ouvre la popup pour la réquisition
function openRequisitionPopup(commandeId) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'requisition_details.php?commande_id=' + commandeId, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('requisition-popup-content').innerHTML = xhr.responseText;
            document.getElementById('requisition-popup').classList.add('show');
        }
    };
    xhr.send();
}

// Ferme la popup de réquisition
function closeRequisitionPopup() {
    document.getElementById('requisition-popup').classList.remove('show');
}

// Fonction qui imprime la Réquisition
function printRequisition() {
    var printContents = document.getElementById('requisition-popup-content').innerHTML;
    var originalContents = document.body.innerHTML;

    var printWindow = window.open('', '', 'height=800, width=1000');
    printWindow.document.write('<html><head><title>Fiche de Réquisition</title>');
    printWindow.document.write('<link rel="stylesheet" href="../css/styles_fiche-requisition.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
}
