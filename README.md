# Projet TOUBILIB
## CONTRIBUTEURS :
### TULINE LEVEQUE (tuline-leveque) & SOFIEN CHERCHARI (bocchiarati)

## ACCEDER AU PROJET :
### 1 - Lancer la commande docker "docker compose up"
### 2 - Aller sur le lien "127.0.0.1:7080" (en Local) pour acceder à l'api (les differentes routes y sont renseignees)
### 3 - Acceder a la BDD grace au port "8080"

## Liste des routes existantes :

## GET
#### /praticiens => acceder a la liste des praticiens (QueryParams : ville, scpecialite)
#### /praticiens/{id_prat} => detail d'un praticien en renseignant son id
#### /praticiens/{id_prat}/rdvs => obtenir les rendez-vous d'un praticien (QueryParams : date_debut, date_fin)
#### /praticiens/{id_prat}/rdvs/{rdv_id} => acceder au detail d'un rendez-vous (un parametre de debut 'date_debut' et de fin 'date_fin' peuvent etre ajoutes en queryParams)
#### /patient/{id_pat}/rdvs => obtenir les rendez-vous d'un patient (QueryParams : date_debut, date_fin)

## POST
#### /signin => se connecter : Body Params requis : "email", "password" 
#### /praticiens/{id_prat}/rdvs => creer un rendez-vous (exemple : /praticiens/4305f5e9-be5a-4ccf-8792-7e07d7017363/rdvs?duree=30&date_heure_debut=2025-10-07 15:00:00&date_heure_fin=2025-10-07 15:30:00&id_pat=d975aca7-50c5-3d16-b211-cf7d302cba50)
#### /praticiens/{id_pat}/indisponibilite => Ajouter une indisponibilité pour un praticien, Body params requis : "date_debut", "date_fin"

## PATCH
#### /praticiens/{id_pat}/rdvs/{id_rdv} => Définir un RDV comme honorer ou non : Body params requis : "statut" (boolean true => honorer, false => non honorer) 

## DELETE
#### /praticiens/{id_prat}/rdvs/{id_rdv} => annuler un rendez-vous


# Mailer :
## Test : 
Vous pouvez tester le mailer avec la commande 
```bash
docker compose run --rm api.rdvs composer mail-test
```
Le detail de la commande mail-test est disponible dans la section "scripts" du fichier `api-rdv/composer.json`