-- =========================
-- USERS
-- =========================
string $login,
        string $password,
        string $name,
        string $gender,
        string $birthDate,
        string $status = 'active',
        ?int $id = null
INSERT INTO users (login, password, name, gender, birth_date)
VALUES
    (
        'john_doe',
        '$2y$10$examplehashedpassword1',
        'John Doe',
        'male',
        '1990-05-12'
    ),
    (
        'jane_smith',
        '$2y$10$examplehashedpassword2',
        'Jane Smith',
        'female',
        '1994-09-21'
    );

-- =========================
-- USER CONTACTS
-- =========================

-- John Doe contacts (id = 1)
INSERT INTO user_contacts (user_id, type, value)
VALUES
    (1, 'phone', '+491111111111'),
    (1, 'phone', '+491222222222'),
    (1, 'email', 'john.doe@gmail.com'),
    (1, 'email', 'john.work@company.com');

-- Jane Smith contacts (id = 2)
INSERT INTO user_contacts (user_id, type, value)
VALUES
    (2, 'phone', '+447700900111'),
    (2, 'phone', '+447700900222'),
    (2, 'email', 'jane.smith@gmail.com'),
    (2, 'email', 'jane.personal@mail.com');

-- =========================
-- USER BALANCES
-- =========================

-- John Doe balances
INSERT INTO user_balances (user_id, currency, balance)
VALUES
    (1, 'EUR', 25000.00),
    (1, 'USD', 12000.50),

-- Jane Smith balances
INSERT INTO user_balances (user_id, currency, balance)
VALUES
    (2, 'EUR', 50000.00),
    (2, 'USD', 80000.00),

-- =========================
-- EVENTS (MATCHES)
-- =========================

INSERT INTO events (title)
VALUES
    ('Team A vs Team B'),
    ('Team C vs Team D'),
    ('Team E vs Team F');
