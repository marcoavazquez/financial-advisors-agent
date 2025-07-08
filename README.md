## Run the server
```bash
composer install
npm install
composer run dev
```

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

## Add the next variables to the .env file
```bash
GOOGLE_CLIENT_ID=YOUR_GOOGLE_CLIENT_ID
GOOGLE_CLIENT_SECRET=YOUR_GOOGLE_CLIENT_SECRET
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/callback

OPENAI_API_KEY=YOUR_OPENAI_API_KEY
OPENAI_ORGANIZATION=

HUBSPOT_APP_ID=YOUR_HUBSPOT_APP_ID
HUBSPOT_CLIENT_ID=YOUR_HUBSPOT_CLIENT_ID
HUBSPOT_CLIENT_SECRET=YOUR_HUBSPOT_CLIENT_SECRET
HUBSPOT_REDIRECT_URI=http://localhost:8000/hubspot/callback
HUBSPOT_API_KEY=YOUR_HUBSPOT_API_KEY
```