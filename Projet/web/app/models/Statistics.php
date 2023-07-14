<?php namespace models;
use DateTime;
use util\PGSQLConnection;

/**
 * Classe concernant les statistiques
 */
class Statistics {
    /**
     * Récupère les 3 événements ayant le plus de participants
     * @return un tableau des 3 événements ayant le plus de participants
     */
    public static function get3MostHighlyParticipatedEvents() : array
    {
        // Top 3 des événements (actifs) avec le plus gros taux de participation
        $stmt = PGSQLConnection::instance()->prepare("
        SELECT EE.eve_id, E.nom, COUNT(*) AS nb_participants
        FROM evenement_equipe EE
        INNER JOIN evenement E on EE.eve_id = E.id
        WHERE E.datefin > NOW()
        GROUP BY EE.eve_id, E.nom
        ORDER BY nb_participants DESC
        LIMIT 3");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Sélectionne le nombre moyen de membres par équipe
     * @return le nombre moyen de membres par équipe
     */
    public static function getAverageMemberCountInTeam() {
        $stmt = PGSQLConnection::instance()->prepare("
        SELECT AVG(nb_membres)
        FROM (
            SELECT COUNT(*) AS nb_membres FROM membre_equipe
            GROUP BY equ_nom
        ) AS nb_membres_par_equipe");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le nombre moyen de participants à un événement
     * @return le nombre moyen de participants à un événement
     */
    public static function getAverageParticipatingMembersToEvents() {
        $stmt = PGSQLConnection::instance()->prepare("
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
            ) as nb_participants_par_evenement");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le temps moyen pour compléter une étape
     * @return le temps moyen pour compléter une étape
     */
    public static function getAverageTimeToCompleteStep() {
        $stmt = PGSQLConnection::instance()->prepare("
        SELECT AVG(temps)
        FROM (
            SELECT EE.date_realisation - E.date_creation AS temps
            FROM equipe_etape EE
            INNER JOIN etape E ON EE.eta_nom = E.nom) as dates");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le temps moyen pour compléter un challenge
     * @return le temps moyen pour compléter un challenge
     */
    public static function getAverageTimeToCompleteChallenge() {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT AVG(temps)
            FROM (
                SELECT EAJC.max - CJ.datecreation AS temps
                FROM equipe_a_fait_jeo_challenge EAJC
                INNER JOIN challenge_jeopardy CJ on EAJC.challengeid = CJ.challengeid
                 ) as dates");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le nombre de points moyen par équipe par événement
     * @return le nombre de points moyen par équipe par événement
     */
    public static function getAveragePointPerTeamPerEvent() {
        $stmt = PGSQLConnection::instance()->prepare("
        SELECT AVG(nb_points)
        FROM (
              SELECT AVG(nbpoints) as nb_points
              FROM evenement_equipe EE
                       INNER JOIN equipe_etape EE2 ON EE.nom = EE2.equ_nom
                       INNER JOIN etape E ON EE2.eta_nom = E.nom
              GROUP BY EE.nom, EE.eve_id
              ) as moyenne_par_equipe_par_evenement");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le nombre de mots moyen dans un writeup
     * @return le nombre de mots moyen dans un writeup
     */
    public static function getAverageWordsPerWriteup() {
        $stmt = PGSQLConnection::instance()->prepare("
        SELECT AVG(ARRAY_LENGTH(REGEXP_SPLIT_TO_ARRAY(W.contenu, '\s'),1)) FROM writeup W");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le taux de participation moyen aux événements
     * @return le taux de participation moyen aux événements
     */
    public static function getParticipationRateToEventPerTeam() {
        $stmt = PGSQLConnection::instance()->prepare("
        SELECT (AVG(nb_equipes_par_evenement) / (SELECT COUNT(*) FROM equipe)) * 100 AS \"taux_moyen_participation (%)\"
        FROM (
            SELECT COUNT(*) AS nb_equipes_par_evenement
            FROM evenement_equipe EE
            GROUP BY EE.eve_id
             ) as nb_equipes_par_evenement");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le type de challenge le plus réalisé
     * @return le type de challenge le plus réalisé
     */
    public static function getMostCompletedChallengeType() {
        $stmt = PGSQLConnection::instance()->prepare("
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
            LIMIT 1");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Sélectionne le type de challenge réalisé le plus rapidement
     * @return le type de challenge réalisé le plus rapidement
     */
    public static function getFastestCompletedType() {
        $stmt = PGSQLConnection::instance()->prepare("
            SELECT CJ.typejeopardy
            FROM challenge_jeopardy CJ
            INNER JOIN equipe_a_fait_jeo_challenge EAJC ON CJ.challengeid = EAJC.challengeid
            WHERE (EAJC.max - CJ.datecreation) = (SELECT MIN(temps)
                                                  FROM (
                                                  SELECT EAJC2.max - CJ2.datecreation AS temps
                                                  FROM equipe_a_fait_jeo_challenge EAJC2
                                                  INNER JOIN challenge_jeopardy CJ2 on EAJC2.challengeid = CJ2.challengeid
                                                  ) as dates);");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}