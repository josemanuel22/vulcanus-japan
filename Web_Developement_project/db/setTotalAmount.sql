UPDATE clientorders
SET totalamount=subquery.totalamount
FROM (
	select customerid, date, orderid, sum(bet) as totalamount
	from clientbets natural join bets natural join clientorders
	group by customerid, date, orderid
) AS subquery
WHERE clientorders.orderid=subquery.orderid;