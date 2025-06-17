<?php
//https://www.youtube.com/watch?v=YB2UgIn2jQg
session_start();

// GENERATE RANDOM TAKEN (STRING)
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

?>
<!DOCTYPE html>

<meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>"> <!--necessary for CSRF-TOKENS-->

<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="styles/pantry.css">

<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Pantry Inventory System - Manage your pantry and shopping list easily.">

  <meta charset="UTF-8">
  <link rel="icon" href="icon.ico" type="image/x-icon">
  <!-- I made an Icon in MS Paint that you can download to make it look more personal,
    should be visible if you put it in the same project folder -->
  <title>Pantry Inventory System</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
    header { background: #22B14C; color: rgb(255, 255, 255); padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
    .header-title { display: flex; align-items: center; }
    .header-title img { margin-right: 0.75rem; }
    nav button { margin: 0 0.5rem; padding: 0.5rem 1rem; cursor: pointer; }
    section { padding: 2rem; display: none; }
    #home { display: block; }
    .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
             background: white; padding: 2rem; border: 1px solid #ccc; z-index: 1000; }
    .overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
               background: rgba(0,0,0,0.5); z-index: 999; }
    .calendar { margin-right: 0.5rem; }
    .checked { text-decoration: line-through; color: #888; }
    .modal h3 { margin-top: 0; }
    .modal label { display: block; margin-bottom: 0.5rem; }
    .modal input { width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #ccc; border-radius: 4px; }
    .modal button { padding: 0.5rem 1rem; background: #22B14C; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .modal button:hover { background: #1a8f3c; }
    .modal button:focus { outline: none; box-shadow: 0 0 0 2px rgba(34, 177, 76, 0.5); }
     
    /* dark mode styles look super awesome*/
    body.manual-dark {
        background-color: #121212 !important;
        color: #e0e0e0 !important;
    }   
    
    body.manual-dark header {
    background: #1f1f1f !important;
     color: #dde2d9 !important;
    }
    body.manual-dark section {
    background: transparent !important;
    color: #dde2d9 !important;
    }
    body.manual-dark .modal {
    background: #2c2c2c !important;
    border-color: #565454 !important;
    color: #dde2d9 !important;
    }
    body.manual-dark .item {
    background: #333 !important;
    border-color: #565454 !important;
    color: #dde2d9 !important;
    }
    body.manual-dark .checked {
    color: #bbb !important;
    }
    body.manual-dark nav button {
    background: #565454 !important;
    color: #dde2d9 !important;
    }
    body.manual-dark nav button:hover {
    background: #555 !important;
    }
    body.manual-dark .modal button {
        background: #22B14C !important;
    }
    body.manual-dark .modal button:hover {
        background: #1a8f3c !important;
    }
    body.manual-dark .modal input {
        background: #3c3d3b !important;
        color: #dde2d9 !important;
        border-color: #565454 !important;
    }
    .item.checked {
      opacity: 0.7;
      background-color: #f0f8f0;
    }

    body.manual-dark .item.checked {
    background-color: #2a4a2a !important;
    opacity: 0.8;
    }

    .item.checked .calendar {
        filter: grayscale(50%);
    }


  </style>
</head>
<body>

<header>
  <div class="header-title">
    <a href="https://github.com/nickolasddiaz/CEN-4033-Pantry-Inventory-Management-System">
    <img src="icon.ico" alt="Pantry Icon" width="48" height="48">
    </a>
    <h1 style="margin: 0;">Pantry Manager</h1>
  </div>
  <nav>
    <button onclick="showScreen('home')">Home</button>
    <button onclick="showPantry()">Your Pantry</button>
    <button onclick="showPantry(true)">Your Shopping List</button>
    <button onclick="showScreen('settings')">Settings</button>
    <button onclick="openModal('loginModal')">Login</button>
    <button onclick="openModal('signupModal')">Sign Up</button>
    <button onclick="openModal('forgotPassword')">Forgot Password</button>
    <button onclick="openModal('forgotPasswordConfirm')">Continue to Forgot Password</button>
  </nav>
</header>

<!-- Home Screen -->
<section id="home">
  <h2>Welcome to the Pantry Inventory System</h2>
  <p>This is the project demo! Our system helps you manage your pantry, keep track of items, and set reminders for shopping trips!</p>
    <p>Use the navigation buttons above to explore the features. 
    <a href="https://github.com/nickolasddiaz/CEN-4033-Pantry-Inventory-Management-System">Link to the Codebase</a>
    </p>
    <p>Click "Login" or "Sign Up" to access your pantry and shopping list.</p>
    <p>Enjoy managing your pantry!</p>
    <p>Please give us a 100 :D [dont forget to remove]</p>
</section>

<!-- Pantry Page -->
<section id="pantry"></section>

<!-- Settings Page -->
<section id="settings">
  <h2>Settings</h2>
  <h3>Notification Preferences</h3>
  <p><strong>Expiration Alerts:</strong> Notify me ? days before an item expires.</p>
  <p><strong>Shopping Reminders:</strong> Remind me every Saturday to update my list.</p>
    <h3>Account Settings</h3>
    <button id="changePasswordButton" class="btn btn-toggle" onclick="openModal('PasswordModalSet')">Change Password</button>
  <button id="changeEmailButton" class="btn btn-toggle" onclick="openModal('EmailModalSet')">Change Email</button>
  <button id="logoutButton" class="btn btn-toggle" onclick="logout()">Logout</button>
  <button id="deleteAccountButton" class="btn btn-toggle" onclick="openModal('deleteAcountModal')">Delete Account</button>
  <h3>Appearance</h3>
  <button id="darkModeBtn" class="btn btn-toggle" onclick="toggleDark()">Enable Dark Mode</button>
  <button id="settingButton" class="btn btn-toggle" onclick="saveSettings()">Save Settings</button>
</section>


<!-- Login Modal -->
<div class="overlay" id="overlay" onclick="closeModal()"></div>
<div class="modal" id="loginModal">
  <h3>Login</h3>
  <label>Enter your Email:</label><br>
  <input type="email" id="loginEmail"><br>
  <label>Enter your Password:</label><br>
  <input type="password" id="loginPassword"><br><br>
  <button onclick="login()">Login</button>
</div>

<!-- Delete Acount Modal -->
<div class="overlay" id="overlay" onclick="closeModal()"></div>
<div class="modal" id="deleteAcountModal">
  <h3>DELETE ACOUNT</h3>
  <label>Are you SURE? <p style="color: red;">ඞ</p>Type YES to continue:</label><br>
  <input type="input" id="loginEmailDelete"><br>
  <button onclick="Security.deleteAccount()">Delete</button>
</div>

<!-- Forgot password Modal -->
<div class="overlay" onclick="closeModal()"></div>
<div class="modal" id="forgotPassword">
  <h3>Forgot Email</h3>
  <label>Enter your Email:</label><br>
  <input type="email" id="loginEmailForgot"><br>
  <button onclick="Security.forgotpassword()">Go</button>
</div>

<!-- Forgot password confirm Modal -->
<div class="overlay" onclick="closeModal()"></div>
<div class="modal" id="forgotPasswordConfirm">
  <h3>Forgot Email</h3>
  <label>Enter your Email:</label><br>
  <input type="email" id="loginEmailForgotConfirm"><br>
  <label>Enter your New Password:</label><br>
  <input type="text" id="loginpasswordForgotConfirm"><br>
  <label>Enter your Code:</label><br>
  <input type="text" id="logincodeForgotConfirm"><br>
  <button onclick="Security.forgotpasswordconfirm()">Go</button>
</div>

<!-- Set Email Modal -->
<div class="overlay" onclick="closeModal()"></div>
<div class="modal" id="EmailModalSet">
  <h3>Set New Email</h3>
  <label>Enter your new Email:</label><br>
  <input type="email" id="loginEmailSetNew"><br>
  <button onclick="Security.setemail()">Go</button>
</div>

<!-- Set Password Modal -->
<div class="overlay" onclick="closeModal()"></div>
<div class="modal" id="PasswordModalSet">
  <h3>Set New Password</h3>
  <label>Enter your new Password:</label><br>
  <input type="password" id="loginPasswordSetPassOld"><br><br>
  <button onclick="Security.setPassword()">Go</button>
</div>

<!-- Signup Modal -->
<div class="modal" id="signupModal">
  <h3>Create an acount</h3>
  <label>Create a Email:</label><br>
  <input type="email" id="signupEmail"><br>
  <label>Create a Password:</label><br>
  <input type="password" id="signupPassword"><br><br>
  <button onclick="signup()">Sign Up</button>
</div>

  <!-- Add Item Modal -->
<div class="modal" id="addItemModal">
  <h3>Add Item to Pantry</h3>
  <label>Item Name:</label><br>
  <input type="text" id="itemName"><br>
  <label>Quantity:</label><br>
  <input type="number" id="quantity"><br>
  <label>Expiration Date:</label><br>
  <input type="date" id="expirationDate"><br><br>
  <button onclick="addItem()">Add Item</button>
</div>

  <!-- modify Item Modal -->
<div class="modal" id="modifyItemModal">
  <h3>Modify Item to Pantry</h3>
  <label>Item Name:</label><br>
  <input type="text" id="itemNamemodify"><br>
  <label>Quantity:</label><br>
  <input type="number" id="quantitymodify"><br>
  <label>Expiration Date:</label><br>
  <input type="date" id="expirationDatemodify"><br><br>
  <button id="modifyitem" >Add Item</button>
</div>

<script> 
// Pantry Manager Application - Modularized JavaScript

// ============================================================================
// UTILITIES MODULE
// ============================================================================
const Utils = {
    // Get cookie value by name
    getCookie(name) {
        const cookies = document.cookie.split("; ");
        for (const cookie of cookies) {
            const [cookieName, value] = cookie.split("=");
            if (cookieName === name) return value;
        }
        return null;
    },

    // Set cookie with expiration
    setCookie(name, value, days = 1) {
        const expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + days);
        document.cookie = `${name}=${value}; expires=${expirationDate.toUTCString()}; path=/; secure; SameSite=Strict`;
    },

    // Validate email format
    isValidEmail(email) {
        return email && email.includes('@') && email.includes('.');
    },

    // Show console messages (placeholder for future notification system)
    showMessage(message, type = 'info') {
        console.log(`[${type.toUpperCase()}] ${message}`);
    }
};

// ============================================================================
// SECURITY MODULE
// ============================================================================
const Security = {
    // Check if user is authenticated
    isAuthenticated() {
        return !!Utils.getCookie('JWT_Token');
    },

    logintest() {
        if (this.isAuthenticated()) {
            return true
        } else {
            return false
            Utils.showMessage('You need to login first.', 'error');
            UIManager.openModal('loginModal');
            UIManager.showScreen('home');
        }
    },

    // Logout user by clearing JWT token cookie
    logout() {
        if (!Security.logintest()) {return;}
        const formData = new FormData();
        formData.append('token', Utils.getCookie('JWT_Token'));
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection
        fetch('logout.php', {
            method: 'POST',
            body: formData
        })


        Utils.setCookie('JWT_Token', '', -1);
        Utils.showMessage('You have been logged out.', 'info');
        UIManager.showScreen('home');
    },

    deleteAccount() {
        if (!Security.logintest()) {return;}

        const email = document.getElementById('loginEmailDelete').value;
        if (email.toLowerCase() !== 'yes') {
            Utils.showMessage('Please type "YES" to confirm account deletion.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('token', Utils.getCookie('JWT_Token'));
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        fetch('delete_acount_proccess.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Utils.showMessage('Account deleted successfully.', 'success');
                Security.logout();
            } else {
                Utils.showMessage(`Error: ${result.message}`, 'error');
            }
        })
        .catch(error => {
            Utils.showMessage('Account deleted successfully.', 'success');
            Security.logout();
            console.error('Delete account error:', error);
        });
    },

    forgotpassword(){
        const email = document.getElementById('loginEmailForgot').value;
        if (!Utils.isValidEmail(email)) {
            Utils.showMessage('Please enter a valid email address.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('email', email);
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        fetch('forgot_password_proccess.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Utils.showMessage(`Verification code sent to your email. code = ` + result.verification_code + `', 'success`);
                UIManager.closeModal();
            } else {
                Utils.showMessage(`Error: ${result.message}`, 'error');
            }
        })
        .catch(error => {
            Utils.showMessage('Error sending verification code.', 'error');
            console.error('Forgot password error:', error);
        });
    },
    forgotpasswordconfirm(){
        const email = document.getElementById('loginEmailForgotConfirm').value;
        const password = document.getElementById('loginpasswordForgotConfirm').value;
        const code = document.getElementById('logincodeForgotConfirm').value;

        if (!Utils.isValidEmail(email) || !password || !code) {
            Utils.showMessage('Please fill in all fields correctly.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        formData.append('code', code);
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        fetch('forgot_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Utils.showMessage('Password reset successful.', 'success');
                UIManager.closeModal();
            } else {
                Utils.showMessage(`Error: ${result.message}`, 'error');
            }
        })
        .catch(error => {
            Utils.showMessage('Error resetting password.', 'error');
            console.error('Forgot password confirm error:', error);
        });
    },

    setPassword(){
        const newPassword = document.getElementById('loginPasswordSetPassOld').value;

        if (!newPassword) {
            Utils.showMessage('Please fill in all fields correctly.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('password', newPassword);
        formData.append('token', Utils.getCookie('JWT_Token'));
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        fetch('set_password.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Utils.showMessage('Password changed successfully.', 'success');
                UIManager.closeModal();
                Security.logout();
                UIManager.showScreen('home');
            } else {
                Utils.showMessage(`Error: ${result.message}`, 'error');
            }
        })
        .catch(error => {
            Utils.showMessage('Error changing password.', 'error');
            console.error('Set password error:', error);
        });

    },
    setemail(){
        const newEmail = document.getElementById('loginEmailSetNew').value;
        if (!Utils.isValidEmail(newEmail)) {
            Utils.showMessage('Please enter a valid email address.', 'error');
            return;
        }
        const formData = new FormData();
        formData.append('email', newEmail);
        formData.append('token', Utils.getCookie('JWT_Token'));
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection


        fetch('set_email.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                Utils.showMessage('Email set successfully.', 'success');
                UIManager.closeModal();
                Security.logout();
                UIManager.showScreen('home');
            } else {
                Utils.showMessage(`Error: ${result.message}`, 'error');
            }
        })
        .catch(error => {
            Utils.showMessage('Error setting email.', 'error');
            console.error('Set email error:', error);
        });
    }
};

// ============================================================================
// CSRF UTILITY FUNCTIONS
// ============================================================================
const CSRFUtils = {
    // Get CSRF token from a meta tag or global variable
    getCSRFToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        
        // Fallback to global variable if meta tag not found
        return window.CSRF_TOKEN || '';
    },

    // Add CSRF token to FormData
    addCSRFToken(formData) {
        const token = this.getCSRFToken();
        if (token) {
            formData.append('csrf_token', token);
        }
        return formData;
    }
};

// ============================================================================
// UI MANAGER MODULE
// ============================================================================
const UIManager = {
    // Show specific screen section
    showScreen(screenId) {
        document.querySelectorAll('section').forEach(section => {
            section.style.display = 'none';
        });
        const targetScreen = document.getElementById(screenId);
        if (targetScreen) {
            targetScreen.style.display = 'block';
        }
    },

    // Open modal dialog
    openModal(modalId) {
        const overlay = document.getElementById('overlay');
        const modal = document.getElementById(modalId);
        
        if (overlay && modal) {
            overlay.style.display = 'block';
            modal.style.display = 'block';
        }
    },

    // Close all modals
    closeModal() {
        const overlay = document.getElementById('overlay');
        const modals = document.querySelectorAll('.modal');
        
        if (overlay) overlay.style.display = 'none';
        modals.forEach(modal => modal.style.display = 'none');
    },

    // Clear form inputs
    clearForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => input.value = '');
        }
    },

    // Clear multiple form fields by their IDs
    clearFields(fieldIds) {
        fieldIds.forEach(id => {
            const field = document.getElementById(id);
            if (field) field.value = '';
        });
    }
};

// ============================================================================
// AUTHENTICATION MODULE
// ============================================================================
const Auth = {
    // Validate login/signup input
    validateInput(email, password) {
        if (!email || !password) {
            Utils.showMessage('Please enter both email and password.', 'error');
            return false;
        }
        
        if (!Utils.isValidEmail(email)) {
            Utils.showMessage('Please enter a valid email address.', 'error');
            return false;
        }
        
        if (password.length < 6) {
            Utils.showMessage('Password must be at least 6 characters long.', 'error');
            return false;
        }
        
        return true;
    },

    // Handle user login
    async login() {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;
        
        if (!this.validateInput(email, password)) return;

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        try {
            const response = await fetch('login_process.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorMessage = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
            }

            const result = await response.json();
            
            if (result.success) {
                Utils.showMessage('Login successful! Welcome.', 'success');
                Utils.setCookie('JWT_Token', result.token, 1);
                UIManager.clearFields(['loginEmail', 'loginPassword']);
                UIManager.closeModal();
                UIManager.showScreen('pantry');
                PantryManager.loadPantry();
            } else {
                Utils.showMessage(`Login failed: ${result.message}`, 'error');
            }
        } catch (error) {
            Utils.showMessage('Login error occurred', 'error');
            console.error('Login error:', error);
        }
    },

    // Handle user signup
    async signup() {
        const email = document.getElementById('signupEmail').value;
        const password = document.getElementById('signupPassword').value;
        
        if (!this.validateInput(email, password)) return;

        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        try {
            const response = await fetch('signup_process.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorMessage = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
            }

            const result = await response.json();
            
            if (result.success) {
                Utils.showMessage('Signup successful! Welcome.', 'success');
                UIManager.clearFields(['signupEmail', 'signupPassword']);
                UIManager.closeModal();
                UIManager.showScreen('home');
            } else {
                Utils.showMessage(`Signup failed: ${result.message}`, 'error');
            }
        } catch (error) {
            Utils.showMessage('Signup error occurred', 'error');
            console.error('Signup error:', error);
        }
    },

    // Check if user is authenticated
    isAuthenticated() {
        return !!Utils.getCookie('JWT_Token');
    }
};

// ============================================================================
// PANTRY MANAGER MODULE
// ============================================================================
const PantryManager = {
    // State management
    state: {
        pantryCount: 0,
        selectedCount: 0,
        pantryItemNames: []
    },

    // Reset state
    resetState() {
        this.state.pantryCount = 0;
        this.state.selectedCount = 0;
        this.state.pantryItemNames = [];
    },

    // Check if item already exists in pantry
    itemExists(itemName) {
        return this.state.pantryItemNames.includes(itemName);
    },

    // Load and display pantry items
    async loadPantry(shopping_list = false) {
    if (!Auth.isAuthenticated()) {
        Utils.showMessage('You need to login first.', 'error');
        UIManager.openModal('loginModal');
        return;
    }

    try {
        const result = await this.performCRUD('', 'read');
        
        if (result && result.success) {
            Utils.showMessage('Pantry items retrieved successfully.', 'success');
            this.renderPantryUI(shopping_list);
            UIManager.showScreen('pantry');
            this.resetState();

            if (result.data && result.data.length > 0) {
                // Filter items based on shopping_list parameter
                const filteredItems = shopping_list 
                    ? result.data.filter(item => item.in_shopping_list == 1)
                    : result.data;

                if (filteredItems.length === 0) {
                    const message = shopping_list 
                        ? 'No items in your shopping list.' 
                        : 'No pantry items found.';
                    Utils.showMessage(message, 'info');
                    
                    // Still show the UI with buttons
                    const pantrySection = document.getElementById('pantry');
                    const emptyMessage = document.createElement('p');
                    emptyMessage.textContent = message;
                    emptyMessage.style.textAlign = 'center';
                    emptyMessage.style.color = '#666';
                    emptyMessage.style.fontStyle = 'italic';
                    pantrySection.appendChild(emptyMessage);
                } else {
                    filteredItems.forEach(item => this.appendPantryItem(item, shopping_list));
                }
            } else {
                Utils.showMessage('No pantry items found for the user.', 'info');
            }
        } else {
            Utils.showMessage('No pantry items found for the user.', 'info');
            this.renderPantryUI(shopping_list);
            UIManager.showScreen('pantry');
        }
    } catch (error) {
        Utils.showMessage('Error loading pantry', 'error');
        console.error('Pantry loading error:', error);
    }
},

    // Render pantry UI structure
    renderPantryUI(shopping_list) {
        const pantrySection = document.getElementById('pantry');
        pantrySection.innerHTML = `
            <h1>Your Pantry:</h1>
            <div class="pantry-actions">
            <button onclick="UIManager.openModal('addItemModal')">Add Item</button>
            <button id="selectpantrybutton" onclick="PantryManager.toggleSelectAll()">Select all</button>
            <button id="removepantrybutton" onclick="PantryManager.removeSelected()">Remove Selected</button>
            ${shopping_list ? `
                <button id="markpantryboughtbutton" onclick="PantryManager.markBought()">Mark Pantry Bought</button>
                <button id="removepantryboughtbutton" onclick="PantryManager.markBought(false)">Remove Pantry Bought</button>
                <button id="removeToShoppingListButton" onclick="PantryManager.moveToPantry(false)">Remove from Shopping List</button>
            ` : `
                <button id="moveToShoppingListButton" onclick="PantryManager.moveToPantry()">Add to Shopping List</button>
            `}
            </div>
            
            <div class="pantry-search">
            <input type="text" id="searchnamePantry" placeholder="Search name of items...">
            <input type="number" id="searchquantityPantry" placeholder="Search by quantity...">
            <input type="number" id="searchdatePantry" placeholder="Search by amount of days until expiration...">
            <button id="searchpantrybutton" onclick="PantryManager.searchPantry()">Search Pantry</button>
            <button id="clearsearchbutton" onclick="PantryManager.clearSearch()">Clear Search</button>
            </div>
        `;
    },

// Add new pantry item to display
appendPantryItem(item, isShoppingList = false) {
    const pantrySection = document.getElementById('pantry');
    const itemDiv = document.createElement('div');
    const currentIndex = this.state.pantryCount;
    
    itemDiv.className = 'item';
    itemDiv.id = `pantryitem${currentIndex}`;
    
    // Add purchased styling if item is purchased and we're in shopping list view
    if (isShoppingList && item.purchased == 1) {
        itemDiv.classList.add('checked');
    }
    
    // Create checkbox
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = `pantrybox${currentIndex}`;
    checkbox.dataset.name = item.name;
    checkbox.dataset.quantity = item.quantity;
    checkbox.dataset.expirationDate = item.expiration_date;
    checkbox.dataset.purchased = item.purchased || false;
    checkbox.dataset.inShoppingList = item.in_shopping_list || false;
    checkbox.onclick = () => this.handleItemSelection(checkbox);
    
    // Create edit button
    const editButton = document.createElement('input');
    editButton.type = 'button';
    editButton.value = 'Edit';
    editButton.onclick = () => this.openModifyModal(currentIndex);
    
    // Create status indicator for shopping list
    let statusIndicator = '';
    if (isShoppingList) {
        statusIndicator = item.purchased == 1 
            ? '<span class="status-indicator status-bought">✅ Bought</span>'
            : '<span class="status-indicator status-to-buy">🛒 To Buy</span>';
    }
    
    // COMPLETELY NEW LAYOUT - this creates the horizontal design
    itemDiv.innerHTML = `
        <span class="calendar">📅</span>
        <div class="item-content">
            <div class="item-field-value item-name">${item.name}</div>
            <div class="item-secondary-info">
                <div class="item-info-piece">
                    <span class="item-info-label">Quantity:</span>
                    <span class="item-info-value">${item.quantity}</span>
                </div>
                <div class="item-info-piece">
                    <span class="item-info-label">Expires:</span>
                    <span class="item-info-value">${item.expiration_date}</span>
                </div>
            </div>
        </div>
        ${statusIndicator}
    `;
    
    // Create actions container
    const actionsDiv = document.createElement('div');
    actionsDiv.className = 'item-actions';
    actionsDiv.appendChild(editButton);
    actionsDiv.appendChild(checkbox);
    
    itemDiv.appendChild(actionsDiv);
    pantrySection.appendChild(itemDiv);
    
    this.state.pantryCount++;
    this.state.pantryItemNames.push(item.name);
    this.updateSelectionUI();
},

async markBought(markAsBought = true) {
    const selectedItems = this.getSelectedItemsJSON();
    
    if (!selectedItems || selectedItems === '[]') {
        Utils.showMessage('No items selected.', 'error');
        return;
    }

    // Parse the selected items and update their purchased status
    const items = JSON.parse(selectedItems);
    const updatedItems = items.map(item => ({
        ...item,
        purchased: markAsBought,
        in_shopping_list: 1,
        old_item_name: item.item_name
    }));

    try {
        const result = await this.performCRUD(JSON.stringify(updatedItems), 'update');
        
        if (result && result.success) {
            const action = markAsBought ? 'marked as bought' : 'unmarked as bought';
            Utils.showMessage(`Selected items ${action} successfully.`, 'success');
            
            // Update the visual display of selected items
            const checkboxes = document.querySelectorAll('#pantry input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkbox.dataset.purchased = markAsBought.toString();
                    const itemDiv = checkbox.parentElement;
                    
                    // Add or remove visual styling for purchased items
                    if (markAsBought) {
                        itemDiv.classList.add('checked');
                    } else {
                        itemDiv.classList.remove('checked');
                    }
                }
            });
            
            // Uncheck all items after operation
            this.toggleSelectAll();
        }
    } catch (error) {
        Utils.showMessage('Error updating purchase status', 'error');
        console.error('Mark bought error:', error);
    }
},



async moveToPantry (addToShoppingList = true) {
    const selectedItems = this.getSelectedItemsJSON();
    
    if (!selectedItems || selectedItems === '[]') {
        Utils.showMessage('No items selected.', 'error');
        return;
    }

    // Parse the selected items and update their shopping list status
    const items = JSON.parse(selectedItems);
    const updatedItems = items.map(item => ({
        ...item,
        in_shopping_list: addToShoppingList,
        old_item_name: item.item_name
    }));

    try {
        const result = await this.performCRUD(JSON.stringify(updatedItems), 'update');
        
        if (result && result.success) {
            const action = addToShoppingList ? 'added to shopping list' : 'removed from shopping list';
            Utils.showMessage(`Selected items ${action} successfully.`, 'success');
            
            // Reload the current view to reflect changes
            const isShoppingList = document.getElementById('markpantryboughtbutton') !== null;
            this.loadPantry(isShoppingList);
        }
    } catch (error) {
        Utils.showMessage('Error updating shopping list status', 'error');
        console.error('Move to pantry error:', error);
    }
},

    // Handle individual item selection
    handleItemSelection(checkbox) {
        this.state.selectedCount += checkbox.checked ? 1 : -1;
        this.updateSelectionUI();
    },

    // Toggle select all items
    toggleSelectAll() {
    // Get only visible checkboxes
    const allCheckboxes = document.querySelectorAll('#pantry input[type="checkbox"]');
    const visibleCheckboxes = Array.from(allCheckboxes).filter(checkbox => {
        const itemDiv = checkbox.closest('.item');
        return itemDiv && itemDiv.style.display !== 'none';
    });
    
    if (visibleCheckboxes.length === 0) {
        Utils.showMessage('No items to select.', 'info');
        return;
    }
    
    // Count currently selected visible items
    const selectedVisibleCount = visibleCheckboxes.filter(checkbox => checkbox.checked).length;
    const shouldSelectAll = selectedVisibleCount < visibleCheckboxes.length;
    
    // Update selection for visible items
    visibleCheckboxes.forEach(checkbox => {
        const wasChecked = checkbox.checked;
        checkbox.checked = shouldSelectAll;
        
        // Update selected count
        if (shouldSelectAll && !wasChecked) {
            this.state.selectedCount++;
        } else if (!shouldSelectAll && wasChecked) {
            this.state.selectedCount--;
        }
    });
    
    this.updateSelectionUI();
},
    searchPantry() {
        const searchName = document.getElementById('searchnamePantry').value.toLowerCase().trim();
        const searchQuantity = document.getElementById('searchquantityPantry').value;
        const searchDays = document.getElementById('searchdatePantry').value;
        
        // Get all pantry item divs
        const itemDivs = document.querySelectorAll('#pantry .item');
        
        itemDivs.forEach(itemDiv => {
            const checkbox = itemDiv.querySelector('input[type="checkbox"]');
            if (!checkbox) return;
            
            const itemName = checkbox.dataset.name.toLowerCase();
            const itemQuantity = parseInt(checkbox.dataset.quantity) || 0;
            const expirationDate = new Date(checkbox.dataset.expirationDate);
            const today = new Date();
            const daysUntilExpiration = Math.ceil((expirationDate - today) / (1000 * 60 * 60 * 24));
            
            let shouldShow = true;
            
            // Filter by name (partial match)
            if (searchName && !itemName.includes(searchName)) {
                shouldShow = false;
            }
            
            // Filter by quantity 
            if (searchQuantity && itemQuantity > parseInt(searchQuantity)) {
                shouldShow = false;
            }
            
            // Filter by days until expiration (less than or equal to)
            if (searchDays && daysUntilExpiration > parseInt(searchDays)) {
                shouldShow = false;
            }
            
            // Show or hide the item - CRITICAL: use 'flex' to maintain layout
            itemDiv.style.display = shouldShow ? 'flex' : 'none';
            
            // Uncheck hidden items
            if (!shouldShow && checkbox.checked) {
                checkbox.checked = false;
                this.state.selectedCount--;
            }
        });
        
        // Update the UI after filtering
        this.updateSelectionUI();
        
        // Show message if no items match
        const visibleItems = Array.from(itemDivs).filter(div => div.style.display !== 'none');
        if (visibleItems.length === 0) {
            Utils.showMessage('No items match your search criteria.', 'info');
        }
    },

    // Update selection-related UI elements
    updateSelectionUI() {
    const selectButton = document.getElementById("selectpantrybutton");
    const removeButton = document.getElementById("removepantrybutton");
    
    // Get visible items count
    const allCheckboxes = document.querySelectorAll('#pantry input[type="checkbox"]');
    const visibleCheckboxes = Array.from(allCheckboxes).filter(checkbox => {
        const itemDiv = checkbox.closest('.item');
        return itemDiv && itemDiv.style.display !== 'none';
    });
    
    const visibleCount = visibleCheckboxes.length;
    const selectedVisibleCount = visibleCheckboxes.filter(checkbox => checkbox.checked).length;
    
    if (selectButton) {
        if (visibleCount === 0) {
            selectButton.textContent = 'Select all';
            selectButton.disabled = true;
        } else {
            selectButton.disabled = false;
            selectButton.textContent = selectedVisibleCount < visibleCount ? 'Select all' : 'Unselect all';
        }
    }
    
    if (removeButton) {
        removeButton.style.display = this.state.selectedCount > 0 ? 'inline-block' : 'none';
    }
    
    // Update other shopping list buttons if they exist
    const markBoughtButton = document.getElementById("markpantryboughtbutton");
    const removeBoughtButton = document.getElementById("removepantryboughtbutton");
    const moveToShoppingButton = document.getElementById("moveToShoppingListButton");
    const removeFromShoppingButton = document.getElementById("removeToShoppingListButton");
    
    [markBoughtButton, removeBoughtButton, moveToShoppingButton, removeFromShoppingButton].forEach(button => {
        if (button) {
            button.style.display = this.state.selectedCount > 0 ? 'inline-block' : 'none';
        }
    });
},

    // Open modify item modal
    openModifyModal(itemIndex) {
        const checkbox = document.getElementById(`pantrybox${itemIndex}`);
        
        if (!checkbox) {
            console.error('Pantry item not found:', itemIndex);
            return;
        }

        // Populate modify form with current values
        document.getElementById('itemNamemodify').value = checkbox.dataset.name;
        document.getElementById('itemNamemodify').dataset.name = checkbox.dataset.name;
        document.getElementById('expirationDatemodify').value = checkbox.dataset.expirationDate;
        document.getElementById('quantitymodify').value = checkbox.dataset.quantity;

        // Set up modify button click handler
        document.getElementById('modifyitem').onclick = () => this.modifyItem(itemIndex);
        
        UIManager.openModal('modifyItemModal');
    },

    // Update pantry item display
    updatePantryItemDisplay(itemIndex, itemName, quantity, expirationDate, purchased = false, inShoppingList = false) {
        const checkbox = document.getElementById(`pantrybox${itemIndex}`);
        
        if (!checkbox) {
            console.error('Pantry item not found:', itemIndex);
            return;
        }

        // Update checkbox data
        checkbox.dataset.name = itemName;
        checkbox.dataset.quantity = quantity;
        checkbox.dataset.expirationDate = expirationDate;
        checkbox.dataset.purchased = purchased;
        checkbox.dataset.inShoppingList = inShoppingList;

        // Update the display elements for the NEW layout
        const itemDiv = checkbox.closest('.item');
        const nameElement = itemDiv.querySelector('.item-name');
        const quantityElement = itemDiv.querySelector('.item-info-piece:first-child .item-info-value');
        const expiresElement = itemDiv.querySelector('.item-info-piece:last-child .item-info-value');
        
        if (nameElement) nameElement.textContent = itemName;
        if (quantityElement) quantityElement.textContent = quantity;
        if (expiresElement) expiresElement.textContent = expirationDate;
    },

    // Get selected items as JSON
    getSelectedItemsJSON() {
        const checkboxes = document.querySelectorAll('#pantry input[type="checkbox"]');
        const selectedItems = [];

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedItems.push({
                    item_name: checkbox.dataset.name,
                    quantity: checkbox.dataset.quantity,
                    expiration_date: checkbox.dataset.expirationDate,
                    purchased: checkbox.dataset.purchased === 'true',
                    in_shopping_list: checkbox.dataset.inShoppingList === 'true'
                });
            }
        });

        return JSON.stringify(selectedItems);
    },

    // Add new item to pantry
    async addItem() {
        const itemName = document.getElementById('itemName').value;
        const expirationDate = document.getElementById('expirationDate').value;
        const quantity = document.getElementById('quantity').value;

        if (!itemName || !expirationDate) {
            Utils.showMessage('Please enter both item name and expiration date.', 'error');
            return;
        }

        if (this.itemExists(itemName)) {
            Utils.showMessage('Item already exists in the pantry.', 'error');
            return;
        }

        const items = JSON.stringify([{
            item_name: itemName,
            quantity: quantity,
            expiration_date: expirationDate
        }]);

        try {
            const result = await this.performCRUD(items, 'create');
            
            if (result && result.success) {
                UIManager.clearFields(['itemName', 'expirationDate', 'quantity']);
                
                const newItem = {
                    name: itemName,
                    quantity: quantity,
                    expiration_date: expirationDate
                };
                
                this.appendPantryItem(newItem);
                UIManager.closeModal();
            } else {
                Utils.showMessage('Failed to add item to pantry.', 'error');
            }
        } catch (error) {
            Utils.showMessage('Error adding item', 'error');
            console.error('Add item error:', error);
        }
    },

    // Remove selected items from pantry
    async removeSelected() {
        const selectedItems = this.getSelectedItemsJSON();
        
        if (!selectedItems || selectedItems === '[]') {
            Utils.showMessage('No items selected for removal.', 'error');
            return;
        }

        try {
            const result = await this.performCRUD(selectedItems, 'remove');
            
            if (result && result.success) {
                Utils.showMessage('Selected items removed successfully.', 'success');
                this.loadPantry(); // Refresh pantry view
            }
        } catch (error) {
            Utils.showMessage('Error removing items', 'error');
            console.error('Remove items error:', error);
        }
    },

    // Modify existing pantry item
    async modifyItem(itemIndex, in_shopping_list = false, purchased = false) {
        const itemName = document.getElementById('itemNamemodify').value;
        const expirationDate = document.getElementById('expirationDatemodify').value;
        const quantity = document.getElementById('quantitymodify').value;
        

        const oldItemName = document.getElementById('itemNamemodify').dataset.name;

        if (!oldItemName) {
            Utils.showMessage('No item selected for modification.', 'error');
            return;
        }

        if (!itemName || !expirationDate) {
            Utils.showMessage('Please enter both item name and expiration date.', 'error');
            return;
        }

        if (this.itemExists(itemName) && itemName !== oldItemName) {
            Utils.showMessage('Item already exists in the pantry.', 'error');
            return;
        }

        const items = JSON.stringify([{
            item_name: itemName,
            quantity: quantity,
            expiration_date: expirationDate,
            purchased: purchased,
            in_shopping_list: in_shopping_list,
            old_item_name: oldItemName
        }]);

        try {
            const result = await this.performCRUD(items, 'update');
            
            if (result && result.success) {
                UIManager.clearFields(['itemNamemodify', 'expirationDatemodify', 'quantitymodify']);
                this.updatePantryItemDisplay(itemIndex, itemName, quantity, expirationDate, purchased, in_shopping_list);
                UIManager.closeModal();
            } else {
                Utils.showMessage('Failed to modify item in pantry.', 'error');
            }
        } catch (error) {
            Utils.showMessage('Error modifying item', 'error');
            console.error('Modify item error:', error);
        }
    },

    // Perform CRUD operations on pantry items
    async performCRUD(items, action) {
        const validActions = ['create', 'read', 'update', 'remove'];
        
        if (!validActions.includes(action)) {
            Utils.showMessage('Invalid action specified.', 'error');
            return null;
        }

        const token = Utils.getCookie('JWT_Token');
        if (!token) {
            Utils.showMessage('You need to login first.', 'error');
            UIManager.openModal('loginModal');
            return null;
        }

        const formData = new FormData();
        formData.append('token', token);
        formData.append('action', action);
        CSRFUtils.addCSRFToken(formData); // Add CSRF protection

        if (action !== 'read') {
            if (!items || items.length === 0) {
                Utils.showMessage('No items to process.', 'error');
                return null;
            }
            formData.append('items', items);
        }

        try {
            const response = await fetch('CRUD_pantry_items.php', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorMessage = await response.text();
                throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
            }

            const result = await response.json();
            
            if (result.success) {
                Utils.showMessage(`Item ${action}d successfully.`, 'success');
                return result;
            } else {
                Utils.showMessage(`Failed to ${action} item: ${result.message}`, 'error');
                return null;
            }
        } catch (error) {
            console.error(`Error ${action}ing item:`, error);
            Utils.showMessage(`An error occurred while ${action}ing the item.`, 'error');
            return null;
        }
    },

    // Fixed clearSearch function
    clearSearch() {
        document.getElementById('searchnamePantry').value = '';
        document.getElementById('searchquantityPantry').value = '';
        document.getElementById('searchdatePantry').value = '';
        
        // Show all items using FLEX display to maintain layout
        const itemDivs = document.querySelectorAll('#pantry .item');
        itemDivs.forEach(itemDiv => {
            itemDiv.style.display = 'flex'; // CRITICAL: Use 'flex' not 'block'
        });
        
        this.updateSelectionUI();
    }
};

// ============================================================================
// THEME MANAGER MODULE
// ============================================================================
const ThemeManager = {
    // Initialize theme on page load
    init() {
        const savedTheme = localStorage.getItem('darkMode');
        if (savedTheme === 'enabled') {
            document.body.classList.add('manual-dark');
            this.updateThemeButton(true);
        }
    },

    // Toggle dark mode
    toggleDarkMode() {
        const isDarkMode = document.body.classList.toggle('manual-dark');
        this.updateThemeButton(isDarkMode);
        localStorage.setItem('darkMode', isDarkMode ? 'enabled' : 'disabled');
    },

    // Update theme button text
    updateThemeButton(isDarkMode) {
        const button = document.getElementById('darkModeBtn');
        if (button) {
            button.textContent = isDarkMode ? 'Disable Dark Mode' : 'Enable Dark Mode';
        }
    }
};

// ============================================================================
// GLOBAL FUNCTIONS (for HTML onclick handlers)
// ============================================================================
function logout() { 
    Security.logout(); 
    UIManager.showScreen('home'); 
    PantryManager.resetState(); 
}
function showScreen(id) { UIManager.showScreen(id); }
function openModal(id) { UIManager.openModal(id); }
function closeModal() { UIManager.closeModal(); }
function login() { Auth.login(); }
function signup() { Auth.signup(); }
function showPantry(shoppinglist = false) { PantryManager.loadPantry(shoppinglist); }
function addItem() { PantryManager.addItem(); }
function markBought(checkbox) { ShoppingListManager.markBought(checkbox); }
function toggleDark() { ThemeManager.toggleDarkMode(); }
function markBought(markAsBought = true) { 
    PantryManager.markBought(markAsBought); 
}

function moveToPantry(addToShoppingList = true) { 
    PantryManager.moveToPantry(addToShoppingList); 
}

// ============================================================================
// INITIALIZATION
// ============================================================================
document.addEventListener('DOMContentLoaded', function() {
    ThemeManager.init();
    
    // Add click handler for overlay to close modals
    const overlay = document.getElementById('overlay');
    if (overlay) {
        overlay.addEventListener('click', closeModal);
    }
});

</script>


</body>
</html>