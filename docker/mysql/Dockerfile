FROM mysql:5.7

COPY app.cnf /etc/mysql/conf.d/lidl.cnf
COPY init.sql /docker-entrypoint-initdb.d/init.sql
