# Database Transactions
Database transactions are applied automatically via middleware. When an (unhandled) exception occurs during one of the database queries the query is rolled back to prevent data corruption.

When a request hits the middleware, the request method is determined. If this method is `POST`, `PUT` or `DELETE` the transactions are enabled for this request.