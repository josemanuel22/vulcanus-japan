--actualizar apuestas ganadoras
UPDATE clientbets
SET outcome=subquery.outcome
FROM (
	select clientbetid, bet*ratio as outcome
	from (clientbets natural join bets)
	where winneropt=optionid
) AS subquery
WHERE subquery.clientbetid = clientbets.clientbetid;



--actualizar apuestas perdedoras
UPDATE clientbets
SET outcome=0
FROM (
	select clientbetid
	from (clientbets natural join bets)
	where winneropt<>optionid and confirmed=true and winneropt is not null
) AS subquery
WHERE subquery.clientbetid=clientbets.clientbetid;
