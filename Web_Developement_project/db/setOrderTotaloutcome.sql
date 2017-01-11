/*
 *	Una vez se disponga de esta información, realizar un procedimiento almacenado
 *	setOrderTotaloutcome.sql que complete la columna
 *	clientorders.totaloutcome (suma del outcome de las apuestas del pedido)
 * 
 */

CREATE OR REPLACE FUNCTION ganancias() RETURNS integer AS $$
DECLARE
	total clientbets.outcome%TYPE;
BEGIN
	UPDATE clientorders
	SET totaloutcome=subquery.total
	FROM (
		select orderid, SUM(outcome) as total
		from clientbets
		group by orderid
	) AS subquery
	WHERE subquery.orderid = clientorders.orderid;
	RETURN NULL;
END;

$$ LANGUAGE plpgsql;
