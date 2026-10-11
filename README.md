# Cyna

Cyna est une application de vente et de gestion de services SaaS. Elle comprend une API Symfony et une interface React.

## Structure du dépôt

- `backend/` : API Symfony, base de données, paiements, factures et administration.
- `frontend/` : interface React avec Vite.

## Prérequis

- PHP 8.4 ou plus récent et Composer
- Node.js et npm
- Docker, pour démarrer la base MySQL fournie

## Configuration

Je garde les fichiers `.env.local` sur ma machine : ils ne doivent pas être ajoutés à Git. Je renseigne les paramètres nécessaires dans `backend/.env.local` et `frontend/.env.local`.

Pour le backend, je configure notamment `DATABASE_URL`, `APP_SECRET`, `MAILER_DSN` (SMTP Brevo) et `STRIPE_SECRET_KEY`. Pour le frontend, je renseigne `VITE_API_URL`, `VITE_PAYMENT_ENV` et les clés publiques de paiement.

Les variables `VITE_*` sont visibles dans le navigateur : je n'y place jamais de secret privé. Les clés Stripe `pk_*` et les identifiants client PayPal sont publics ; les clés Stripe `sk_*`, les mots de passe et `MAILER_DSN` doivent rester privés. Si un secret a été poussé sur GitHub, je le révoque ou le renouvelle : supprimer le fichier ne l'efface pas de l'historique.

## Lancer le projet

Dans un terminal, je prépare le backend et sa base de données :

```powershell
cd backend
composer install
docker compose -f compose.yaml up -d database
php bin/console doctrine:migrations:migrate
```

Dans un second terminal, je lance l'interface :

```powershell
cd frontend
npm ci
npm run dev
```

Je vérifie que `VITE_API_URL` pointe vers l'API et que la configuration CORS du backend autorise l'origine de Vite.

## Factures

Les factures sont enregistrées dans `backend/var/invoices`, hors du répertoire public. Ce dossier est ignoré par Git, car les factures contiennent des données personnelles.

## Vérifications

Depuis `frontend/`, je vérifie l'interface avec :

```powershell
npm run lint
npm run build
```

Depuis `backend/`, je vérifie le conteneur Symfony avec :

```powershell
php bin/console lint:container
```
