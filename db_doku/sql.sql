USE `test-api`;
-- Benutzer
CREATE TABLE users (
                       id INT AUTO_INCREMENT PRIMARY KEY,
                       username VARCHAR(50) NOT NULL UNIQUE,
                       email VARCHAR(100) NOT NULL UNIQUE,
                       password_hash VARCHAR(255) NOT NULL,
                       created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- Benutzerprofil (Informationen des Benutzers)
CREATE TABLE profiles (
                          id INT AUTO_INCREMENT PRIMARY KEY,
                          user_id INT NOT NULL,
                          bio TEXT,
                          avatar_url VARCHAR(255),
                          FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Termine (wird von einem Benutzer verwaltet)
CREATE TABLE events (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        creator_id INT NOT NULL,
                        title VARCHAR(100) NOT NULL,
                        description TEXT,
                        start_time DATETIME NOT NULL,
                        end_time DATETIME NOT NULL,
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 4. Einladungen / Teilnehmer (inkl. Status) (benutzer zu einem Termin hinzufügen)
CREATE TABLE event_participants (
                                    event_id INT NOT NULL,
                                    user_id INT NOT NULL,
                                    status ENUM('invited', 'accepted', 'declined') DEFAULT 'invited',
                                    invited_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    PRIMARY KEY (event_id, user_id),
                                    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
                                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Anhänge (Dateiuploads)
CREATE TABLE attachments (
                             id INT AUTO_INCREMENT PRIMARY KEY,
                             event_id INT NOT NULL,
                             file_path VARCHAR(255) NOT NULL,
                             uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                             FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- 6. E-Mail Verifizierung (optional)
CREATE TABLE email_verifications (
                                     id INT AUTO_INCREMENT PRIMARY KEY,
                                     user_id INT NOT NULL,
                                     verification_token VARCHAR(255) NOT NULL,
                                     is_verified BOOLEAN DEFAULT FALSE,
                                     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);