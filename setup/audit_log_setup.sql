CREATE TABLE audit_log (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,

    user_id INT NOT NULL,
    action_description VARCHAR(255) NOT NULL,

    affected_id INT NULL,   -- optional, no FK on purpose

    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_auditlog_user
        FOREIGN KEY (user_id) REFERENCES user(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
