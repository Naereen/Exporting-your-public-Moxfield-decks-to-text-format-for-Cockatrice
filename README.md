# Exportateur Moxfield → Cockatrice (expérience ratée !)

> Exporte tous les decks publics d'un utilisateur [Moxfield](https://www.moxfield.com/) en fichiers texte compatibles avec [Cockatrice](https://cockatrice.github.io/).

Cela n'a pas fonctionné.

----

## 🎯 Fonctionnalités

- Récupère automatiquement **tous les decks publics** d'un utilisateur Moxfield via l'API publique.
- Convertit chaque deck au **format texte Cockatrice** (`.txt`).
- Regroupe tous les fichiers dans une **archive ZIP** téléchargeable en un clic.
- Interface web simple, sans installation requise.

## 🚀 Utilisation

1. Ouvrir le fichier [`index.html`](index.html) dans un navigateur web moderne.
2. Entrer le nom d'utilisateur Moxfield souhaité.
3. Cliquer sur **« Lancer l'exportation »**.
4. Patienter pendant le téléchargement des decks (un délai est appliqué entre chaque requête pour respecter les limites de l'API).
5. Une archive `Moxfield_Backups_<utilisateur>.zip` est automatiquement téléchargée.

## 📄 Format de sortie

Chaque deck est exporté dans un fichier `<Nom du deck>.txt` au format suivant :

```
4 Lightning Bolt
2 Mountain
SB: 2 Tormod's Crypt
SB: 1 Général Tazri
```

- Les cartes du **mainboard** sont listées directement (`quantité NomDeLaCarte`).
- Les cartes du **sideboard** et les **commandants** sont préfixées par `SB: `.

## 🛠️ Technologies

- HTML / CSS (via [Tailwind CSS CDN](https://tailwindcss.com/))
- JavaScript (Vanilla)
- [JSZip](https://stuk.github.io/jszip/) pour la génération de l'archive ZIP
- API publique Moxfield (`https://api.moxfield.com/v2`)

## ⚠️ Limitations

- Seuls les **decks publics** sont accessibles via l'API.
- Un délai de 400–500 ms est appliqué entre chaque requête pour éviter le blocage par l'API (HTTP 429).
- L'outil fonctionne entièrement côté client (navigateur) : aucune donnée n'est envoyée à un serveur tiers.

## 📜 Licence

[MIT](LICENSE) © 2026 Lilian Besson
