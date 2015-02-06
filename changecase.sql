CREATE OR REPLACE FUNCTION changecase
(p_id_prac IN pracownicy.id_prac%TYPE,
 p_nazwisko OUT pracownicy.nazwisko%TYPE)
RETURN NUMBER IS
 liczba_prac NUMBER;
BEGIN
 select count(*) into liczba_prac from pracownicy where id_prac = p_id_prac;
 IF liczba_prac > 0 THEN
  SELECT INITCAP(nazwisko) INTO p_nazwisko FROM pracownicy
  WHERE id_prac = p_id_prac;
  RETURN 1;
 ELSE
  RETURN 0;
 END IF;
END changecase;
