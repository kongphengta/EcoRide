# 🎨 TUTORIEL FIGMA - ÉTAPE PAR ÉTAPE

## 🚀 **Guide Pratique pour Créer vos Maquettes EcoRide**

---

## 📋 **ÉTAPE 1 : CONFIGURATION FIGMA**

### **1.1 - Créer le Projet**
1. Aller sur **https://figma.com**
2. **Sign up** ou **Log in**
3. Cliquer **"New Design File"**
4. Renommer : **"EcoRide - Maquettes Examen"**

### **1.2 - Organiser les Pages**
Dans le panneau de gauche, créer 4 pages :
- 📐 **Wireframes Desktop** 
- 📱 **Wireframes Mobile**
- 🎨 **Mockups Desktop**
- 📲 **Mockups Mobile**

### **1.3 - Créer les Frames**
Pour chaque page, créer les frames :

**Desktop** : Clic droit → **Frame** → Choisir **"Desktop"** (1440x1024)
**Mobile** : Clic droit → **Frame** → Choisir **"iPhone 14"** (390x844)

---

## 🏗️ **ÉTAPE 2 : WIREFRAMES DESKTOP (Page 1)**

### **2.1 - Page d'Accueil Desktop (90 min)**

#### **A. Créer le Header (15 min)**
```
1. Rectangle 1440x80px en haut
2. Texte "EcoRide" aligné à gauche  
3. Menu horizontal au centre : Accueil | Trajets | Contact
4. Zone utilisateur à droite : Avatar + Dropdown
```

**Raccourcis Figma :**
- `R` : Rectangle
- `T` : Texte
- `Ctrl+D` : Dupliquer
- `Alt+Drag` : Copier

#### **B. Créer le Carousel (20 min)**
```
1. Rectangle 1440x400px sous le header
2. 3 cercles en bas pour indicateurs
3. Flèches gauche/droite
4. Texte overlay : "Voyage écologique"
```

#### **C. Section Recherche (25 min)**
```
1. Container 800px centré
2. Titre "Trouvez votre trajet"
3. 3 inputs côte à côte :
   - Départ (300px)
   - Arrivée (300px)  
   - Date (200px)
4. Bouton "Rechercher" pleine largeur
```

#### **D. Section Engagement (20 min)**
```
1. Container 1140px
2. Titre + paragraphe
3. Grille 2 colonnes :
   - Image placeholder + titre
   - Image placeholder + titre
4. CTA centré en bas
```

#### **E. Footer (10 min)**
```
1. Rectangle 1440x200px, fond gris
2. 2 colonnes :
   - Logo + description + email
   - Liens légaux (3 liens)
```

### **2.2 - Dashboard Conducteur (90 min)**

#### **A. Header + Breadcrumb (15 min)**
```
1. Réutiliser header précédent (Ctrl+C, Ctrl+V)
2. Breadcrumb sous header : Accueil > Profil > Covoiturages
```

#### **B. Stats Cards (25 min)**
```
1. 4 rectangles 250x120px
2. Grille 4 colonnes avec espacement 20px
3. Dans chaque card :
   - Icône (cercle 40px)
   - Nombre (gros texte)
   - Label (petit texte)
```

#### **C. Tableau Trajets (35 min)**
```
1. Rectangle container 1140px
2. Header tableau : 5 colonnes
   - Trajet | Date | Statut | Réservations | Actions
3. 3 lignes de données
4. Bouton "Nouveau trajet" en haut à droite
```

#### **D. Réservations en Attente (15 min)**
```
1. Container 1140px
2. Titre "Réservations en Attente"
3. 2 cards avec :
   - Avatar + nom
   - Détails trajet
   - Boutons Accepter/Refuser
```

### **2.3 - Formulaire Réservation (90 min)**

#### **A. Header + Breadcrumb (10 min)**
```
1. Réutiliser header
2. Breadcrumb : Trajets > Détails > Réservation
```

#### **B. Layout 2 Colonnes (20 min)**
```
1. Container 1140px
2. 2 colonnes : 60% / 40%
3. Espacement 40px entre colonnes
```

#### **C. Colonne Détails Trajet (35 min)**
```
1. Card 680px :
   - Titre "Paris → Lyon"
   - Infos trajet (date, heure, prix, places)
   - Point départ/arrivée
   - Description
2. Card Conducteur :
   - Avatar + nom + note
   - Stats (trajets effectués)
   - Description
```

#### **D. Colonne Formulaire (25 min)**
```
1. Card 400px :
   - Titre "Réserver votre place"
   - Select nombre places
   - Infos crédits
   - Calcul coût
   - Textarea message
   - Bouton "Confirmer"
   - Infos pratiques
```

---

## 📱 **ÉTAPE 3 : WIREFRAMES MOBILE (Page 2)**

### **3.1 - Page d'Accueil Mobile (60 min)**

#### **A. Header Mobile (10 min)**
```
1. Rectangle 390x60px
2. Menu hamburger (3 lignes) à gauche
3. Logo "EcoRide" centré
4. Icône notification à droite
```

#### **B. Hero Section (15 min)**
```
1. Rectangle 390x200px
2. Texte overlay :
   - "Voyagez écologique"
   - "Trouvez votre covoiturage"
```

#### **C. Recherche Mobile (20 min)**
```
1. Container 358px (margins 16px)
2. 3 inputs stacked :
   - Départ (pleine largeur)
   - Arrivée (pleine largeur)
   - Date + Bouton recherche (même ligne)
```

#### **D. Cards Trajets (15 min)**
```
1. 3 cards 358x120px
2. Chaque card :
   - Trajet + heure
   - Prix + places
   - Conducteur + note
   - Bouton "Réserver"
```

### **3.2 - Dashboard Mobile (60 min)**

#### **A. Header avec Notifications (10 min)**
```
1. Header 390x60px
2. Menu + logo + badge notification (3)
```

#### **B. Stats Grid 2x2 (15 min)**
```
1. 4 cards 170x80px
2. Grille 2x2 avec espacement 10px
3. Icône + nombre + label dans chaque
```

#### **C. Menu Actions (20 min)**
```
1. Liste verticale 358px
2. 5 items avec :
   - Icône + texte + badge
   - Flèche droite
   - Espacement 16px entre items
```

#### **D. Bottom Navigation (15 min)**
```
1. Rectangle 390x80px en bas
2. 4 onglets avec icônes :
   - Accueil | Recherche | Profil | Plus
```

### **3.3 - Recherche Mobile (60 min)**

#### **A. Search Header (10 min)**
```
1. Header 390x60px
2. Flèche retour + "Recherche" + icône recherche
```

#### **B. Critères + Filtres (20 min)**
```
1. Zone critères (Départ, Arrivée, Date)
2. Scroll horizontal des filtres :
   - Pills : Tous | Aujourd'hui | Prix | Places
```

#### **C. Résultats Cards (20 min)**
```
1. 3 cards résultats 358x140px
2. Chaque card :
   - Heure + trajet + durée
   - Conducteur + note
   - Prix + places
   - Boutons Détails/Réserver
```

#### **D. Mini Carte (10 min)**
```
1. Rectangle 358x200px
2. Placeholder carte avec itinéraire
3. Toggle Liste/Carte
```

---

## 🎨 **ÉTAPE 4 : MOCKUPS DESIGN (Pages 3-4)**

### **4.1 - Préparation Design System**

#### **A. Créer les Couleurs**
```
1. Panel "Fill" → Créer style :
   - Vert-Principal: #28a745
   - Vert-Hover: #218838
   - Gris-Clair: #f8f9fa
   - Gris-Texte: #6c757d
```

#### **B. Créer les Typographies**
```
1. Panel "Text" → Créer styles :
   - H1: Inter 40px Bold
   - H2: Inter 32px Semibold  
   - Body: Inter 16px Regular
   - Small: Inter 14px Regular
```

#### **C. Créer les Composants**
```
1. Bouton Principal : 
   - Fond #28a745, texte blanc
   - Border-radius 8px, padding 12x24px

2. Card :
   - Fond blanc, border #dee2e6
   - Border-radius 8px, shadow

3. Input :
   - Border #ced4da, focus #80bdff
   - Border-radius 6px, padding 12px
```

### **4.2 - Application du Design**

#### **A. Pour chaque Wireframe Desktop :**
```
1. Dupliquer le wireframe (Ctrl+D)
2. Appliquer les couleurs :
   - Header : fond vert #28a745
   - Textes : gris foncé #343a40
   - Backgrounds : blanc/gris clair
3. Ajouter les typographies :
   - Titres → Style H1/H2
   - Textes → Style Body
4. Styliser les composants :
   - Rectangles → Cards avec ombre
   - Boutons → Style bouton principal
   - Inputs → Style formulaire
5. Ajouter icônes :
   - Bootstrap Icons ou similaires
   - Taille 20-24px, couleur héritée
```

#### **B. Pour chaque Wireframe Mobile :**
```
1. Même processus mais adapter :
   - Touch targets 44px minimum
   - Textes plus grands (18px minimum)
   - Espacement plus généreux
   - Boutons pleine largeur
```

---

## 📤 **ÉTAPE 5 : EXPORT ET FINALISATION**

### **5.1 - Préparer l'Export**

#### **A. Nommer les Frames**
```
Wireframes Desktop:
- desktop_accueil_wireframe
- desktop_dashboard_wireframe  
- desktop_reservation_wireframe

Wireframes Mobile:
- mobile_accueil_wireframe
- mobile_dashboard_wireframe
- mobile_recherche_wireframe

Mockups Desktop:
- desktop_accueil_mockup
- desktop_dashboard_mockup
- desktop_reservation_mockup

Mockups Mobile:
- mobile_accueil_mockup
- mobile_dashboard_mockup
- mobile_recherche_mockup
```

#### **B. Export PNG**
```
1. Sélectionner tous les frames d'une page
2. Panel droit → Export
3. Format : PNG, 2x (haute résolution)
4. Cliquer "Export"
5. Répéter pour chaque page
```

### **5.2 - Créer les PDFs**

#### **A. Structure PDF Wireframes**
```
Page 1: Titre "EcoRide - Wireframes"
Page 2: Desktop - Page d'Accueil
Page 3: Desktop - Dashboard  
Page 4: Desktop - Réservation
Page 5: Mobile - Page d'Accueil
Page 6: Mobile - Dashboard
Page 7: Mobile - Recherche
Page 8: Notes méthodologiques
```

#### **B. Structure PDF Mockups**
```
Page 1: Titre "EcoRide - Design Final"
Page 2: Charte graphique appliquée
Pages 3-8: Même ordre que wireframes
Page 9: Responsive design - comparaisons
Page 10: Composants et patterns utilisés
```

---

## 💡 **CONSEILS FIGMA PRATIQUES**

### **Raccourcis Essentiels**
```
R : Rectangle
T : Texte
O : Ellipse
L : Line
V : Move (sélection)
K : Scale
Ctrl+G : Grouper
Ctrl+Shift+G : Dégrouper
Ctrl+D : Dupliquer
Alt+Drag : Copier en glissant
Shift+Drag : Contraindre proportions
```

### **Bonnes Pratiques**
```
1. Nommer tous les layers
2. Grouper les éléments logiques
3. Utiliser les grilles (Layout Grid)
4. Créer des composants réutilisables
5. Organiser en frames cohérents
6. Utiliser les styles pour couleurs/typo
```

### **Organisation Panels**
```
Gauche : Pages et layers
Droite : Propriétés et export
Haut : Outils et composants
Centre : Canvas de travail
```

---

## 🎯 **CHECKLIST FINAL**

### **Avant Export :**
- [ ] Toutes les frames nommées correctement
- [ ] Cohérence visuelle entre wireframes/mockups
- [ ] Respect de la charte graphique
- [ ] Textes réalistes (pas de Lorem Ipsum)
- [ ] Alignements et espacements corrects
- [ ] Responsive adapté mobile

### **Exports :**
- [ ] 6 PNG wireframes haute résolution
- [ ] 6 PNG mockups haute résolution  
- [ ] 1 PDF wireframes complet
- [ ] 1 PDF mockups complet
- [ ] Fichiers organisés dans dossiers appropriés

---

**🎉 Avec ce tutoriel, vous avez tout pour créer des maquettes professionnelles dans Figma !**

*Prêt à commencer ? Je peux vous guider sur une maquette spécifique si vous voulez !*
