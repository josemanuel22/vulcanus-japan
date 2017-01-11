--Realizar un trigger
--updCredit.sql que actualice el campo credit de la tabla customers cuando se
--finalice la compra, es decir, cuando la fecha del pedido pase de NULL a un valor.
--También debe actualizar credit cuando se cierren todas las apuestas de un pedido,
--es decir, cuando totaloutcome sea definitivo.

CREATE OR REPLACE FUNCTION updCredit_date() RETURNS TRIGGER AS $$
BEGIN
	IF (NEW.confirmed = true) THEN
		UPDATE customers
		SET credit=credit-NEW.bet;
	END IF;
	RETURN NULL;

END
$$ LANGUAGE plpgsql;

--descomentar para modificar
--DROP TRIGGER t_updCredit_date on clientorders;
CREATE TRIGGER t_updCredit_date AFTER UPDATE OF confirmed ON clientbets
FOR EACH ROW EXECUTE PROCEDURE updCredit_date();

/*
select * from clientorders where customerid = 6944 and orderid=73075;
select customerid, credit from customers where customerid=6944;

update clientorders set date=current_date where customerid = 6944 and orderid=73075;
select customerid, credit from customers where customerid=6944;
*/