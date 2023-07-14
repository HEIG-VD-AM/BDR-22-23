-------------------------
--- Triggers
-- Auteurs: Rayane ANNEN, Felix BREVAL et Alexis MARTINS
-------------------------


SET SEARCH_PATH TO ctf;

--Mettre a jour le nombre de points quand il est resolu
CREATE OR REPLACE FUNCTION update_etape_points()
    RETURNS TRIGGER AS
$$
DECLARE diff INTEGER;
BEGIN
    SELECT nbpoints INTO diff
    FROM ctf.etape
    WHERE nom = NEW.eta_nom;
    diff = (0.9 * diff)::numeric::integer;
    IF diff > 50 THEN
        UPDATE ctf.etape
        SET nbpoints = diff
        WHERE nom = NEW.eta_nom;
    ELSE
        UPDATE ctf.etape
        SET nbpoints = 50
        WHERE nom = NEW.eta_nom;
    END IF;
    RETURN NEW;
END;
$$
    LANGUAGE plpgsql;
CREATE OR REPLACE TRIGGER update_points
    BEFORE INSERT ON Equipe_etape
    FOR EACH ROW
EXECUTE FUNCTION update_etape_points();


--Les points d'un challenge sont equivalent a 100 * saDifficulte
CREATE OR REPLACE FUNCTION set_nbpoints()
    RETURNS TRIGGER AS $$
BEGIN
    UPDATE etape
    SET nbpoints = 100 * NEW.difficulte
    WHERE nom = NEW.nom;
    RETURN NEW;
END;
$$
    LANGUAGE plpgsql;
CREATE OR REPLACE TRIGGER minimum_points
    AFTER INSERT ON etape
    FOR EACH ROW
EXECUTE FUNCTION set_nbpoints();


-- Change le type des challenges vers une valeur par défaut si un supprime le type du challenge
CREATE OR REPLACE FUNCTION check_challenge_type_delete()
    RETURNS TRIGGER AS $$
BEGIN
    UPDATE challenge_jeopardy SET typejeopardy = 'Misc' WHERE typejeopardy = OLD.nomtype;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER check_challenge_type_delete
    BEFORE DELETE ON type_challenge_jeopardy
    FOR EACH ROW
EXECUTE FUNCTION check_challenge_type_delete();


-- Change le type de l'équipe pour une valeur par défaut
CREATE OR REPLACE FUNCTION check_team_type_delete()
    RETURNS TRIGGER AS $$
BEGIN
    UPDATE equipe SET typeequipe = 'Other' WHERE typeequipe = OLD.nomtype;
    RETURN OLD;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER check_team_type_delete
    BEFORE DELETE ON type_equipe
    FOR EACH ROW
EXECUTE FUNCTION check_team_type_delete();