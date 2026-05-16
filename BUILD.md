# Build Lafia - Guide

## Prerequis (une seule fois)

1. **Android Studio** installe : `sudo snap install android-studio --classic`
2. **SDK Android** telecharge (via Android Studio > SDK Manager)
3. **Variables d'environnement** dans `~/.bashrc` :
```bash
export ANDROID_HOME="$HOME/Android/Sdk"
export JAVA_HOME="/snap/android-studio/209/jbr"
export PATH="$PATH:$JAVA_HOME/bin:$ANDROID_HOME/platform-tools"
```
4. **NativePHP Mobile** installe : `composer require nativephp/mobile`
5. **Ressources NativePHP** : `php artisan native:install`

## Builder l'APK

Depuis le dossier `lafia-app/` :

```bash
php artisan native:build android
```

Ou si ca ne marche pas, le build Gradle direct :

```bash
cd nativephp/android && ./gradlew assembleDebug
```

Le build prend ~5-20 min selon le PC.

## Ou trouver l'APK

Apres le build, l'APK est toujours ici :

```
lafia-app/nativephp/android/app/build/outputs/apk/debug/app-debug.apk
```

Taille : ~83 Mo

## Envoyer l'APK

- **WhatsApp** : envoyer comme document (pas comme image), limite 100 Mo
- **Email** : en piece jointe (Gmail limite 25 Mo, utiliser Google Drive)
- **Google Drive / Dropbox** : uploader puis partager le lien
- **Telegram** : envoyer comme fichier, limite 2 Go
- **USB** : copier sur cle USB

## Installer sur un telephone Android

1. Recevoir/telecharger l'APK sur le telephone
2. Android demande "Autoriser les sources inconnues" > accepter
3. Appuyer sur l'APK > Installer
4. Ouvrir Lafia

---

# Build iOS

## Prerequis (une seule fois)

1. **Un Mac** avec macOS — Xcode ne tourne que sur Mac
2. **Xcode** installe depuis le Mac App Store (gratuit, ~12 Go)
3. **Xcode Command Line Tools** : `xcode-select --install`
4. **Compte Apple Developer** (gratuit pour tester sur son propre iPhone, 99$/an pour publier sur l'App Store)
5. **NativePHP Mobile** installe : `composer require nativephp/mobile`
6. **Ressources NativePHP** : `php artisan native:install`

## Builder l'IPA

Depuis le dossier `lafia-app/` :

```bash
php artisan native:build ios
```

Ou ouvrir le projet Xcode directement :

```bash
php artisan native:open ios
```

Puis dans Xcode : Product > Build (Cmd+B)

## Ou trouver le build iOS

```
lafia-app/nativephp/ios/build/
```

## Tester sur un iPhone (sans publier)

1. Brancher l'iPhone en USB au Mac
2. Dans Xcode : selectionner ton iPhone comme cible (en haut)
3. Product > Run (Cmd+R)
4. L'app s'installe directement sur le telephone
5. Sur l'iPhone : Reglages > General > Gestion des appareils > faire confiance au developpeur

**Note** : sans compte Apple Developer payant (99$/an), l'app expire apres 7 jours. Il faut re-builder.

## Publier sur l'App Store

1. Compte Apple Developer payant (99$/an) sur developer.apple.com
2. Configurer les certificats et profils dans Xcode (Signing & Capabilities)
3. `php artisan native:package ios`
4. Soumettre via Xcode > Product > Archive > Distribute App
5. Attendre la review Apple (~24-48h)

## Pas de Mac ?

- **Pas possible de builder iOS sans Mac** — c'est une restriction Apple
- Alternative : louer un Mac dans le cloud (MacStadium, AWS EC2 Mac) ~30-50$/mois
- Ou emprunter un Mac le temps du build

---

# Notes generales

- Pas besoin d'emulateur ni de telephone branche pour builder
- Le PC/Mac fait le build, le telephone recoit juste le fichier final
- APK (Android) = debug, pour tester. `native:package` pour publier
- IPA (iOS) = necessite un Mac + Xcode obligatoirement
- Les deux builds partagent le meme code Laravel/Livewire
