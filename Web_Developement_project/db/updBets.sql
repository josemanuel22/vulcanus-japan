CREATE OR REPLACE FUNCTION updBets() RETURNS TRIGGER AS $$
BEGIN
	UPDATE clientbets
	SET outcome=subquery.outcome
	FROM (
		select clientbetid, bet*ratio as outcome
		from (clientbets natural join bets)
		where winneropt=optionid and NEW.betid = bets.betid
	) AS subquery
	WHERE subquery.clientbetid = clientbets.clientbetid;

	UPDATE clientbets
	SET outcome=0
	FROM (
		select clientbetid
		from (clientbets natural join bets)
		where winneropt<>optionid and NEW.betid = bets.betid and winneropt is not null and confirmed=true
	) AS subquery
	WHERE subquery.clientbetid = clientbets.clientbetid;

	UPDATE customers
	SET credit=credit+outcome
	FROM clientbets natural join bets
	WHERE betid = NEW.betid and confirmed=true;

	RETURN NULL;
END
$$ LANGUAGE plpgsql;

--descomentar para modificar
--DROP TRIGGER t_updBets on bets;
CREATE TRIGGER t_updBets AFTER UPDATE OF winneropt ON bets
FOR EACH ROW EXECUTE PROCEDURE updBets();
/*
select * from bets where betid=21;
select * from bets natural join clientbets where betid=21;

update bets set winneropt=107 where betid=21;

select * from bets where betid=21;
select * from bets natural join clientbets where betid=21;

update bets set winneropt=1 where betid=21;

select * from bets where betid=21;
select * from bets natural join clientbets where betid=21;
*/


/* ojo ejecutar una sola vez, tras ejecucion reiniciar db
select * from clientbets natural join bets where orderid=94923 and betid=1141;
select credit from customers where customerid=8987;
update bets set winneropt=63 where betid=1141;
*/
