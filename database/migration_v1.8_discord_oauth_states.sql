-- Migration v1.8 — Discord OAuth2 state tokens
CREATE TABLE discord_oauth_states (
  id         INT          AUTO_INCREMENT PRIMARY KEY,
  user_id    INT          NOT NULL,
  state      CHAR(32)     NOT NULL,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP    NOT NULL,
  UNIQUE KEY uq_state (state),
  INDEX      idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
