-- =========================
-- USERS
-- =========================
CREATE TABLE IF NOT EXISTS users (
                                     id SERIAL PRIMARY KEY,
                                     login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    gender VARCHAR(6) CHECK (gender IN ('male', 'female')),
    birth_date DATE,
    status VARCHAR(7) NOT NULL DEFAULT 'active'
    CHECK (status IN ('active', 'blocked')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

-- =========================
-- USER CONTACTS
-- =========================
CREATE TABLE IF NOT EXISTS user_contacts (
                                             id SERIAL PRIMARY KEY,
                                             user_id INT NOT NULL,
                                             type VARCHAR(7) NOT NULL CHECK (type IN ('phone', 'email', 'address')),
    value VARCHAR(255) NOT NULL,
    CONSTRAINT fk_user_contacts_user
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
    );

CREATE INDEX idx_user_contacts_user_id ON user_contacts(user_id);

-- =========================
-- USER BALANCES
-- =========================
CREATE TABLE IF NOT EXISTS user_balances (
                                             id SERIAL PRIMARY KEY,
                                             user_id INT NOT NULL,
                                             currency VARCHAR(3) NOT NULL CHECK (currency IN ('EUR', 'USD', 'RUB')),
    balance NUMERIC(10, 2) NOT NULL DEFAULT 0,
    CONSTRAINT uq_user_currency UNIQUE (user_id, currency),
    CONSTRAINT fk_user_balances_user
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
    );

CREATE INDEX idx_user_balances_user_id ON user_balances(user_id);

-- =========================
-- EVENTS (MATCHES)
-- =========================
CREATE TABLE IF NOT EXISTS events (
                                      id SERIAL PRIMARY KEY,
                                      title VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

-- =========================
-- BETS
-- =========================
CREATE TABLE IF NOT EXISTS bets (
                                    id SERIAL PRIMARY KEY,
                                    user_id INT NOT NULL,
                                    event_id INT NOT NULL,
                                    outcome VARCHAR(4) NOT NULL CHECK (outcome IN ('home', 'draw', 'away')),
    coefficient NUMERIC(5, 2) NOT NULL CHECK (coefficient >= 1.01 AND coefficient <= 40.00),
    amount NUMERIC(10, 2) NOT NULL CHECK (amount >= 1 AND amount <= 500),
    currency VARCHAR(3) NOT NULL CHECK (currency IN ('EUR','USD','RUB')) DEFAULT 'EUR',
    status VARCHAR(7) NOT NULL DEFAULT 'pending' CHECK (status IN ('pending', 'won', 'lost')),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    settled_at TIMESTAMP,
    CONSTRAINT fk_bets_user
    FOREIGN KEY (user_id)
    REFERENCES users(id),
    CONSTRAINT fk_bets_event
    FOREIGN KEY (event_id)
    REFERENCES events(id)
    );

CREATE INDEX idx_bets_user_id ON bets(user_id);
CREATE INDEX idx_bets_event_id ON bets(event_id);
