-- Crear la bases de datos
CREATE DATABASE IF NOT EXISTS app_db;
GRANT ALL PRIVILEGES ON app_db.* TO 'app_user'@'%';

CREATE DATABASE IF NOT EXISTS app_test_db;
GRANT ALL PRIVILEGES ON app_test_db.* TO 'app_user'@'%';