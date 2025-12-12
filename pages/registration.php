<?php 
require_once '../config.php'; 

// Get session data for auto-fill (if user came from SSO or has session)
$prefillEmail = $_SESSION['email'] ?? $_SESSION['user_email'] ?? $_GET['email'] ?? '';
$prefillFirstName = $_SESSION['first_name'] ?? '';
$prefillLastName = $_SESSION['last_name'] ?? '';
$prefillPhone = $_SESSION['phone'] ?? '';

// If user has a full name but not first/last, try to split it
if (empty($prefillFirstName) && !empty($_SESSION['user_name'])) {
    $nameParts = explode(' ', $_SESSION['user_name'], 2);
    $prefillFirstName = $nameParts[0] ?? '';
    $prefillLastName = $nameParts[1] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/registration.css">
</head>

<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="registration-section">
    <h1 class="anton-font">Member Registration</h1>
    <p class="registration-intro">
        Join the JPCS Malvar Chapter family! Fill out the form below to start your membership application. 
        Fields marked with <span class="required">*</span> are required.
    </p>

    <div class="info-note">
        <strong>📝 Note:</strong> Please ensure all information provided is accurate. You will receive a verification email to activate your account.
    </div>

    <!-- Success/Error Messages -->
    <div id="messageBox" class="message-box" style="display: none;"></div>

    <form class="registration-form" id="registrationForm" method="POST">
        
        <div class="form-section">
            <h2>Account Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="your.email@batstate-u.edu.ph" value="<?php echo htmlspecialchars($prefillEmail); ?>" <?php echo !empty($prefillEmail) ? 'readonly' : ''; ?>>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password <span class="required">*</span></label>
                    <input type="password" id="password" name="password" required minlength="8" placeholder="Minimum 8 characters">
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="confirmPassword" name="confirm_password" required placeholder="Confirm your password">
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h2>Personal Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name <span class="required">*</span></label>
                    <input type="text" id="firstName" name="first_name" required placeholder="Enter your first name" value="<?php echo htmlspecialchars($prefillFirstName); ?>">
                </div>
                <div class="form-group">
                    <label for="middleName">Middle Name</label>
                    <input type="text" id="middleName" name="middle_name" placeholder="Enter your middle name">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name <span class="required">*</span></label>
                    <input type="text" id="lastName" name="last_name" required placeholder="Enter your last name" value="<?php echo htmlspecialchars($prefillLastName); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="birthdate">Date of Birth <span class="required">*</span></label>
                    <input type="date" id="birthdate" name="birthdate" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender <span class="required">*</span></label>
                    <select id="gender" name="gender" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer-not-to-say">Prefer not to say</option>
                    </select>
                </div>
            </div>
        </div>
            
        <div class="form-section">
            <h2>Contact Information</h2>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" required placeholder="09XX XXX XXXX" value="<?php echo htmlspecialchars($prefillPhone); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Complete Address <span class="required">*</span></label>
                <textarea id="address" name="address" required placeholder="Enter your complete address"></textarea>
            </div>
        </div>

        <div class="form-section">
            <h2>Academic Information</h2>
            
            <div class="form-group">
                <label for="school">School/Campus <span class="required">*</span></label>
                <input type="text" id="school" name="school" required value="Batangas State University TNEU - JPLPC Malvar" placeholder="Your school">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="studentId">SR-CODE <span class="required">*</span></label>
                    <input type="text" id="studentId" name="student_id" required placeholder="e.g., 25-76345">
                </div>
                <div class="form-group">
                    <label for="yearLevel">Year Level <span class="required">*</span></label>
                    <select id="yearLevel" name="year_level" required>
                        <option value="">Select year level</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="course">Course/Program <span class="required">*</span></label>
                <select id="course" name="course" required>
                    <option value="">Select your course</option>
                    <option value="BSCS">BS Computer Science</option>
                    <option value="BSIT">BS Information Technology</option>
                    <option value="BSIS">BS Information Systems</option>
                    <option value="Other">Other (IT-related)</option>
                </select>
            </div>
        </div>

        <div class="checkbox-group">
            <div class="checkbox-item">
                <input type="checkbox" id="terms" required>
                <label for="terms">I agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a> of JPCS Malvar Chapter. <span class="required">*</span></label>
            </div>
            <div class="checkbox-item">
                <input type="checkbox" id="newsletter" name="newsletter">
                <label for="newsletter">I would like to receive newsletters and updates about JPCS events and activities.</label>
            </div>
        </div>

        <button type="submit" class="submit-btn" id="submitBtn">
            <span class="btn-text">Submit Registration</span>
            <span class="btn-loading" style="display: none;">Submitting...</span>
        </button>
    </form>
</section>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="../js/script.js"></script>
<script>
// Registration Form Handler
document.getElementById('registrationForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = document.getElementById('submitBtn');
    const messageBox = document.getElementById('messageBox');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoading = submitBtn.querySelector('.btn-loading');
    
    // Validate password match
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (password !== confirmPassword) {
        showMessage('Passwords do not match!', 'error');
        return;
    }
    
    // Disable button and show loading
    submitBtn.disabled = true;
    btnText.style.display = 'none';
    btnLoading.style.display = 'inline';
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch('../handlers/register.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            
            // Redirect to verification page after 2 seconds
            if (result.redirect) {
                setTimeout(() => {
                    window.location.href = result.redirect;
                }, 2000);
            }
        } else {
            if (result.errors) {
                showMessage(result.errors.join('<br>'), 'error');
            } else {
                showMessage(result.message || 'Registration failed. Please try again.', 'error');
            }
        }
    } catch (error) {
        console.error('Registration error:', error);
        showMessage('An error occurred. Please try again later.', 'error');
    } finally {
        submitBtn.disabled = false;
        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
    }
});

function showMessage(message, type) {
    const messageBox = document.getElementById('messageBox');
    messageBox.innerHTML = message;
    messageBox.className = 'message-box ' + type;
    messageBox.style.display = 'block';
    messageBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>

<style>
.message-box {
    padding: 15px 20px;
    border-radius: 10px;
    margin: 20px 0;
    font-weight: 500;
}
.message-box.success {
    background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
    color: #2e7d32;
    border: 1px solid #a5d6a7;
}
.message-box.error {
    background: linear-gradient(135deg, #ffebee, #ffcdd2);
    color: #c62828;
    border: 1px solid #ef9a9a;
}
.submit-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
</body>
</html>
