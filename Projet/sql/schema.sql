-------------------------
--- Création du schéma
-- Auteurs: Rayane ANNEN, Felix BREVAL et Alexis MARTINS
-------------------------


DROP SCHEMA IF EXISTS ctf CASCADE;
CREATE SCHEMA ctf;

SET SEARCH_PATH TO ctf;

DROP TABLE IF EXISTS Utilisateur CASCADE;
CREATE TABLE Utilisateur
(
    pseudo                 VARCHAR(32),
    adresseEmail           VARCHAR(255) UNIQUE,
    motDePasse             VARCHAR(70) NOT NULL,
    estAdministrateur      BOOLEAN     NOT NULL,
    descriptionUtilisateur TEXT,
    siteInternet           VARCHAR(255),
    PRIMARY KEY (pseudo)
);

DROP TABLE IF EXISTS Evenement CASCADE;
CREATE TABLE Evenement
(
    id           SERIAL,
    nom          VARCHAR(32) NOT NULL,
    estEnLigne   BOOLEAN     NOT NULL,
    dateCreation DATE        NOT NULL,
    dateFin      DATE        NOT NULL,
    estJeopardy  BOOLEAN     NOT NULL,
    crt_pseudo   VARCHAR(32),
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS Challenge CASCADE;
CREATE TABLE Challenge
(
    challengeId SERIAL,
    nom         VARCHAR(32) NOT NULL,
    eve_id      INT         NOT NULL,
    PRIMARY KEY (ChallengeId)
);

DROP TABLE IF EXISTS Writeup CASCADE;
CREATE TABLE Writeup
(
    id          SERIAL,
    titre       VARCHAR(32) NOT NULL,
    contenu     TEXT        NOT NULL,
    pseudo      VARCHAR(32) NOT NULL,
    challengeId INT         NOT NULL,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS Type_equipe;
CREATE TABLE IF NOT EXISTS Type_equipe
(
    nomType VARCHAR(32) PRIMARY KEY
);

DROP TABLE IF EXISTS Equipe CASCADE;
CREATE TABLE Equipe
(
    nom        VARCHAR(32),
    motDePasse VARCHAR(70) NOT NULL,
    typeEquipe VARCHAR(32) NOT NULL,
    crt_pseudo VARCHAR(32) NOT NULL,
    PRIMARY KEY (nom)
);

DROP TABLE IF EXISTS Membre_equipe CASCADE;
CREATE TABLE Membre_equipe
(
    equ_nom    VARCHAR(32) NOT NULL,
    uti_pseudo VARCHAR(32) NOT NULL,
    PRIMARY KEY (equ_nom, uti_pseudo)
);

DROP TABLE IF EXISTS Salle CASCADE;
CREATE TABLE Salle
(
    numeroSalle INT,
    etage       CHAR(1),
    PRIMARY KEY (numeroSalle, etage)
);

DROP TABLE IF EXISTS Salle_evenement CASCADE;
CREATE TABLE Salle_evenement
(
    noSalle INT     NOT NULL,
    etage   CHAR(1) NOT NULL,
    eve_id  INT     NOT NULL,
    PRIMARY KEY(etage, noSalle, eve_id)
);

DROP TABLE IF EXISTS Serveur CASCADE;
CREATE TABLE Serveur
(
    id              SERIAL,
    adresseLocale   VARCHAR(15)  NOT NULL,
    emailMainteneur VARCHAR(255) NOT NULL,
    numeroSalle     INT          NOT NULL,
    etage           CHAR(1)      NOT NULL,
    PRIMARY KEY (id)
);

DROP TABLE IF EXISTS Challenge_Jeopardy CASCADE;
CREATE TABLE Challenge_Jeopardy
(
    challengeId         SERIAL,
    descriptionJeopardy TEXT,
    typeJeopardy        VARCHAR(32) NOT NULL,
    auteur              VARCHAR(32),
    dateCreation        date        NOT NULL,
    dateFin             date        NOT NULL,
    PRIMARY KEY (challengeId)
);

DROP TABLE IF EXISTS Type_challenge_jeopardy;
CREATE TABLE Type_challenge_jeopardy
(
    nomType VARCHAR(32) PRIMARY KEY
);

DROP TABLE IF EXISTS Challenge_Attaque_Defense;
CREATE TABLE Challenge_Attaque_Defense
(
    challengeId SERIAL,
    flag        char(32) NOT NULL,
    serveurId   INT      NOT NULL,
    PRIMARY KEY (challengeId)
);

DROP TABLE IF EXISTS Etape CASCADE;
CREATE TABLE Etape
(
    nom              VARCHAR(32),
    descriptionEtape text        NOT NULL,
    nbPoints         INT         NOT NULL,
    difficulte       INT         NOT NULL,
    flag             VARCHAR(32) NOT NULL,
    jeo_challengeid  INT         NOT NULL,
    date_creation    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (nom, jeo_challengeid)
);

DROP TABLE IF EXISTS Evenement_equipe CASCADE;
CREATE TABLE Evenement_equipe
(
    eve_id INT,
    nom    VARCHAR(32),
    PRIMARY KEY (eve_id, nom)
);

DROP TABLE IF EXISTS Equipe_Etape CASCADE;
CREATE TABLE Equipe_Etape
(
    eta_nom             VARCHAR(32),
    equ_nom             VARCHAR(32),
    eta_jeo_challengeid INT,
    date_realisation    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (eta_nom, equ_nom)
);

DROP TABLE IF EXISTS Equipe_ChallengeAttaqueDefense CASCADE;
CREATE TABLE Equipe_ChallengeAttaqueDefense
(
    equ_nom            VARCHAR(32),
    attdef_challengeid INT,
    PRIMARY KEY (equ_nom, attdef_challengeid)
);

ALTER TABLE Evenement
    ADD CONSTRAINT
        fk_eve_uti_crt_pseudo FOREIGN KEY (crt_pseudo) REFERENCES Utilisateur (pseudo) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Challenge
    ADD CONSTRAINT
        fk_cha_eve_eve_id FOREIGN KEY (eve_id) REFERENCES Evenement (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Writeup
    ADD CONSTRAINT
        fk_write_uti_pseudo FOREIGN KEY (pseudo) REFERENCES Utilisateur (pseudo) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Writeup
    ADD CONSTRAINT
        fk_write_chall_challengeId FOREIGN KEY (challengeId) REFERENCES Challenge_jeopardy (challengeId) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Equipe
    ADD CONSTRAINT
        fk_equ_uti_crt_pseudo FOREIGN KEY (crt_pseudo) REFERENCES Utilisateur (pseudo) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Membre_equipe
    ADD CONSTRAINT
        fk_memequ_equ_equ_nom FOREIGN KEY (equ_nom) REFERENCES Equipe (nom) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Membre_equipe
    ADD CONSTRAINT
        fk_memequ_uti_uti_pseudo FOREIGN KEY (uti_pseudo) REFERENCES Utilisateur (pseudo) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Salle_evenement
    ADD CONSTRAINT
        fk_seve_sal_no_salle FOREIGN KEY (noSalle, etage) REFERENCES Salle (numeroSalle, etage) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Salle_evenement
    ADD CONSTRAINT
        fk_seve_eve_eve_id FOREIGN KEY (eve_id) REFERENCES Evenement (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Serveur
    ADD CONSTRAINT
        fk_ser_sal_numeroSalle FOREIGN KEY (numeroSalle, etage) REFERENCES Salle (numeroSalle, etage) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Challenge_Attaque_Defense
    ADD CONSTRAINT
        fk_challad_ser_serveurId FOREIGN KEY (serveurId) REFERENCES Serveur (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Etape
    ADD CONSTRAINT
        fk_eta_challj_jeo_challenge_id FOREIGN KEY (jeo_challengeid) REFERENCES Challenge_Jeopardy (challengeId) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Evenement_equipe
    ADD CONSTRAINT
        fk_eve_equ_eve_eve_id FOREIGN KEY (eve_id) REFERENCES Evenement (id) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Evenement_equipe
    ADD CONSTRAINT
        fk_eve_equ_equ_nom FOREIGN KEY (nom) REFERENCES Equipe (nom) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Equipe_Etape
    ADD CONSTRAINT
        fk_eeta_eta_eta_nom FOREIGN KEY (eta_nom, eta_jeo_challengeid) REFERENCES Etape (nom, jeo_challengeid) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Equipe_Etape
    ADD CONSTRAINT
        fk_eeta_equ_equ_nom FOREIGN KEY (equ_nom) REFERENCES Equipe (nom) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE Equipe_ChallengeAttaqueDefense
    ADD CONSTRAINT
        fk_equ_challad_attdef_challenge_id FOREIGN KEY (attdef_challengeid) REFERENCES Challenge_Attaque_Defense (challengeId) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Equipe_ChallengeAttaqueDefense
    ADD CONSTRAINT
        fk_equ_challad_equ_nom FOREIGN KEY (equ_nom) REFERENCES Equipe (nom) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Challenge_Attaque_Defense
    ADD CONSTRAINT
        fk_challad_chall_challengeId FOREIGN KEY (challengeId) REFERENCES Challenge (challengeId) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Challenge_Jeopardy
    ADD CONSTRAINT
        fk_challj_chall_challengeId FOREIGN KEY (challengeId) REFERENCES Challenge (challengeId) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Challenge_Jeopardy
    ADD CONSTRAINT
        fk_challj_typeChall_nom FOREIGN KEY (typeJeopardy) REFERENCES Type_challenge_jeopardy (nomType) ON UPDATE CASCADE ON DELETE RESTRICT;

ALTER TABLE Equipe
    ADD CONSTRAINT
        fk_equipe_typeEqupe_nom FOREIGN KEY (typeEquipe) REFERENCES Type_equipe (nomType) ON UPDATE CASCADE ON DELETE RESTRICT;


