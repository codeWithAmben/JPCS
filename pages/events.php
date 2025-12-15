<?php 
require_once '../config.php'; 
require_once '../includes/db_helper.php';
require_once '../includes/functions.php';

$events = getAllEvents();
$user = getCurrentUser();
$userId = $user ? $user['id'] : null;

// Filter active events
$activeEvents = array_filter($events, fn($e) => ($e['status'] ?? 'active') === 'active');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - JPCS Malvar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/events.css">
</head>

<body class="inner-page">

<?php include '../includes/header.php'; ?>

<section class="events-section">
    <h1 class="anton-font">Upcoming Events</h1>
    
    <div class="events-grid">
        <?php if (empty($activeEvents)): ?>
            <p class="text-center text-muted">No upcoming events at the moment.</p>
        <?php else: ?>
            <?php foreach ($activeEvents as $event): 
                $isRegistered = $userId ? isUserRegisteredForEvent($userId, $event['id']) : false;
                $fee = (float)($event['registration_fee'] ?? 0);
                $isFree = $fee == 0;
            ?>
            <div class="event-card">
                <div class="event-card-header">
                    <span class="event-category"><?php echo htmlspecialchars($event['category']); ?></span>
                    <?php if ($isRegistered): ?>
                        <span class="badge badge-success">Registered</span>
                    <?php elseif ($isFree): ?>
                        <span class="badge badge-info">Free</span>
                    <?php else: ?>
                        <span class="badge badge-warning">₱<?php echo number_format($fee, 2); ?></span>
                    <?php endif; ?>
                </div>
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                <div class="event-meta">
                    <p><i data-lucide="calendar"></i> <?php echo formatDate($event['date']); ?> at <?php echo formatTime($event['time']); ?></p>
                    <p><i data-lucide="map-pin"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                </div>
                <p class="event-desc"><?php echo htmlspecialchars($event['description']); ?></p>
                
                <div class="event-actions">
                    <?php if ($userId): ?>
                        <?php if ($isRegistered): ?>
                            <button class="btn btn-secondary" disabled>Already Registered</button>
                        <?php else: ?>
                            <button class="btn btn-primary" onclick="openRegistrationModal('<?php echo $event['id']; ?>', '<?php echo htmlspecialchars(addslashes($event['title'])); ?>', <?php echo $fee; ?>)">
                                Register Now
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="../login.php" class="btn btn-outline">Login to Register</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Registration Modal -->
<div id="registrationModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Register for <span id="modalEventTitle"></span></h2>
        <form id="registrationForm" enctype="multipart/form-data">
            <input type="hidden" id="modalEventId" name="event_id">
            
            <div id="modalMessage" class="alert" style="display:none; margin-bottom: 15px;"></div>

            <div id="paymentSection" style="display:none;">
                <div class="alert alert-info">
                    <strong>Registration Fee: ₱<span id="modalEventFee"></span></strong><br>
                    Please send payment to GCash: <strong>0912 345 6789 (JPCS Treasurer)</strong> and upload the screenshot below.
                </div>
                <div class="form-group">
                    <label for="payment_proof">Upload Payment Proof (Screenshot) <span class="required">*</span></label>
                    <input type="file" id="payment_proof" name="payment_proof" accept="image/*" class="form-control">
                </div>
            </div>
            
            <div id="freeSection">
                <p>This event is free. Click confirm to register.</p>
            </div>

            <div class="form-actions">
                <button type="submit" id="confirmRegBtn" class="btn btn-primary">
                    <span class="btn-text">Confirm Registration</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const modal = document.getElementById("registrationModal");
    const closeBtn = document.querySelector(".close-modal");
    const form = document.getElementById('registrationForm');
    const messageDiv = document.getElementById('modalMessage');
    const submitBtn = document.getElementById('confirmRegBtn');
    
    function openRegistrationModal(id, title, fee) {
        document.getElementById('modalEventId').value = id;
        document.getElementById('modalEventTitle').textContent = title;
        document.getElementById('modalEventFee').textContent = fee.toFixed(2);
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
        
        if (fee > 0) {
            document.getElementById('paymentSection').style.display = 'block';
            document.getElementById('freeSection').style.display = 'none';
            document.getElementById('payment_proof').required = true;
        } else {
            document.getElementById('paymentSection').style.display = 'none';
            document.getElementById('freeSection').style.display = 'block';
            document.getElementById('payment_proof').required = false;
        }
        modal.style.display = "block";
    }

    closeBtn.onclick = () => modal.style.display = "none";
    window.onclick = (e) => { if (e.target == modal) modal.style.display = "none"; }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Client-side validation
        const fee = parseFloat(document.getElementById('modalEventFee').textContent);
        const proofInput = document.getElementById('payment_proof');
        if (fee > 0 && (!proofInput.files || proofInput.files.length === 0)) {
            messageDiv.className = 'alert alert-danger';
            messageDiv.textContent = 'Please upload a payment proof screenshot.';
            messageDiv.style.display = 'block';
            return;
        }

        const formData = new FormData(this);
        const originalBtnText = submitBtn.querySelector('.btn-text').textContent;
        submitBtn.disabled = true;
        submitBtn.querySelector('.btn-text').textContent = 'Submitting...';

        fetch('../handlers/event_register.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => { 
                messageDiv.textContent = d.message;
                if (d.success) {
                    messageDiv.className = 'alert alert-success';
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    messageDiv.className = 'alert alert-danger';
                }
                messageDiv.style.display = 'block';
            })
            .catch(e => {
                messageDiv.className = 'alert alert-danger';
                messageDiv.textContent = 'A network error occurred. Please try again.';
                messageDiv.style.display = 'block';
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.querySelector('.btn-text').textContent = originalBtnText;
            });
    });
</script>

<footer>
    <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
</footer>

<?php include '../includes/tawk_chat.php'; ?>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="../js/script.js"></script>
<script>lucide.createIcons();</script>
</body>
</html>
