<?php
/**
 * Doji Funding — Auth Modals
 * 
 * Login and Sign Up modal overlays.
 * Included in footer.php, controlled by assets/js/auth.js
 */

$csrf = generateCsrf();
$user = getCurrentUser();
?>

<!-- ═══ LOGIN MODAL ═══ -->
<div class="modal-overlay" id="loginModal">
    <div class="modal">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
        <button class="modal-close" onclick="AuthModal.close()">&times;</button>
        
        <div class="modal-header">
            <img class="modal-logo" src="<?= LOGO_FILE ?>" alt="<?= SITE_NAME ?>" onerror="this.outerHTML='<div class=\'modal-logo-fallback\'>D</div>'">
            <h2 class="modal-title">Welcome Back</h2>
            <p class="modal-sub">Log in to access your Dashboard</p>
        </div>

        <form id="loginForm" onsubmit="AuthModal.submitLogin(event)">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <!-- Google Sign In -->
            <button type="button" class="google-btn" onclick="AuthModal.googleAuth()">
                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continue with Google
            </button>
            <div class="google-separator">or</div>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" type="email" name="email" 
                       placeholder="trader@example.com" required autocomplete="email">
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="form-input-wrap">
                    <input class="form-input" type="password" name="password" 
                           placeholder="••••••••" required autocomplete="current-password" id="loginPassword">
                    <button type="button" class="eye-toggle" onclick="AuthModal.togglePassword('loginPassword')" aria-label="Toggle password visibility"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                </div>
            </div>

            <div id="loginError" class="form-error"></div>

            <button type="submit" class="form-btn" id="loginBtn">
                <span class="btn-text">Log In</span>
                <span class="btn-loader" style="display:none"></span>
            </button>
        </form>

        <div class="modal-footer">
            Don't have an account?
            <a onclick="AuthModal.switchTo('signup')" class="modal-link">Sign Up</a>
        </div>
        </div><!-- /.modal-content -->
    </div>
</div>

<!-- ═══ SIGNUP MODAL ═══ -->
<div class="modal-overlay" id="signupModal">
    <div class="modal modal-wide">
        <canvas class="modal-dot-canvas" aria-hidden="true"></canvas>
        <div class="modal-content">
        <button class="modal-close" onclick="AuthModal.close()">&times;</button>
        
        <div class="modal-header">
            <img class="modal-logo" src="<?= LOGO_FILE ?>" alt="<?= SITE_NAME ?>" onerror="this.outerHTML='<div class=\'modal-logo-fallback\'>D</div>'">
            <h2 class="modal-title">Start Trading</h2>
            <p class="modal-sub">Create your account to configure your challenge</p>
        </div>

        <form id="signupForm" onsubmit="AuthModal.submitSignup(event)">
            <input type="hidden" name="csrf" value="<?= $csrf ?>">

            <!-- Google Sign Up -->
            <button type="button" class="google-btn" onclick="AuthModal.googleAuth()">
                <svg viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                Continue with Google
            </button>
            <div class="google-separator">or</div>
            
            <!-- Row 1: First Name, Last Name, Email -->
            <div class="form-row form-row-3">
                <div class="form-group">
                    <label class="form-label">First Name <span class="form-req">*</span></label>
                    <input class="form-input" type="text" name="first_name" 
                           placeholder="First name" required autocomplete="given-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span class="form-req">*</span></label>
                    <input class="form-input" type="text" name="last_name" 
                           placeholder="Last name" required autocomplete="family-name">
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span class="form-req">*</span></label>
                    <input class="form-input" type="email" name="email" 
                           placeholder="Email" required autocomplete="email">
                </div>
            </div>

            <!-- Row 2: Password -->
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password <span class="form-req">*</span></label>
                    <div class="form-input-wrap">
                        <input class="form-input" type="password" name="password"
                               placeholder="Min. 8 characters" required minlength="8"
                               autocomplete="new-password" id="signupPassword">
                        <button type="button" class="eye-toggle" onclick="AuthModal.togglePassword('signupPassword')" aria-label="Toggle password visibility"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                    </div>
                    <div class="form-hint" id="passwordStrength"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Referral code</label>
                    <input class="form-input" type="text" name="referral"
                           placeholder="Optional" autocomplete="off">
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="form-checks">
                <label class="form-check">
                    <input type="checkbox" name="marketing">
                    <span>I agree to receive marketing communications</span>
                </label>
                <label class="form-check">
                    <input type="checkbox" name="terms" required>
                    <span>I agree to the <a href="privacy.php" class="modal-link">Privacy Policy</a> and <a href="terms.php" class="modal-link">Terms</a>.</span>
                </label>
            </div>

            <div id="signupError" class="form-error"></div>

            <button type="submit" class="form-btn" id="signupBtn">
                <span class="btn-text">Create Account</span>
                <span class="btn-loader" style="display:none"></span>
            </button>
        </form>

        <div class="modal-footer">
            Already have an account?
            <a onclick="AuthModal.switchTo('login')" class="modal-link">Sign in</a>
        </div>
        </div><!-- /.modal-content -->
    </div>
</div>

<!-- ═══ USER DROPDOWN (logged in) ═══ -->
<?php if (isLoggedIn()): ?>
<div class="user-dropdown" id="userDropdown" style="display:none">
    <div class="dropdown-header">
        <div class="dropdown-avatar"><?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?></div>
        <div>
            <div class="dropdown-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
            <div class="dropdown-email"><?= htmlspecialchars($user['email']) ?></div>
        </div>
    </div>
    <div class="dropdown-sep"></div>
    <a class="dropdown-item" href="dashboard.php"><?= icon('chart', 14) ?> Dashboard</a>
    <a class="dropdown-item" href="dashboard.php#challenges"><?= icon('target', 14) ?> My Challenges</a>
    <a class="dropdown-item" href="dashboard.php#settings">⚙️ Settings</a>
    <div class="dropdown-sep"></div>
    <a class="dropdown-item dropdown-logout" onclick="AuthModal.logout()">↪ Log Out</a>
</div>
<?php endif; ?>
