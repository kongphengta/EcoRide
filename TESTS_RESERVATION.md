# Tests à effectuer sur le système de réservation EcoRide

## ✅ Tests fonctionnels de base

### Scénario 1 : Réservation complète réussie
1. **Passager** réserve un trajet (2 places par exemple)
   - Vérifier débit des crédits
   - Vérifier mise à jour des places restantes
   - Vérifier statut "En attente"

2. **Conducteur** accepte la réservation
   - Vérifier changement de statut vers "Confirmée"
   - Vérifier que l'action n'est plus disponible

3. **Vérifications finales**
   - Crédits toujours débités
   - Places toujours réservées
   - Réservation visible dans les deux dashboards

### Scénario 2 : Réservation rejetée
1. **Passager** réserve un trajet
2. **Conducteur** rejette la réservation
   - Vérifier remboursement des crédits
   - Vérifier remise à disposition des places
   - Vérifier statut "Rejetée"

### Scénario 3 : Annulation par le passager
1. **Passager** réserve puis annule
   - Vérifier remboursement des crédits
   - Vérifier remise à disposition des places
   - Vérifier statut "Annulée"

### Scénario 4 : Annulation par le conducteur
1. **Conducteur** annule une réservation confirmée
   - Vérifier remboursement des crédits
   - Vérifier remise à disposition des places
   - Vérifier statut "Annulée par le chauffeur"

## ❌ Tests des cas d'erreur

### Sécurité
- [ ] Réserver son propre trajet (doit être bloqué)
- [ ] Réserver deux fois le même trajet (doit être bloqué)
- [ ] Accéder aux actions d'un autre utilisateur (doit être bloqué)

### Validation métier
- [ ] Réserver sans crédits suffisants (doit être bloqué)
- [ ] Réserver plus de places que disponible (doit être bloqué)
- [ ] Réserver avec 0 place (doit être bloqué)

### Interface utilisateur
- [ ] Affichage correct des dashboards
- [ ] Breadcrumbs fonctionnels
- [ ] Messages flash appropriés
- [ ] Calcul dynamique du prix

## 🔧 Améliorations suggérées (optionnelles)

1. **Pagination** des réservations dans les dashboards
2. **Filtres** par statut/date dans les dashboards  
3. **Notifications temps réel** (WebSockets)
4. **Tests automatisés** (PHPUnit)
5. **API REST** pour mobile
6. **Système de notation** après trajet
7. **Historique des actions** (logs)

## 📧 Test des emails (si configuré)

- [ ] Email de création de réservation
- [ ] Email de confirmation au passager
- [ ] Email de rejet au passager  
- [ ] Email de nouvelle réservation au conducteur
- [ ] Email d'annulation par le conducteur

---

**Status:** ✅ Système complet et opérationnel
**Dernière mise à jour:** 4 août 2025
