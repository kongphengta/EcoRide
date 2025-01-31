// script.js
document.getElementById('search-form').addEventListener('submit', function(event) {
    event.preventDefault();

    const lieuDepart = document.getElementById('lieu_depart').value;
    const lieuArrivee = document.getElementById('lieu_arrivee').value;

    if (lieuDepart && lieuArrivee) {
        // Appeler une API ou un contrôleur Symfony pour rechercher les trajets
        fetch(`/api/search?lieu_depart=${lieuDepart}&lieu_arrivee=${lieuArrivee}`)
            .then(response => response.json())
            .then(data => {
                const resultsContainer = document.getElementById('search-results');
                resultsContainer.innerHTML = '';
                if (data.trajets.length > 0) {
                    data.trajets.forEach(trajet => {
                        const div = document.createElement('div');
                        div.className = 'alert alert-info';
                        div.innerHTML = `
                            <strong>Trajet ${trajet.id}:</strong> ${trajet.lieu_depart} -> ${trajet.lieu_arrivee} à ${trajet.prix}€
                        `;
                        resultsContainer.appendChild(div);
                    });
                } else {
                    resultsContainer.innerHTML = '<p>Aucun trajet trouvé.</p>';
                }
            })
            .catch(error => console.error('Erreur:', error));
    } else {
        alert('Veuillez remplir tous les champs.');
    }
});