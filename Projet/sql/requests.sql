------------------------------
-- Utilisateurs
------------------------------

-- Création ou mise à jour d'un utilisateur
INSERT INTO 
utilisateur (pseudo, adresseemail, descriptionutilisateur, siteinternet, estadministrateur, motdepasse)
VALUES ('username', 'mail@address.com', 'Description', 'Website.com', true, 'Password')
ON CONFLICT (pseudo) DO UPDATE
SET adresseemail = 'mail@address.com', descriptionutilisateur = 'Description', siteinternet = 'Website.com', estadministrateur = true, motdepasse = 'Password';

-- Sélectionne un utilisateur avec son nom et son mot de passe (authentification)
SELECT * 
FROM utilisateur 
WHERE pseudo = :username 
AND motdepasse = :password;

-- Mise à jour des privilièges d'un utilisateur
UPDATE utilisateur
SET estadministrateur = true
WHERE pseudo = 'username';

-- Vérifie que l'utilisateur existe déjà
SELECT COUNT(1) 
FROM utilisateur 
WHERE pseudo = 'username'
OR adresseemail = 'mail@address.com';

-- Récupère les informations d'un utilisateur à partir de son nom d'utilisateur
SELECT * 
FROM utilisateur 
WHERE pseudo = 'username';

-- Supprime un utilisateur 
DELETE FROM utilisateur 
WHERE pseudo = 'username';

-- Récupère l'équipe d'un utilisateur
SELECT E.* FROM equipe E
LEFT OUTER JOIN membre_equipe ME on E.nom = ME.equ_nom
WHERE ME.uti_pseudo = 'username'
OR E.crt_pseudo = 'username';

------------------------------
-- Equipe
------------------------------

-- Sélectionne tous les événements auxquels participe une équipe
SELECT * FROM evenement E
INNER JOIN evenement_equipe EE 
	ON E.id = EE.eve_id
WHERE EE.nom = 'eventName';

-- Création d'une nouvelle équipe ou mise à jour
INSERT INTO equipe (nom, motdepasse, typeequipe, crt_pseudo)
VALUES ('teamName', 'password', 'Pro', 'creatorName')
ON CONFLICT (nom) DO UPDATE
SET motdepasse = 'password', typeequipe = 'Pro', crt_pseudo = 'creatorName';

-- Ajoute un membre à une équipe
INSERT INTO membre_equipe (uti_pseudo, equ_nom)
VALUES ('username', 'teamName')
ON CONFLICT (uti_pseudo, equ_nom) DO NOTHING;

-- Supprime un membre de l'équipe
DELETE FROM membre_equipe
WHERE uti_pseudo = 'username' AND equ_nom = 'teamName';

-- Sélectionne une équipe à partir de son nom
SELECT * 
FROM equipe 
WHERE nom = 'teamName';

-- Vérifie si une équipe existe
SELECT COUNT(*) 
FROM Equipe 
WHERE nom = 'teamName';

-- Sélectionne les utilisiateurs d'une équipe
SELECT U.pseudo, U.adresseemail, U.descriptionutilisateur, U.siteinternet, U.estadministrateur, U.motdepasse
FROM membre_equipe ME
INNER JOIN utilisateur U ON ME.uti_pseudo = U.pseudo
WHERE ME.equ_nom = 'teamName';

-- Supprime une équipe
DELETE FROM equipe 
WHERE nom = 'teamName';

-- Sélectionne tous les événements auxquels participe une équipe
SELECT E.*
FROM evenement E
INNER JOIN evenement_equipe EE ON E.id = EE.eve_id
WHERE EE.nom = 'teamName';

-- Vérifie si une équipe participe à un événement
SELECT COUNT(*) FROM evenement_equipe
WHERE nom = 'teamName' AND eve_id = 1;

-- Récupère tous les types d'équipe
SELECT * 
FROM type_equipe 
ORDER BY nomtype;

-- Ajoute un type équipe 
INSERT INTO type_equipe (nomtype) 
VALUES ('teamType') ON CONFLICT DO NOTHING;

-- Supprime un type d'équipe
DELETE FROM type_equipe 
WHERE nomtype = 'teamType';

------------------------------
-- Evénements
------------------------------

-- Création d'un événement
INSERT INTO evenement (nom, estenligne, datecreation, datefin, estjeopardy, crt_pseudo)
VALUES ('eventName', true, '2023-01-22', '2023-01-23', true, 'creatorName');

-- Récupère toutes les informations d'un événement
SELECT * 
FROM Evenement 
WHERE id = 1;

-- Récupère toutes les informations des événements non-terminés
SELECT E.*, CRT.* FROM evenement E 
INNER JOIN utilisateur CRT on E.crt_pseudo = CRT.pseudo
WHERE datefin > NOW()

-- Récupérer toutes les salles attribuées à un événement
SELECT * 
FROM salle_evenement 
WHERE eve_id = 1;

-- Récupération des challenges Jeopardy
SELECT *
FROM challenge_jeopardy
INNER JOIN challenge C on challenge_jeopardy.challengeid = C.challengeid
WHERE C.eve_id = 1;

-- Récupération des challenges Attaque-Défense
SELECT *
FROM challenge_attaque_defense
INNER JOIN challenge C on challenge_attaque_defense.challengeid = C.challengeid
WHERE C.eve_id = 1;

-- Vérifie qu'un événement existe
SELECT COUNT(1) 
FROM evenement 
WHERE id = 1;

-- Récupère les points de toutes les équipes ayant participées à un événement donné
SELECT * FROM points_par_equipe_par_evenement P
WHERE P.id = 1
ORDER BY P.sum DESC;

-- Récupère les informations d'une équipe pour un événement donné
SELECT * FROM points_par_equipe_par_evenement P
WHERE P.id = 1
AND P.equ_nom = 'teams1';

-- Récupère les points de toutes les équipes ayant participées pour tous les événements
SELECT equ_nom, SUM(sum) FROM points_par_equipe_par_evenement P
GROUP BY P.equ_nom
ORDER BY SUM(sum) DESC;

-- Vérifie qu'une équipe participe à l'événement
SELECT COUNT(1) FROM evenement_equipe
WHERE eve_id = 1 AND nom = 'nomEquipe';

-- Assigne une salle à un événement
INSERT INTO salle_evenement(nosalle, etage, eve_id)
VALUES (1, 'A', 1;)
ON CONFLICT DO NOTHING;

-- Inscrit une équipe à un événement
INSERT INTO evenement_equipe (eve_id, nom) 
VALUES (1, 'teamName');

-- Compte tous les serveurs d'un événement attaque-défense
SELECT COUNT(*)
FROM evenement E
INNER JOIN challenge C ON E.id = C.eve_id
INNER JOIN challenge_attaque_defense CAD ON C.challengeid = CAD.challengeid
WHERE E.id = 1
GROUP BY E.id;

-- Compte tous les serveurs attaqués par une équipe donnée pour un événement donné
SELECT COUNT(*)
FROM equipe_challengeattaquedefense ECAD
INNER JOIN challenge_attaque_defense CAD ON ECAD.attdef_challengeid = CAD.challengeid
INNER JOIN challenge C ON CAD.challengeid = C.challengeid
INNER JOIN evenement E ON C.eve_id = E.id
WHERE E.id = 1 AND ECAD.equ_nom = 'teamName';

------------------------------
-- Challenges
------------------------------

-- Création d'un challenge
INSERT INTO Challenge (nom, eve_id)
VALUES ('challengeName', 1)
ON CONFLICT DO NOTHING;
 
-- Récupérer un challenge à partir d'un nom
SELECT challengeId 
FROM Challenge 
WHERE nom = 'challengeName';

-- Vérifie qu'un challenge existe
SELECT COUNT(1) 
FROM challenge C 
WHERE C.challengeid = 1;

------------------------------
-- Challenges attaque-défense
------------------------------

-- Création d'un challenge
INSERT 
INTO Challenge_Attaque_Defense (challengeid, flag, serveurid)
VALUES (1, 'flag', 1) ON CONFLICT DO NOTHING;

-- Récupération du serveur d'un challenge
SELECT * 
FROM serveur 
WHERE id = 1;

-- Vérifie qu'une équipe donnée ait réalisée le challenge
SELECT COUNT(1) 
FROM equipe_challengeattaquedefense EC 
INNER JOIN challenge_attaque_defense CAD 
	ON CAD.challengeid = EC.attdef_challengeid
WHERE EC.attdef_challengeid = 1
AND EC.equ_nom = 'teamName';

-- Vérifie que un flag donné pour un challengé donné soit correct
SELECT COUNT(1) 
FROM challenge_attaque_defense CAD
WHERE CAD.challengeid = 1
AND CAD.flag = 'flag';

------------------------------
-- Challenges Jeopardy
------------------------------

-- Création d'un challenge Jeopardy
INSERT INTO Challenge_Jeopardy (challengeId, descriptionJeopardy, typeJeopardy, auteur, dateCreation, dateFin)
VALUES (1, 'Description', true, 'author', '2023-01-21', '2023-01-22') 
ON CONFLICT DO NOTHING;

-- Récupère toutes les informations d'un challenge Jeopardy
SELECT * FROM challenge_jeopardy CJ 
INNER JOIN challenge C on C.challengeid = CJ.challengeid
WHERE CJ.challengeid = 1;

-- Vérifie qu'un challenge existe
SELECT COUNT(1) 
FROM challenge_jeopardy C 
WHERE C.challengeid = 1;

-- Sélectionne les étapes d'un challenge
SELECT E.* FROM etape E 
INNER JOIN challenge_jeopardy CJ ON CJ.challengeid = E.jeo_challengeid
WHERE E.jeo_challengeid = 1;

-- Récupère tous les points par équipe pour un challenge donnée
SELECT * FROM points_par_equipe_par_challenge P
WHERE P.challengeId = 1
ORDER BY P.sum DESC;

-- Récupère tous les points de toutes les étapes d'un challenge pour une équipe
SELECT SUM 
FROM points_par_equipe_par_challenge 
WHERE challengeid = 1
AND equ_nom = 'teamName';

-- Vérifie si une équipe a fait toutes les étapes d'un challenge
SELECT COUNT(*) 
FROM equipe_a_fait_jeo_challenge 
WHERE equ_nom = 'teamName'
AND challengeid = 1;

-- Récupère tous les types des challenges Jeopardy
SELECT * 
FROM type_challenge_jeopardy 
ORDER BY nomtype;

-- Création d'un nouveau type de challenge Jeopardy
INSERT INTO type_challenge_jeopardy (nomtype) 
VALUES ('newType') ON CONFLICT DO NOTHING;

-- Supprime un type de challenge
DELETE FROM type_challenge_jeopardy 
WHERE nomtype = 'typeToDelete';

------------------------------
-- Etapes
------------------------------

-- Création d'une étape
INSERT INTO etape (descriptionetape, nbpoints, difficulte, flag, nom, jeo_challengeid)
VALUES ('description', 100, 1, 'flag', 'stepName', 1) ON CONFLICT DO NOTHING;

-- Vérifie qu'une équipe a validé une étape
SELECT COUNT(1) FROM equipe_etape EE 
INNER JOIN etape E ON EE.eta_nom = E.nom
WHERE EE.eta_nom = 'stepName' AND EE.equ_nom = 'teamName';

-- Vérifie que le flag rentré correspond bien au flag attendu
SELECT COUNT(1) 
FROM etape E 
WHERE E.nom = 'stepName' 
AND E.flag = 'flag';

-- Inscrit une équipe comme ayant réalisé une étape
INSERT INTO equipe_etape (equ_nom, eta_nom) 
VALUES (:equipe, :etape);

------------------------------
-- Writeups
------------------------------

-- Création des writeup
INSERT INTO 
writeup (titre, contenu, pseudo, challengeid)
VALUES ('Title', 'Writeups', 'Author', 1);

-- Supprime un writeup
DELETE FROM writeup 
WHERE id = 1;

-- Sélectionne tous les writeups d'un utilisateur pour un challenge donné
SELECT * 
FROM writeup 
WHERE challengeid = 1
AND pseudo = 'username';

-- Sélectionne un writeup selon un ID et un utilisateur
SELECT * 
FROM writeup 
WHERE id = 1 
AND pseudo = 'username';

-- Sélectionne tous les writeups d'un utilisateur
SELECT * 
FROM writeup 
WHERE pseudo = 'username';

-- Sélectionne tous les writeups d'un utilisateur pour des challenges terminés
SELECT *
FROM writeup W
INNER JOIN challenge_jeopardy CJ on W.challengeid = CJ.challengeid
WHERE pseudo = 'username'
AND CJ.datefin < NOW();

------------------------------
-- Salles
------------------------------

-- Création d'une salle
INSERT INTO salle (numerosalle, etage)
VALUES (1, 'A');

-- Récupère toutes les salles
SELECT * 
FROM salle;

------------------------------
-- Serveurs
------------------------------

-- Création d'un nouveau serveur
INSERT INTO serveur (adresselocale, numerosalle, etage, emailmainteneur)
VALUES ('123.45.67.89', 2, 'A', 'Maintainer');

-- Sélectionne tous les serveurs
SELECT * 
FROM serveur;

------------------------------
-- Statistiques
------------------------------


-- Top 3 des événements (actifs) avec le plus gros taux de participation
SELECT EE.eve_id, E.nom, COUNT(*) AS nb_participants
FROM evenement_equipe EE
INNER JOIN evenement E on EE.eve_id = E.id
WHERE E.datefin > NOW()
GROUP BY EE.eve_id, E.nom
ORDER BY nb_participants DESC
LIMIT 3;

-- Sélectionne le nombre moyen de membres par équipe
SELECT AVG(nb_membres)
FROM (
	SELECT COUNT(*) AS nb_membres FROM membre_equipe
	GROUP BY equ_nom
) AS nb_membres_par_equipe;

-- Sélectionne le nombre moyen de participants à un événement
SELECT AVG(nb_participants)
            FROM (
                SELECT COUNT(*) AS nb_participants
                FROM (
                    SELECT EE.eve_id AS eve, ME.uti_pseudo AS pseudo
                    FROM evenement_equipe EE
                    INNER JOIN membre_equipe ME ON EE.nom = ME.equ_nom
                    UNION
                    SELECT EE2.eve_id AS eve, E.crt_pseudo AS pseudo
                    FROM evenement_equipe EE2
                    INNER JOIN equipe E ON EE2.nom = E.nom
                     ) as participants
                GROUP BY eve
            ) as nb_participants_par_evenement;

-- Sélectionne le temps moyen pour compléter une étape
SELECT AVG(temps)
FROM (
	SELECT EE.date_realisation - E.date_creation AS temps
	FROM equipe_etape EE
	INNER JOIN etape E ON EE.eta_nom = E.nom) as dates;

-- Sélectionne le temps moyen pour compléter un challenge
SELECT AVG(temps)
FROM (
    SELECT EAJC.max - CJ.datecreation AS temps
    FROM equipe_a_fait_jeo_challenge EAJC
    INNER JOIN challenge_jeopardy CJ on EAJC.challengeid = CJ.challengeid
        ) as dates;

-- Sélectionne le nombre de points moyen par équipe par événement
SELECT AVG(nb_points)
FROM (
	  SELECT AVG(nbpoints) as nb_points
	  FROM evenement_equipe EE
			   INNER JOIN equipe_etape EE2 ON EE.nom = EE2.equ_nom
			   INNER JOIN etape E ON EE2.eta_nom = E.nom
	  GROUP BY EE.nom, EE.eve_id
	  ) as moyenne_par_equipe_par_evenement;
			  
			  
-- Nombre de mots moyens dans un writeup	  
SELECT AVG(ARRAY_LENGTH(REGEXP_SPLIT_TO_ARRAY(W.contenu, '\s'),1)) FROM writeup W;

-- Sélectionne le taux de participation moyen aux événements
SELECT (AVG(nb_equipes_par_evenement) / (SELECT COUNT(*) FROM equipe)) * 100 AS "taux_moyen_participation (%)"
        FROM (
            SELECT COUNT(*) AS nb_equipes_par_evenement
            FROM evenement_equipe EE
            GROUP BY EE.eve_id
             ) as nb_equipes_par_evenement


-- Sélectionne le type de challenge le plus réalisé
SELECT CJ.typejeopardy
FROM challenge_jeopardy CJ
INNER JOIN equipe_a_fait_jeo_challenge EAJC ON CJ.challengeid = EAJC.challengeid
GROUP BY CJ.typejeopardy
HAVING COUNT(*) = (
    SELECT MAX(nb)
    FROM (
        SELECT COUNT(*) AS nb
        FROM challenge_jeopardy CJ2
        INNER JOIN equipe_a_fait_jeo_challenge EAJC2 ON CJ2.challengeid = EAJC2.challengeid
        GROUP BY CJ2.typejeopardy
         ) as nb_par_type
     )
LIMIT 1;

-- Sélectionne le type de challenge réalisé le plus rapidement
SELECT CJ.typejeopardy
FROM challenge_jeopardy CJ
INNER JOIN equipe_a_fait_jeo_challenge EAJC ON CJ.challengeid = EAJC.challengeid
WHERE (EAJC.max - CJ.datecreation) = (SELECT MIN(temps)
                                      FROM (
                                      SELECT EAJC2.max - CJ2.datecreation AS temps
                                      FROM equipe_a_fait_jeo_challenge EAJC2
                                      INNER JOIN challenge_jeopardy CJ2 on EAJC2.challengeid = CJ2.challengeid
                                      ) as dates);