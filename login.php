<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Register - May Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
    <link rel="stylesheet" href="login.css" />
    <style>
        .d-none { display: none; }
        #passwordHelp { font-size: 0.8rem; margin-top: 5px; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear { display: none; }
        
        .auth-input-wrapper { position: relative; }
        .auth-eye {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            z-index: 10;
        }
        input[type="password"] { padding-right: 45px !important; }
    </style>
</head>
<body>
    <?php include_once("navbar.php"); ?>

    <div class="auth-page">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Welcome</h2>
                <p>Login or create an account to continue.</p>
            </div>
            <div class="auth-tabs">
                <button id="tab-login" class="auth-tab active" type="button">Login</button>
                <button id="tab-register" class="auth-tab" type="button">Register</button>
            </div>

            <div class="auth-body">
                <form id="loginForm" action="dblogin.php" method="POST" class="auth-form">
                    <div class="auth-field">
                        <label for="loginEmail">Email Address</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-envelope auth-icon"></i>
                            <input type="email" id="loginEmail" name="email" placeholder="example@mail.com" required>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label for="loginPassword">Password</label>
                        <div class="auth-input-wrapper">
                            <i class="fas fa-lock auth-icon"></i>
                            <input type="password" id="loginPassword" name="password" placeholder="Enter password" required>
                            <i class="fas fa-eye-slash auth-eye" id="toggleLoginPassword"></i>
                        </div>
                    </div>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="auth-alert" id="alertbox" style="background: #fff5f5; border: 1px solid #feb2b2; padding: 10px; border-radius: 8px; margin-bottom: 15px; color: #c53030;">
                            <i class="fas fa-circle-exclamation"></i>
                            <span id="error-message">
                                <?php 
                                    if($_GET['error'] == 'email_not_found') echo "This email is not registered yet.";
                                    else if($_GET['error'] == 'wrong_password') echo "Incorrect password. Please try again.";
                                    else if($_GET['error'] == 'too_many_attempts') {
                                        $wait = isset($_GET['wait']) ? (int)$_GET['wait'] : 60;
                                        echo "Too many failed attempts. Please wait <b id='timer'>$wait</b> seconds.";
                                    }
                                    else echo "Invalid email or password.";
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <button type="submit" id="loginBtn" class="auth-submit">Login</button>
                </form>

                <form id="registerForm" action="dbregister.php" method="POST" class="auth-form d-none">
                        <div class="auth-grid">
                            <div class="auth-field">
                                <label for="firstName">First Name</label>
                                <div class="auth-input-wrapper">
                                    <i class="fas fa-user auth-icon"></i>
                                    <input type="text" id="firstName" name="firstName" placeholder="First name" required>
                                </div>
                            </div>
                            <div class="auth-field">
                                <label for="lastName">Last Name</label>
                                <div class="auth-input-wrapper">
                                    <i class="fas fa-user auth-icon"></i>
                                    <input type="text" id="lastName" name="lastName" placeholder="Last name" required>
                                </div>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="registerEmail">Email Address</label>
                            <div class="auth-input-wrapper">
                                <i class="fas fa-envelope auth-icon"></i>
                                <input type="email" id="registerEmail" name="email" required>
                            </div>
                            <div id="emailHelp" style="font-size: 0.8rem; margin-top: 5px;"></div> </div>

                        <div class="auth-field">
                            <label for="address">Home Address</label>
                            <div class="auth-input-wrapper">
                                <i class="fas fa-location-dot auth-icon"></i>
                                <input type="text" id="address" name="address" placeholder="Street, Township, City" required>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="contact">Phone Number</label>
                            <div class="auth-input-wrapper">
                                <i class="fas fa-phone auth-icon"></i>
                                <input type="text" id="contact" name="contact" placeholder="09xxxxxxxxx" maxlength="11" required>
                            </div>
                            <div id="contactHelp" style="font-size: 0.8rem; margin-top: 5px;"></div> 
                        </div>

                        <div class="auth-field">
                            <label for="registerPassword">New Password</label>
                            <div class="auth-input-wrapper">
                                <i class="fas fa-lock auth-icon"></i>
                                <input type="password" id="registerPassword" name="password" placeholder="Min. 8 chars (A-Z, a-z, 0-9, @)" required>
                                <i class="fas fa-eye-slash auth-eye" id="toggleRegisterPassword"></i>
                            </div>
                            <div id="passwordHelp" style="font-size: 0.8rem; margin-top: 5px;"></div>
                        </div>

                        <button type="submit" id="registerBtn" class="auth-submit" disabled style="opacity: 0.6; cursor: not-allowed;">Create Account</button>
                    </form>
            </div>
        </div>
    </div>

    <script>
    const tabLogin = document.getElementById('tab-login');
    const tabRegister = document.getElementById('tab-register');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginBtn = document.getElementById('loginBtn');
    
    // Countdown Timer for Brute Force
    const timerDisplay = document.getElementById('timer');
    if (timerDisplay) {
        loginBtn.disabled = true;
        loginBtn.style.opacity = "0.6";
        loginBtn.style.cursor = "not-allowed";
        
        let seconds = parseInt(timerDisplay.innerText);
        const countdown = setInterval(() => {
            seconds--;
            timerDisplay.innerText = seconds;
            if (seconds <= 0) {
                clearInterval(countdown);
                document.getElementById('alertbox').style.display = 'none';
                loginBtn.disabled = false;
                loginBtn.style.opacity = "1";
                loginBtn.style.cursor = "pointer";
            }
        }, 1000);
    }

    const regForm = document.getElementById('registerForm');
    const inputs = regForm.querySelectorAll('input[required]');
    const registerBtn = document.getElementById('registerBtn');
    
    const emailInput = document.getElementById('registerEmail');
    const contactInput = document.getElementById('contact');
    const passInput = document.getElementById('registerPassword');
    
    const emailHelp = document.getElementById('emailHelp');
    const contactHelp = document.getElementById('contactHelp');
    const passwordHelp = document.getElementById('passwordHelp');
    const toggleLoginPassword = document.getElementById('toggleLoginPassword');
    const loginPasswordInput = document.getElementById('loginPassword');

    toggleLoginPassword.addEventListener('click', function () {
        const type = loginPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        loginPasswordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
    const registerPasswordInput = document.getElementById('registerPassword');

    toggleRegisterPassword.addEventListener('click', function () {
        const type = registerPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        registerPasswordInput.setAttribute('type', type);
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    tabLogin.addEventListener('click', () => {
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');
        loginForm.classList.remove('d-none');
        registerForm.classList.add('d-none');
    });

    tabRegister.addEventListener('click', () => {
        tabRegister.classList.add('active');
        tabLogin.classList.remove('active');
        registerForm.classList.remove('d-none');
        loginForm.classList.add('d-none');
    });

    function validateForm() {
        let allFilled = true;
        inputs.forEach(input => {
            if (!input.value.trim()) allFilled = false;
        });

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isEmailValid = emailPattern.test(emailInput.value);
        const isContactValid = contactInput.value.length >= 9 && contactInput.value.length <= 11;
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/;
        const isPassStrong = strongRegex.test(passInput.value);

        if (allFilled && isEmailValid && isContactValid && isPassStrong) {
            registerBtn.disabled = false;
            registerBtn.style.opacity = "1";
            registerBtn.style.cursor = "pointer";
        } else {
            registerBtn.disabled = true;
            registerBtn.style.opacity = "0.6";
            registerBtn.style.cursor = "not-allowed";
        }
    }

    emailInput.addEventListener('input', () => {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (emailInput.value.length === 0) {
            emailHelp.innerHTML = "";
        } else if (emailPattern.test(emailInput.value)) {
            emailHelp.innerHTML = "<span class='text-success'><i class='fas fa-check-circle'></i> Valid email format</span>";
        } else {
            emailHelp.innerHTML = "<span class='text-danger'><i class='fas fa-times-circle'></i> Please enter a valid email</span>";
        }
        validateForm();
    });

    contactInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 0) {
            contactHelp.innerHTML = "";
        } else if (this.value.length >= 9 && this.value.length <= 11) {
            contactHelp.innerHTML = "<span class='text-success'><i class='fas fa-check-circle'></i> Valid phone number</span>";
        } else {
            contactHelp.innerHTML = "<span class='text-danger'><i class='fas fa-times-circle'></i> Must be 9-11 digits</span>";
        }
        validateForm();
    });

    passInput.addEventListener('input', function() {
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*])(?=.{8,})/;
        if (this.value.length === 0) {
            passwordHelp.innerHTML = "";
        } else if (strongRegex.test(this.value)) {
            passwordHelp.innerHTML = "<span class='text-success'><i class='fas fa-check-circle'></i> Strong password</span>";
        } else {
            passwordHelp.innerHTML = "<span class='text-danger'><i class='fas fa-times-circle'></i> Must include uppercase, lowercase, number, and symbol (min. 8).</span>";
        }
        validateForm();
    });

    inputs.forEach(input => {
        input.addEventListener('input', validateForm);
    });
</script>
</body>
</html>