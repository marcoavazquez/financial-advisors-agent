## Install postgresql and pgvector extension
```bash
sudo apt install -y postgresql-common
sudo /usr/share/postgresql-common/pgdg/apt.postgresql.org.sh
```
There is not 18 pgvector version 
```bash
sudo apt update
sudo apt install postgresql-17
sudo apt install postgresql-17-pgvector
```
Enable extension
```sql
\c [database name]
CREATE EXTENSION vector;
```
### If the cluster was not created automatically
```bash
# check
sudo pg_lsclusters
# create the cluster
sudo pg_createcluster 17 main --start
```