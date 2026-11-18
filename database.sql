-- psql -a -d $DATABASE_URL -f database.sql
CREATE TABLE IF NOT EXISTS urls (
    id SERIAL PRIMARY KEY,
    name VARCHAR UNIQUE NOT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE IF NOT EXISTS url_checks (
    id SERIAL PRIMARY KEY,
    url_id BIGINT NOT NULL REFERENCES urls(id) ON DELETE RESTRICT,
    status_code INT NOT NULL,
    h1 VARCHAR,
    title VARCHAR,
    description TEXT,
    created_at TIMESTAMP NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_url_checks_url_id ON url_checks(url_id);