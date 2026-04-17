<?php
require_once 'db.php';

// Redirect if already logged in
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: " . (isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : "index.php"));
    exit;
}

$redirect = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'index.php';
include 'header.php';
?>

<style>
/* Apple-Level Pro style overrides for login.php */
body { background: #fbfbfd; padding-top: 64px; }

.pro-auth-section { min-height: calc(100vh - 64px); display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
.pro-auth-card {
    width: 100%; max-width: 420px; background: #fff; border-radius: 32px; padding: 50px 40px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.04); text-align: center; border: 1px solid rgba(0,0,0,0.02);
}
.pro-auth-title { font-size: 32px; font-weight: 700; color: #1d1d1f; letter-spacing: -0.03em; margin-bottom: 8px; }
.pro-auth-subtitle { font-size: 15px; color: #86868b; margin-bottom: 40px; }

.pro-input-group { margin-bottom: 20px; text-align: left; }
.pro-input-group label { display: block; font-size: 13px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px; }
.pro-input-group input {
    width: 100%; padding: 16px; border: 1px solid rgba(0,0,0,0.08); border-radius: 14px; font-size: 16px; color: #1d1d1f; background: #fcfcfd; box-sizing: border-box; transition: all 0.3s;
}
.pro-input-group input:focus { outline: none; border-color: #007aff; box-shadow: 0 0 0 4px rgba(0,122,255,0.1); background: #fff; }

.pro-auth-btn {
    width: 100%; padding: 16px; background: #1d1d1f; color: #fff; border: none; border-radius: 980px; font-size: 17px; font-weight: 600; cursor: pointer; transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), background 0.3s; margin-top: 10px;
}
.pro-auth-btn:hover { background: #333336; transform: scale(0.98); }

.pro-divider { display: flex; align-items: center; margin: 30px 0; font-size: 13px; color: #86868b; }
.pro-divider::before, .pro-divider::after { content: ""; flex: 1; height: 1px; background: rgba(0,0,0,0.08); }
.pro-divider span { padding: 0 15px; }

.pro-switch { margin-top: 30px; font-size: 14px; color: #86868b; }
.pro-switch a { color: #007aff; text-decoration: none; font-weight: 500; cursor: pointer; transition: 0.3s; }
.pro-switch a:hover { opacity: 0.7; }

.pro-error { color: #ff3b30; background: #ffeeee; padding: 16px; border-radius: 14px; font-size: 14px; font-weight: 500; margin-bottom: 25px; display: none; }

</style>

<section class="pro-auth-section">
    <div class="pro-auth-card">

        <!-- LOGIN FORM -->
        <div id="loginFormContainer">
            <h2 class="pro-auth-title">Sign In</h2>
            <p class="pro-auth-subtitle">Log in to manage your WAGGY account.</p>

            <div class="pro-error" id="loginError"></div>

            <form id="loginForm" onsubmit="handleProAuth(event, 'login')">
                <div class="pro-input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="pro-input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="pro-auth-btn">Sign In</button>
            </form>

            <div class="pro-divider"><span>or connection</span></div>

            <div id="g_id_onload_login" data-client_id="YOUR_GOOGLE_CLIENT_ID" data-context="signin" data-ux_mode="popup" data-callback="handleGoogleCb" data-auto_prompt="false"></div>
            <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="signin_with" data-size="large" data-logo_alignment="center" style="display: flex; justify-content: center;"></div>

            <div class="pro-switch">
                Don't have an account? <a onclick="toggleProAuth('signup')">Create yours now.</a>
            </div>
        </div>

        <!-- SIGNUP FORM -->
        <div id="signupFormContainer" style="display: none;">
            <h2 class="pro-auth-title">Create ID</h2>
            <p class="pro-auth-subtitle">One account for everything WAGGY.</p>

            <div class="pro-error" id="signupError"></div>

            <form id="signupForm" onsubmit="handleProAuth(event, 'signup')">
                <div class="pro-input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Appleseed" required>
                </div>
                <div class="pro-input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="pro-input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="pro-auth-btn">Continue</button>
            </form>

            <div class="pro-divider"><span>or connection</span></div>

            <div id="g_id_onload" data-client_id="YOUR_GOOGLE_CLIENT_ID" data-context="use" data-ux_mode="popup" data-callback="handleGoogleCb" data-auto_prompt="false"></div>
            <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="signup_with" data-size="large" data-logo_alignment="center" style="display: flex; justify-content: center;"></div>

            <div class="pro-switch">
                Already have an ID? <a onclick="toggleProAuth('login')">Sign In.</a>
            </div>
        </div>

    </div>
</section>

<script>
    function toggleProAuth(type) {
        document.getElementById('loginFormContainer').style.display = type === 'login' ? 'block' : 'none';
        document.getElementById('signupFormContainer').style.display = type === 'signup' ? 'block' : 'none';
    }

    async function handleProAuth(e, type) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button');
        const err = type === 'login' ? document.getElementById('loginError') : document.getElementById('signupError');

        btn.innerHTML = 'Processing...'; btn.disabled = true; err.style.display = 'none';

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.action = type;

        try {
            const res = await fetch('ajax/auth_action.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
            });
            const result = await res.json();

            if (result.success) window.location.href = '<?= $redirect ?>';
            else {
                err.textContent = result.message || 'An error occurred.';
                err.style.display = 'block';
                btn.innerHTML = type === 'login' ? 'Sign In' : 'Continue';
                btn.disabled = false;
            }
        } catch (error) {
            err.textContent = 'Network error. Try again.';
            err.style.display = 'block';
            btn.innerHTML = type === 'login' ? 'Sign In' : 'Continue';
            btn.disabled = false;
        }
    }

    async function handleGoogleCb(response) {
        try {
            const res = await fetch('ajax/auth_action.php', {
                method: 'POST', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'google_auth', credential: response.credential })
            });
            const result = await res.json();
            if (result.success) window.location.href = '<?= $redirect ?>';
            else alert('Failed: ' + (result.message || 'Unknown'));
        } catch (error) { alert('Network error during Sign-In.'); }
    }
</script>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<?php include 'footer.php'; ?>