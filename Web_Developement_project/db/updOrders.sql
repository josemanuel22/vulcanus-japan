--Realizar un trigger updOrders.sql que
--actualice la información de totalamount y totaloutcome de la tabla clientorders
--cuando se añada, elimine o modifique una apuesta de clientbets.

CREATE OR REPLACE FUNCTION updateOrders() RETURNS TRIGGER AS $$
    BEGIN

        IF (TG_OP = 'DELETE') THEN
		--se borra una apuesta del carrito
			UPDATE clientorders
			SET totalamount=totalamount-OLD.bet
			FROM clientbets
			WHERE clientorders.orderid = OLD.orderid;
			--select * from clientorders where customerid=7068 and orderid=74409;
			--select * from clientbets where customerid=7068 and orderid=74409 --clientbetid=264;
			--select * from clientbets where clientbetid=700000
			--DELETE FROM clientbets WHERE clientbetid=700000;
        ELSIF (TG_OP = 'UPDATE') THEN
		--se resuelve una apuesta
			IF (NEW.outcome<>OLD.outcome) THEN
				UPDATE clientorders
				SET totaloutcome=totaloutcome+NEW.outcome
				FROM clientbets
				WHERE clientbets.orderid = clientorders.orderid;
			--select * from clientorders where customerid=7068 and orderid=74409;
			--select * from clientbets where customerid=7068 and orderid=74409 --clientbetid=262;
			--UPDATE clientbets
			--SET outcome=100
			--WHERE customerid=7068 and clientbetid=262;
        --se cambia la cantidad apostada
			ELSIF (NEW.bet<>OLD.bet) THEN
				UPDATE clientorders
				SET totalamount=totalamount-OLD.bet+NEW.bet
				FROM clientbets
				WHERE clientbets.orderid = clientorders.orderid;
			--select * from clientbets limit 5;
			--select * from clientorders where customerid=7068 and orderid=74409;
			--select * from clientbets where customerid=7068 and orderid=74409 --clientbetid=264;
			--UPDATE clientbets
			--SET bet=286.13
			--WHERE customerid=7068 and clientbetid=264;
			END IF;
        ELSIF (TG_OP = 'INSERT') THEN
		--nueva apuesta en el carrito
			UPDATE clientorders
			SET totalamount=totalamount+NEW.bet
			FROM clientbets
			WHERE clientorders.orderid = NEW.orderid;
			--select * from clientorders where customerid=7068 and orderid=74409;
			--select * from clientbets where customerid=7068 and orderid=74409 --clientbetid=264;

			--INSERT INTO clientbets (customerid, optionid, bet, ratio, outcome, betid, orderid, clientbetid)
			--VALUES ('7068','63','500','6.5','0','300','74409','700000');
        END IF;
        RETURN NULL;
    END;
$$ LANGUAGE plpgsql;

--DROP TRIGGER updOrders on clientbets;
CREATE TRIGGER updOrders
AFTER INSERT OR UPDATE OR DELETE ON clientbets
FOR EACH ROW EXECUTE PROCEDURE updateOrders();
