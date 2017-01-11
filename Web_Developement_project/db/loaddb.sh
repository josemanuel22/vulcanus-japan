#!/bin/bash
echo "creamos la base de datos y hace el dump"
createdb -U alumnodb si1
gunzip -c dump-v1.4a.sql.gz | psql -U alumnodb si1

echo "modificamos la db con nuestros cambios."
psql -f actualiza.sql -U alumnodb si1

echo "consulta d)"
psql -f setTotalAmount.sql -U alumnodb si1

echo "consulta e)"
psql -f setOutcomeBets.sql -U alumnodb si1

echo "consulta f)"
psql -f setOrderTotaloutcome.sql -U alumnodb si1
psql -c "select ganancias()" -U alumnodb si1

echo "consulta g)"
psql -f updBets.sql -U alumnodb si1

echo "consulta h)"
psql -f updOrders.sql -U alumnodb si1

echo "consulta i)"
psql -f updCredit.sql -U alumnodb si1
