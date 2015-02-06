create or replace procedure Podwyzka
(p_procent IN NUMBER, p_etat IN pracownicy.etat%TYPE) is
begin
 update pracownicy
 set placa_pod = placa_pod + placa_pod * p_procent / 100
 where etat = p_etat;
end;
