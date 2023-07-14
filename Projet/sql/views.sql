-------------------------
--- Vues
-- Auteurs: Rayane ANNEN, Felix BREVAL et Alexis MARTINS
-------------------------


SET SEARCH_PATH TO ctf;

-- Points de chaque equipe regroupes par challenge
DROP VIEW IF EXISTS points_par_equipe_par_challenge CASCADE;
CREATE OR REPLACE VIEW points_par_equipe_par_challenge
AS
SELECT EE.equ_nom, CJ.challengeid, SUM(E.nbpoints)
FROM equipe_etape EE
         JOIN etape E on EE.eta_nom = E.nom
         JOIN challenge_jeopardy CJ on E.jeo_challengeid = CJ.challengeid
GROUP BY EE.equ_nom, CJ.challengeid;

-- Points de chaque equipe regroupes par evenement
DROP VIEW IF EXISTS points_par_equipe_par_evenement CASCADE;
CREATE OR REPLACE VIEW points_par_equipe_par_evenement
AS
SELECT E.id, PEE.equ_nom, E.nom, SUM(PEE.sum)
FROM points_par_equipe_par_challenge PEE
         JOIN challenge C on PEE.challengeid = C.challengeid
         JOIN evenement E on C.eve_id = E.id
GROUP BY PEE.equ_nom, E.id;

-- Liste d'équipes et des challenges dont elles ont réalisé toutes les étapes
CREATE VIEW equipe_a_fait_jeo_challenge
AS
SELECT EE.equ_nom, CJ.challengeid, MAX(EE.date_realisation)
FROM equipe_etape EE
         JOIN etape E ON E.nom = EE.eta_nom
         JOIN challenge_jeopardy CJ ON CJ.challengeid = E.jeo_challengeid
GROUP BY EE.equ_nom, CJ.challengeid
HAVING COUNT(*) = (SELECT COUNT(*)
                   FROM challenge_jeopardy CJ2
                            JOIN etape E ON E.jeo_challengeid = CJ2.challengeid
                   GROUP BY CJ2.challengeid
                   HAVING CJ2.challengeid = CJ.challengeid);

