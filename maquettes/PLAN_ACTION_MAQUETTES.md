# 🎨 PLAN D'ACTION - MAQUETTES ECORIDE

## 📋 **Guide Complet de Réalisation des 6 Maquettes**

### 🎯 **Objectif**
Créer 6 maquettes (3 desktop + 3 mobile) pour compléter votre dossier d'examen.

---

## 🚀 **ÉTAPE 1 : Configuration de l'Outil**

### **Figma (Recommandé - Gratuit)**

1. **Créer un compte :** https://figma.com
2. **Créer un nouveau fichier :** "EcoRide - Maquettes Examen"
3. **Organiser en pages :**
   - Page 1 : Wireframes Desktop
   - Page 2 : Wireframes Mobile  
   - Page 3 : Mockups Desktop
   - Page 4 : Mockups Mobile

### **Templates de tailles :**
- **Desktop :** 1440x900px (Standard laptop)
- **Mobile :** 375x812px (iPhone standard)

---

## 📐 **ÉTAPE 2 : WIREFRAMES (Structure) - 4-6h**

### **2.1 - Page d'Accueil Desktop** (90 min)

**Structure à reproduire :**
```
┌─ Header ────────────────────────────────────────┐
│ [Logo EcoRide] [Accueil][Trajets][Contact] [Menu User] │
├─ Hero Carousel ──────────────────────────────────┤
│ [Image Carousel avec boutons navigation]         │
│ "Voyage écologique" + texte                     │
├─ Section Recherche ──────────────────────────────┤
│ "Trouvez votre covoiturage"                     │
│ [Départ] [Arrivée] [Date] [Rechercher]          │
├─ Section Trajets Populaires ─────────────────────┤
│ [Card Trajet 1] [Card Trajet 2] [Card Trajet 3] │
├─ Section Avantages ──────────────────────────────┤
│ [Écologique] [Économique] [Convivial]           │
└─ Footer ────────────────────────────────────────┘
```

**Éléments clés :**
- Navigation Bootstrap avec logo
- Carousel 3 images avec indicateurs
- Formulaire de recherche centré
- Grille 3 colonnes pour les avantages
- Footer avec liens et contact

### **2.2 - Dashboard Conducteur Desktop** (90 min)

**Structure basée sur votre app :**
```
┌─ Header Navigation ─────────────────────────────┐
│ [Logo] [Menu] [Avatar + Dropdown]              │
├─ Breadcrumb ────────────────────────────────────┤
│ Accueil > Mon Profil > Mes Covoiturages        │
├─ Statistiques Cards ────────────────────────────┤
│ [Trajets créés] [Réservations] [Crédits] [Note] │
├─ Mes Trajets ───────────────────────────────────┤
│ [Tableau avec: Date, Destination, Places, Actions] │
├─ Réservations en Attente ──────────────────────┤
│ [Liste des demandes avec boutons Accepter/Refuser] │
└─ Footer ───────────────────────────────────────┘
```

### **2.3 - Formulaire Réservation Desktop** (90 min)

**Structure workflow complet :**
```
┌─ Header ────────────────────────────────────────┐
│ [Navigation classique]                          │
├─ Breadcrumb ────────────────────────────────────┤
│ Trajets > Détails > Réservation                │
├─ Main Content (2 colonnes) ────────────────────┤
│ │ Détails Trajet    │ Formulaire Réservation │  │
│ │ - Date/Heure      │ - Nombre de places     │  │
│ │ - Itinéraire      │ - Informations         │  │
│ │ │ - Prix/place    │ - Crédits disponibles  │  │
│ │ - Conducteur      │ - [Bouton Réserver]    │  │
├─ Actions ───────────────────────────────────────┤
│ [Retour] [Réserver Maintenant]                 │
└─ Footer ───────────────────────────────────────┘
```

### **2.4 - Page d'Accueil Mobile** (60 min)

**Adaptation mobile :**
```
┌─ Header Mobile ─────────┐
│ [Logo] [☰ Menu]         │
├─ Hero Compact ─────────┤
│ [Image + Titre]        │
├─ Recherche ────────────┤
│ [Départ]               │
│ [Arrivée]              │
│ [Date] [🔍]            │
├─ Trajets Stack ───────┤
│ [Card Trajet 1]        │
│ [Card Trajet 2]        │
│ [Card Trajet 3]        │
├─ CTA Flottant ─────────┤
│ [+ Proposer trajet]    │
└─ Bottom Nav ───────────┘
```

### **2.5 - Dashboard Mobile** (60 min)

**Navigation mobile :**
```
┌─ Header ───────────────┐
│ [☰] EcoRide [🔔]      │
├─ Stats Cards ─────────┤
│ [Trajets] [Crédits]    │
├─ Menu Actions ────────┤
│ [Mes Trajets]          │
│ [Réservations]         │
│ [Profil]              │
├─ Contenu Principal ───┤
│ [Liste adaptée mobile] │
├─ Bottom Tabs ─────────┤
│ [🏠][🚗][👤][⚙️]      │
└────────────────────────┘
```

### **2.6 - Recherche Mobile** (60 min)

**Interface tactile :**
```
┌─ Search Header ────────┐
│ [←] Recherche [🔍]     │
├─ Filtres Scroll ─────┤
│ [Date][Prix][Places]   │
├─ Résultats ───────────┤
│ [Card Trajet 1]        │
│ [Card Trajet 2]        │
│ [Card Trajet 3]        │
├─ Carte Intégrée ─────┤
│ [Mini Map avec points] │
└─ Actions ─────────────┘
```

---

## 🎨 **ÉTAPE 3 : MOCKUPS (Design Final) - 8-12h**

### **3.1 - Préparation Design System**

**Récupérer de votre charte graphique :**
- **Couleur principale :** `#28a745` (Vert Bootstrap)
- **Couleur secondaire :** `#20c997`
- **Grises :** `#f8f9fa`, `#6c757d`, `#343a40`
- **Police :** Inter/System fonts
- **Composants :** Bootstrap 5 style

### **3.2 - Application du Design**

**Pour chaque wireframe :**
1. **Dupliquer** le wireframe
2. **Appliquer les couleurs** de la charte
3. **Ajouter la typographie** (Inter/Roboto)
4. **Insérer les icônes** Bootstrap Icons
5. **Ajouter les images** (placeholders ou vraies)
6. **Peaufiner les détails** (ombres, bordures)

### **3.3 - Éléments de Design à Inclure**

**Navigation :**
- Fond vert `#28a745`
- Logo EcoRide blanc
- Menu dropdown avec avatar

**Cards/Boutons :**
- Border-radius: 8px
- Ombre: `0 2px 4px rgba(0,0,0,0.1)`
- Boutons verts avec hover

**Formulaires :**
- Inputs avec border `#ced4da`
- Focus bleu Bootstrap
- Labels gris foncé

---

## 📱 **ÉTAPE 4 : ADAPTATIONS MOBILES**

### **Points d'Attention Mobile :**

1. **Navigation :** Menu hamburger obligatoire
2. **Touch Targets :** Boutons minimum 44px
3. **Lisibilité :** Texte minimum 16px
4. **Espacement :** Plus généreux pour le tactile
5. **Images :** Optimisées pour petit écran

---

## 📁 **ÉTAPE 5 : EXPORT ET LIVRAISON**

### **5.1 - Export des Fichiers**

**Wireframes :**
- Format : PNG 2x (haute résolution)
- Noms : `desktop_accueil.png`, `mobile_dashboard.png`, etc.

**Mockups :**
- Format : PNG 2x 
- Noms : `desktop_accueil_hd.png`, `mobile_recherche_hd.png`, etc.

### **5.2 - Création des PDFs**

**2 documents PDF :**
1. **`maquettes_wireframes.pdf`** - Toutes les structures
2. **`maquettes_mockups.pdf`** - Tous les designs finaux

**Structure PDF :**
```
Page 1 : Page de garde avec titre
Page 2 : Desktop - Page d'Accueil
Page 3 : Desktop - Dashboard
Page 4 : Desktop - Réservation
Page 5 : Mobile - Page d'Accueil  
Page 6 : Mobile - Dashboard
Page 7 : Mobile - Recherche
Page 8 : Notes et commentaires
```

---

## ⏰ **PLANNING DÉTAILLÉ**

### **Jour 1 (3-4h) - Wireframes Desktop**
- [ ] Configuration Figma
- [ ] Page d'Accueil Desktop (90 min)
- [ ] Dashboard Conducteur Desktop (90 min)
- [ ] Formulaire Réservation Desktop (90 min)

### **Jour 2 (2-3h) - Wireframes Mobile** 
- [ ] Page d'Accueil Mobile (60 min)
- [ ] Dashboard Mobile (60 min)
- [ ] Recherche Mobile (60 min)

### **Jour 3-4 (6-8h) - Mockups**
- [ ] Application design system
- [ ] Mockups Desktop (4h)
- [ ] Mockups Mobile (3h)
- [ ] Finalisation et export (1h)

### **Jour 5 (1-2h) - Export Final**
- [ ] Export PNG haute résolution
- [ ] Création PDFs professionnels
- [ ] Vérification qualité

---

## 💡 **CONSEILS PRATIQUES**

### **Inspiration Design :**
- Votre application EcoRide existante
- BlaBlaCar (référence covoiturage)
- Bootstrap 5 documentation
- Material Design (pour mobile)

### **Raccourcis Figma :**
- `Ctrl+D` : Dupliquer
- `Alt+Drag` : Copier élément
- `Ctrl+G` : Grouper
- `Ctrl+Shift+O` : Outline stroke

### **Cohérence à Maintenir :**
- Mêmes couleurs partout
- Espacements réguliers (8px grid)
- Icônes de même style
- Hiérarchie typographique claire

---

## 🎯 **CRITÈRES DE VALIDATION**

### **Wireframes :**
- [ ] Structure claire et logique
- [ ] Navigation cohérente
- [ ] Placement optimal des éléments
- [ ] Responsive bien pensé

### **Mockups :**
- [ ] Respect charte graphique
- [ ] Typographie professionnelle
- [ ] Couleurs harmonieuses
- [ ] Détails soignés

### **Exports :**
- [ ] Haute résolution
- [ ] Noms de fichiers corrects
- [ ] PDFs bien organisés
- [ ] Prêt pour l'examen

---

**🎉 Ce plan vous guide étape par étape pour créer des maquettes professionnelles qui impressionneront le jury !**

*Prêt à commencer ? Dites-moi par quelle étape vous voulez débuter !*
