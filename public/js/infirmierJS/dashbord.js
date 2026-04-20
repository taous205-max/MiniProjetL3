function changerStatusMedecin(id, nouveauStatus) {
    if (!id || id === 0) return;
    if(confirm("Changer le statut du médecin en : " + nouveauStatus + " ?")) {
        window.location.href = `index.php?page=dashbord&action=update_doc_status&id=${id}&status=${encodeURIComponent(nouveauStatus)}`;
    }
}

function appelerSuivant() {
    if(confirm("Voulez-vous appeler le prochain patient présent ?")) {
        window.location.href = "index.php?page=dashbord&action=call_next";
    }
}