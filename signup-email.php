<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ThinkUp | Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <style>
        /* ====== CSS Variables ====== */
        :root {
            --ink: #023047;
            --teal: #218ca6;
            --bg: #e4f7fb;
            --track: #cfe9f1;
            --error: #b00020;
            --step-pad: 24px;
            --dot-size: 40px;
        }

        /* ====== Reset & Base ====== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg);
            color: #000;
            min-height: 100svh;
            display: grid;
            place-items: center;
            position: relative;
            padding: 24px 16px;
        }

        /* ======= Logo */
        .logo {
            position: absolute;
            top: -30px;
            left: 100px;
        }

        .logo img {
            width: 250px;
        }

        /* Container */
        .wrap {
            width: 100%;
            max-width: 520px;
            text-align: center;
        }

        .wrap.wide {
            max-width: 760px;
        }

        /* Stepper */
        .stepper {
            position: relative;
            margin: 8px auto 16px;
            max-width: 420px;
        }

        .stepper .track,
        .stepper .progress {
            position: absolute;
            left: 10%;
            right: 10%;
            top: 22px;
            height: 4px;
            border-radius: 9999px;
            z-index: 0;
            transition: width 0.25s ease, background-color 0.25s ease;
        }

        .stepper .track {
            background: var(--track);
        }

        .stepper .progress {
            background: var(--teal);
        }

        .stepper.done .progress,
        .stepper.done .track {
            background: #0b2330;
        }

        .dots {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 var(--step-pad);
            position: relative;
            z-index: 2;
        }

        .dot {
            width: var(--dot-size);
            height: var(--dot-size);
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            border: 2px solid var(--teal);
            background: #fff;
            color: var(--ink);
            transition: all 0.3s ease;
        }

        .dot.done {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
        }

        .dot.current {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
            box-shadow: 0 0 0 4px rgba(2, 48, 71, 0.08);
        }

        /* Typography */
        h1 {
            font: 500 1.8rem/1.15 'Archivo Black', sans-serif;
            color: #0b2330;
            margin: 40px 0 6px;
        }

        .sub {
            font-size: 0.92rem;
            color: #586469;
            margin-bottom: 20px;
        }

        .foot {
            font-size: 0.96rem;
            margin-top: 16px;
            color: #123;
        }

        .foot a {
            color: var(--teal);
            text-decoration: none;
            font-weight: 800;
        }

        .foot a:hover {
            text-decoration: underline;
        }

        /* Form */
        .form {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-top: 25px;
        }

        .step[data-step="3"] .form {
            align-items: center;
            gap: 14px;
        }

        /* Input Groups */
        .input-group {
            position: relative;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--teal);
            border-radius: 14px;
            background: var(--bg);
            font-size: 0.95rem;
            color: var(--ink);
            outline: none;
            transition: border 0.2s, box-shadow 0.2s;
            font-weight: 400;
        }

        .input-group input:focus {
            border-width: 3px;
            box-shadow: 0 0 0 3px rgba(33, 140, 166, 0.18);
        }

        .input-group.invalid input {
            border-color: var(--error) !important;
            box-shadow: 0 0 0 2px rgba(199, 58, 58, 0.12) !important;
        }

        /* Floating Labels */
        .input-group label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.95rem;
            font-weight: 400;
            color: var(--ink);
            background: transparent;
            padding: 0;
            transition: all 0.22s ease;
            pointer-events: none;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -10px;
            left: 18px;
            transform: none;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--ink);
            background: var(--bg);
            padding: 0 6px;
        }

        /* Password Toggle */
        .input-group.pw input {
            padding-right: 56px;
        }

        .toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            background: var(--bg);
            color: var(--ink);
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            user-select: none;
        }

        /* Error Messages */
        .error {
            display: none;
            font-size: 0.85rem;
            color: var(--error);
            text-align: left;
            margin-top: -10px;
        }

        .error.show {
            display: block;
        }

        /* Checkboxes */
        .terms {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: 0.9rem;
            text-align: left;
        }

        .terms a {
            color: var(--teal);
            font-weight: 800;
            text-decoration: none;
        }

        .opt {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 2px solid var(--teal);
            border-radius: 14px;
            background: #fff;
            padding: 12px 14px;
            text-align: left;
            transition: all 0.2s ease;
        }

        .opt input {
            width: 18px;
            height: 18px;
        }

        .opt span {
            color: #0b2330;
            font-weight: 500;
        }

        .opt:has(input:checked) {
            font-weight: 700;
        }

        .step[data-step="3"] .opt {
            width: 100%;
            max-width: 520px;
        }

        .step[data-step="3"] .opt:has(input:checked) {
            background: #e7f6fb;
            border-color: #69bfd3;
            box-shadow: 0 0 0 3px rgba(33, 140, 166, 0.18) inset;
        }

        /* Buttons & Actions */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            min-width: 510px;
            height: 44px;
            padding: 0 18px;
            border: 0;
            border-radius: 14px;
            cursor: pointer;
            font-weight: 800;
            background: #0f3a4c;
            color: #fff;
            transition: background 0.2s, transform 0.02s, opacity 0.2s;
        }

        .btn:hover {
            background: #218ca6;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn.primary {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            height: 48px;
            border-radius: 12px;
        }

        /* Step-specific Styles */
        .step[data-step="1"] .actions {
            justify-content: center;
        }

        #next1 {
            width: 100%;
            min-width: 100%;
            height: 48px;
            border-radius: 12px;
        }

        .step[data-step="2"] .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            justify-content: stretch;
        }

        .step[data-step="2"] .btn {
            min-width: 0;
            width: 100%;
            height: 48px;
            border-radius: 12px;
        }

        .step[data-step="3"] .actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            justify-content: center;
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
        }

        .step[data-step="3"] .btn {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            min-width: 0;
        }

        /* Utility Classes */
        .hidden {
            display: none;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .step[data-step="3"] h1 {
                white-space: normal;
            }
        }
    </style>
</head>
<body>
    <div class="logo">
        <img src="https://images.unsplash.com/vector-1761420317266-eaf29b9ce275?ixlib=rb-4.1.0&auto=format&fit=crop&q=80&w=220" alt="ThinkUp" />
    </div>

    <div class="wrap">
        <div class="stepper" aria-label="Sign up progress">
            <div class="track"></div>
            <div class="progress" id="progress"></div>
            <div class="dots">
                <div class="dot" data-step="1">1</div>
                <div class="dot" data-step="2">2</div>
                <div class="dot" data-step="3">3</div>
            </div>
        </div>

        <section class="step" data-step="1">
            <h1>Enter your account details</h1>
            <form class="form" onsubmit="return false">
                <div class="input-group" id="grpEmail">
                    <input id="email" type="text" placeholder=" " autocomplete="username" />
                    <label for="email">Email or Username</label>
                </div>

                <div class="input-group pw" id="grpPw1">
                    <input id="pw1" type="password" placeholder=" " autocomplete="new-password" />
                    <label for="pw1">Password</label>
                    <i class="bi bi-eye-slash-fill toggle" data-target="pw1"></i>
                </div>
                <div id="pw1Err" class="error">A number or symbol, at least 6 characters.</div>

                <div class="input-group pw" id="grpPw2">
                    <input id="pw2" type="password" placeholder=" " autocomplete="new-password" />
                    <label for="pw2">Confirm Password</label>
                    <i class="bi bi-eye-slash-fill toggle" data-target="pw2"></i>
                </div>
                <div id="pw2Err" class="error">Passwords don't match.</div>

                <label class="terms">
                    <input type="checkbox" id="tos" />
                    <span>I have read and agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>.</span>
                </label>

                <div class="actions">
                    <button type="button" class="btn" id="next1" disabled>
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </form>
            <p class="foot">Already have an account? <a href="log-in.php">Log in instead</a></p>
        </section>

        <section class="step hidden" data-step="2">
            <h1>Just a few more things...</h1>
            <form class="form" onsubmit="return false">
                <div class="input-group">
                    <input id="first" type="text" placeholder=" " autocomplete="given-name" required />
                    <label for="first">First Name</label>
                </div>
                <div class="input-group">
                    <input id="last" type="text" placeholder=" " autocomplete="family-name" required />
                    <label for="last">Last Name</label>
                </div>
                <div class="actions">
                    <button type="button" class="btn" id="prev2">
                        <i class="bi bi-chevron-left"></i> Previous
                    </button>
                    <button type="button" class="btn" id="next2" disabled>
                        Next <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </form>
            <p class="foot">Already have an account? <a href="log-in.php">Log in instead</a></p>
        </section>

        <section class="step hidden" data-step="3">
            <h1>How did you find out about ThinkUp?</h1>
            <p class="sub">We'd love to know how you found out about us. Please check all that apply.</p>
            <form class="form" onsubmit="return false">
                <label class="opt">
                    <input type="checkbox" name="ref" value="google" />
                    <span>Google Search</span>
                </label>
                <label class="opt">
                    <input type="checkbox" name="ref" value="facebook" />
                    <span>Facebook</span>
                </label>
                <label class="opt">
                    <input type="checkbox" name="ref" value="school" />
                    <span>School / Teacher Recommendation</span>
                </label>
                <label class="opt">
                    <input type="checkbox" name="ref" value="friends" />
                    <span>Friends / Word of mouth</span>
                </label>
                <div class="actions">
                    <button type="button" class="btn" id="prev3">
                        <i class="bi bi-chevron-left"></i> Previous
                    </button>
                    <button type="button" class="btn primary" id="finishBtn">
                        Sign up for free
                    </button>
                </div>
            </form>
            <p class="foot">Already have an account? <a href="log-in.php">Log in instead</a></p>
        </section>
    </div>

    <script>
        // ========== STATE MANAGEMENT ==========
        const SignUpState = {
            currentStep: 1,
            totalSteps: 3,
            formData: {
                email: '',
                firstName: '',
                lastName: '',
                referrals: []
            }
        };

        // ========== DOM ELEMENTS ==========
        const DOM = {
            steps: document.querySelectorAll('.step'),
            dots: document.querySelectorAll('.dot'),
            progress: document.getElementById('progress'),
            stepper: document.querySelector('.stepper'),
            wrap: document.querySelector('.wrap'),
            
            // Step 1
            email: document.getElementById('email'),
            pw1: document.getElementById('pw1'),
            pw2: document.getElementById('pw2'),
            tos: document.getElementById('tos'),
            next1: document.getElementById('next1'),
            grpPw1: document.getElementById('grpPw1'),
            grpPw2: document.getElementById('grpPw2'),
            pw1Err: document.getElementById('pw1Err'),
            pw2Err: document.getElementById('pw2Err'),
            
            // Step 2
            first: document.getElementById('first'),
            last: document.getElementById('last'),
            next2: document.getElementById('next2'),
            prev2: document.getElementById('prev2'),
            
            // Step 3
            prev3: document.getElementById('prev3'),
            finishBtn: document.getElementById('finishBtn'),
            referralCheckboxes: document.querySelectorAll('input[name="ref"]')
        };

        // ========== VALIDATION FUNCTIONS ==========
        const Validation = {
            isValidPassword: (password) => {
                return password.length >= 6 && (/\d/.test(password) || /[^A-Za-z]/.test(password));
            },

            passwordsMatch: (pw1, pw2) => {
                return pw2.length > 0 && pw1 === pw2;
            },

            isEmailValid: (email) => {
                return email.trim().length > 0;
            },

            isStep1Valid: () => {
                const emailValid = Validation.isEmailValid(DOM.email.value);
                const passwordValid = Validation.isValidPassword(DOM.pw1.value);
                const passwordsMatch = Validation.passwordsMatch(DOM.pw1.value, DOM.pw2.value);
                const termsAccepted = DOM.tos.checked;
                
                return emailValid && passwordValid && passwordsMatch && termsAccepted;
            },

            isStep2Valid: () => {
                return DOM.first.value.trim() && DOM.last.value.trim();
            }
        };

        // ========== UI UPDATES ==========
        const UI = {
            updateStep: (stepNumber) => {
                SignUpState.currentStep = stepNumber;
                
                // Update step visibility
                DOM.steps.forEach(step => {
                    step.classList.toggle('hidden', Number(step.dataset.step) !== stepNumber);
                });
                
                // Update stepper dots
                DOM.dots.forEach(dot => {
                    const dotStep = Number(dot.dataset.step);
                    dot.classList.toggle('done', dotStep <= stepNumber);
                    dot.classList.toggle('current', dotStep === stepNumber);
                });
                
                // Update progress bar
                const offset = 10;
                const progressWidth = ((stepNumber - 1) / (SignUpState.totalSteps - 1)) * (100 - offset * 2);
                DOM.progress.style.width = `${progressWidth}%`;
                
                // Update container width
                DOM.wrap.classList.toggle('wide', stepNumber === 3);
                
                // Update stepper state
                DOM.stepper.classList.toggle('done', stepNumber === SignUpState.totalSteps);
                
                // Scroll to top
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            updateStep1UI: () => {
                const passwordValid = Validation.isValidPassword(DOM.pw1.value);
                const passwordsMatch = Validation.passwordsMatch(DOM.pw1.value, DOM.pw2.value);
                
                // Show/hide error messages
                DOM.pw1Err.classList.toggle('show', DOM.pw1.value.length > 0 && !passwordValid);
                DOM.pw2Err.classList.toggle('show', DOM.pw2.value.length > 0 && !passwordsMatch);
                
                // Update input styles
                DOM.grpPw1.classList.toggle('invalid', DOM.pw1.value.length > 0 && !passwordValid);
                DOM.grpPw2.classList.toggle('invalid', DOM.pw2.value.length > 0 && !passwordsMatch);
                
                // Update button state
                DOM.next1.disabled = !Validation.isStep1Valid();
            },

            updateStep2UI: () => {
                DOM.next2.disabled = !Validation.isStep2Valid();
            },

            togglePasswordVisibility: (targetId) => {
                const targetInput = document.getElementById(targetId);
                const isPassword = targetInput.type === 'password';
                const toggleIcon = document.querySelector(`.toggle[data-target="${targetId}"]`);
                
                // Save cursor position
                const cursorPosition = targetInput.selectionStart;
                
                // Toggle input type
                targetInput.type = isPassword ? 'text' : 'password';
                
                // Update icon
                toggleIcon.classList.toggle('bi-eye', isPassword);
                toggleIcon.classList.toggle('bi-eye-slash-fill', !isPassword);
                
                // Restore cursor position
                requestAnimationFrame(() => {
                    try {
                        targetInput.setSelectionRange(cursorPosition, cursorPosition);
                    } catch (e) {}
                    targetInput.focus({ preventScroll: true });
                });
            }
        };

        // ========== EVENT HANDLERS ==========
        const EventHandlers = {
            handleStep1Input: () => {
                UI.updateStep1UI();
            },

            handleStep2Input: () => {
                UI.updateStep2UI();
            },

            handlePasswordToggle: (event) => {
                if (event.target.classList.contains('toggle')) {
                    const targetId = event.target.dataset.target;
                    UI.togglePasswordVisibility(targetId);
                }
            },

            handleNextStep1: () => {
                if (!DOM.next1.disabled) {
                    UI.updateStep(2);
                }
            },

            handlePrevStep2: () => {
                UI.updateStep(1);
            },

            handleNextStep2: () => {
                if (!DOM.next2.disabled) {
                    UI.updateStep(3);
                }
            },

            handlePrevStep3: () => {
                UI.updateStep(2);
            },

            // --- THIS IS THE UPDATED FUNCTION ---
            handleFinish: async () => {
                // Validate all required fields
                if (!DOM.first.value.trim() || !DOM.last.value.trim() || !DOM.email.value.trim()) {
                    alert('Please complete all required fields.');
                    return;
                }
                
                // Disable button to prevent double clicks
                DOM.finishBtn.disabled = true;
                DOM.finishBtn.textContent = 'Creating Account...';

                // Prepare Data for Backend
                const userData = {
                    email: DOM.email.value.trim(),
                    password: DOM.pw1.value,
                    firstName: DOM.first.value.trim(),
                    lastName: DOM.last.value.trim(),
                    referrals: Array.from(DOM.referralCheckboxes)
                        .filter(checkbox => checkbox.checked)
                        .map(checkbox => checkbox.value)
                };
                
                try {
                    // Send to PHP Backend
                    const response = await fetch('process_signup.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(userData)
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Success: Clear storage and redirect
                        localStorage.removeItem('thinkup_first');
                        localStorage.removeItem('thinkup_last');
                        localStorage.removeItem('thinkup_email');
                        window.location.href = 'home.php';
                    } else {
                        // Error from backend (e.g., email exists)
                        alert(result.message || 'An error occurred.');
                        DOM.finishBtn.disabled = false;
                        DOM.finishBtn.textContent = 'Sign up for free';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Connection error. Please try again.');
                    DOM.finishBtn.disabled = false;
                    DOM.finishBtn.textContent = 'Sign up for free';
                }
            }
        };

        // ========== EVENT LISTENERS ==========
        const setupEventListeners = () => {
            // Step 1 validation listeners
            [DOM.email, DOM.pw1, DOM.pw2, DOM.tos].forEach(element => {
                element.addEventListener('input', EventHandlers.handleStep1Input);
                element.addEventListener('change', EventHandlers.handleStep1Input);
            });
            
            // Password toggle listeners
            document.addEventListener('click', EventHandlers.handlePasswordToggle);
            
            // Step 2 validation listeners
            [DOM.first, DOM.last].forEach(element => {
                element.addEventListener('input', EventHandlers.handleStep2Input);
            });
            
            // Navigation button listeners
            DOM.next1.addEventListener('click', EventHandlers.handleNextStep1);
            DOM.prev2.addEventListener('click', EventHandlers.handlePrevStep2);
            DOM.next2.addEventListener('click', EventHandlers.handleNextStep2);
            DOM.prev3.addEventListener('click', EventHandlers.handlePrevStep3);
            DOM.finishBtn.addEventListener('click', EventHandlers.handleFinish);
        };

        // ========== INITIALIZATION ==========
        const init = () => {
            // Clear any existing data
            ['thinkup_first', 'thinkup_last', 'thinkup_email', 'thinkup_refs'].forEach(key => {
                localStorage.removeItem(key);
            });
            
            // Set initial step
            UI.updateStep(1);
            
            // Setup event listeners
            setupEventListeners();
            
            // Initial validation
            UI.updateStep1UI();
            UI.updateStep2UI();
        };

        // Initialize the application
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>