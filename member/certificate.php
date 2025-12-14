<?php
require_once '../config.php';
require_once '../includes/db_helper.php';
require_once '../includes/functions.php';

requireLogin();
$user = getCurrentUser();
$member = getMemberByUserId($user['id']);

// Redirect back if not an active member
if (!$member || ($member['membership_status'] ?? '') !== 'active') {
    setFlash('Certificate is available only for active members.', 'error');
    header('Location: dashboard.php');
    exit;
}

// Load current officers for signatures
$officers = getAllOfficers();
$president = null;
$adviser = null;
foreach ($officers as $off) {
    if (stripos($off['position'], 'president') !== false) $president = $off;
    if (stripos($off['position'], 'adviser') !== false || stripos($off['position'], 'advisor') !== false) $adviser = $off;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Certificate - JPCS</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/pages.css">
    <style>
        /* Certificate Page Layout */
        body {
            background-color: #f3f4f6;
        }

        .certificate-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px); /* Adjust for header */
            padding: 40px 20px;
        }

        /* The Certificate Paper */
        .certificate-paper {
            width: 100%;
            max-width: 950px; /* Approx landscape A4 ratio width */
            background: #fff;
            position: relative;
            padding: 50px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            color: #333;
            text-align: center;
            border: 15px solid #fff; /* Inner white spacing */
            outline: 2px solid #ff6a00; /* Thin inner orange line */
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        /* Thick Outer Border */
        .certificate-border {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 15px solid #ff6a00;
            pointer-events: none;
            z-index: 1;
        }

        /* Decorative Corners */
        .corner {
            position: absolute;
            width: 60px;
            height: 60px;
            z-index: 2;
            border: 4px solid #e05e00;
        }
        .corner-tl { top: 25px; left: 25px; border-right: none; border-bottom: none; }
        .corner-tr { top: 25px; right: 25px; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 25px; left: 25px; border-right: none; border-top: none; }
        .corner-br { bottom: 25px; right: 25px; border-left: none; border-top: none; }

        /* Watermark Background */
        .certificate-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background-image: url('../assets/images/LOGO.png');
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            opacity: 0.04;
            pointer-events: none;
            z-index: 0;
        }

        /* Typography */
        .cert-org {
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 4px;
            color: #555;
            margin-bottom: 10px;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .cert-title {
            font-family: var(--font-primary); /* Uses Poppins from global css */
            font-size: 3.5rem;
            font-weight: 800;
            color: #ff6a00;
            text-transform: uppercase;
            line-height: 1;
            margin: 10px 0 30px;
            letter-spacing: -1px;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 0px rgba(0,0,0,0.05);
        }

        .cert-body {
            font-size: 1.25rem;
            color: #444;
            margin-bottom: 20px;
            font-style: italic;
            position: relative;
            z-index: 2;
        }

        .cert-name {
            font-family: "Georgia", serif; /* Serif fits certificates better for names */
            font-size: 3rem;
            color: #1a1a1a;
            font-weight: 700;
            margin: 20px 0 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
            display: inline-block;
            min-width: 60%;
            position: relative;
            z-index: 2;
        }

        .cert-desc {
            font-size: 1.1rem;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto 40px;
            color: #666;
            position: relative;
            z-index: 2;
        }

        /* Details Grid */
        .cert-details {
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 20px;
            position: relative;
            z-index: 2;
        }

        .detail-item h4 {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 5px;
        }

        .detail-item p {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ff6a00;
        }

        /* Seal */
        .cert-seal {
            margin-top: 40px;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        
        .seal-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #ff6a00;
            color: #ff6a00;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 0.8rem;
            transform: rotate(-15deg);
            margin: 0 auto;
            background: rgba(255, 106, 0, 0.05);
            box-shadow: 0 0 0 4px #fff, 0 0 0 6px #ff6a00;
        }

        /* Print Settings */
        @media print {
            @page {
                size: landscape;
                margin: 0;
            }
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .certificate-container {
                padding: 0;
                height: 100vh;
                display: block;
            }
            .certificate-paper {
                box-shadow: none;
                max-width: 100%;
                height: 100%;
                border: none;
                /* Ensure background colors print */
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            /* Hide UI Elements */
            header, footer, .no-print, .btn, .top-bar, #tawkchat-container {
                display: none !important;
            }
            /* Force orange borders to show */
            .certificate-border {
                border-color: #ff6a00 !important;
            }
            .cert-title, .detail-item p {
                color: #ff6a00 !important;
            }
        }
    </style>
</head>
<body class="inner-page">

<div class="no-print">
    <?php include '../includes/header.php'; ?>
</div>

<div class="certificate-container">
    
    <!-- Action Buttons -->
    <div style="margin-bottom: 30px;" class="no-print">
        <a href="dashboard.php" class="btn btn-outline" style="margin-right: 10px;">
            ← Back to Dashboard
        </a>
        <button class="btn btn-primary" onclick="window.print();">
            Download / Print Certificate 🖨️
        </button>
    </div>

    <!-- Certificate Type Controls -->
    <?php
    // Allow pre-filling via GET parameters
    $cert_type = isset($_GET['type']) && $_GET['type'] === 'recognition' ? 'recognition' : 'membership';
    $recognition_reason = isset($_GET['reason']) ? trim($_GET['reason']) : '';
    ?>
    <div class="no-print" style="margin-bottom: 20px; display:flex; gap:12px; align-items:center;">
        <label style="display:flex; gap:8px; align-items:center;">
            <strong>Certificate:</strong>
            <select id="cert-type" style="padding:6px 8px;">
                <option value="membership" <?php echo $cert_type === 'membership' ? 'selected' : ''; ?>>Certificate of Membership</option>
                <option value="recognition" <?php echo $cert_type === 'recognition' ? 'selected' : ''; ?>>Certificate of Recognition</option>
            </select>
        </label>

        <label id="reason-label" style="display:flex; gap:8px; align-items:center; <?php echo $cert_type === 'recognition' ? '' : 'display:none;'; ?>">
            <strong>For:</strong>
            <input id="reason-input" type="text" placeholder="Reason or achievement" value="<?php echo htmlspecialchars($recognition_reason); ?>" style="padding:6px 8px; width:360px;" />
        </label>
    </div>

    <div class="certificate-paper">
        <div class="certificate-border"></div>
        <div class="certificate-watermark"></div>
        
        <!-- Decorative Corners -->
        <div class="corner corner-tl"></div>
        <div class="corner corner-tr"></div>
        <div class="corner corner-bl"></div>
        <div class="corner corner-br"></div>

        <div class="cert-org">Junior Philippine Computer Society</div>
        <div class="cert-title">Certificate of Membership</div>
        
        <div class="cert-body">This certifies that</div>
        
        <div class="cert-name">
            <?php echo htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')); ?>
        </div>
        
        <div class="cert-desc">
            is hereby recognized as an official active member of the <strong>JPCS Malvar Chapter</strong>
            for the academic year <?php echo date('Y') . '-' . (date('Y')+1); ?>. This certification grants all rights,
            privileges, and responsibilities appertaining thereto.
        </div>

        <div class="cert-details">
            <div class="detail-item detail-member-id">
                <h4>Member ID</h4>
                <p><?php echo htmlspecialchars($member['member_id'] ?? 'N/A'); ?></p>
            </div>
            <div class="detail-item detail-membership-status">
                <h4>Membership Status</h4>
                <p style="color: #27ae60;">ACTIVE</p>
            </div>
            <div class="detail-item detail-valid-until">
                <h4>Valid Until</h4>
                <p><?php echo formatDate($member['expiry_date'] ?? date('Y-m-d')); ?></p>
            </div>
        </div>

        <!-- Recognition Credentials (hidden for membership certificate) -->
        <div class="cert-credentials" style="display:none; margin-top:20px;">
            <div style="display:flex; justify-content:center; gap:80px; align-items:flex-end;">
                <div style="text-align:center; width:260px;">
                    <div style="height:60px;">
                        <?php if ($president && !empty($president['photo'])): ?>
                            <img src="../assets/profiles/<?php echo htmlspecialchars($president['photo']); ?>" alt="<?php echo htmlspecialchars($president['name']); ?>" style="max-height:60px; object-fit:contain;" />
                        <?php else: ?>
                            <div style="height:60px"></div>
                        <?php endif; ?>
                    </div>
                    <div style="border-top:1px solid #333; margin-top:12px; padding-top:8px; font-weight:700;">
                        <?php echo htmlspecialchars($president['name'] ?? 'President'); ?>
                    </div>
                    <div style="font-size:0.85rem; color:#666; margin-top:4px;">President</div>
                </div>

                <div style="text-align:center; width:260px;">
                    <div style="height:60px;">
                        <?php if ($adviser && !empty($adviser['photo'])): ?>
                            <img src="../assets/profiles/<?php echo htmlspecialchars($adviser['photo']); ?>" alt="<?php echo htmlspecialchars($adviser['name']); ?>" style="max-height:60px; object-fit:contain;" />
                        <?php else: ?>
                            <div style="height:60px"></div>
                        <?php endif; ?>
                    </div>
                    <div style="border-top:1px solid #333; margin-top:12px; padding-top:8px; font-weight:700;">
                        <?php echo htmlspecialchars($adviser['name'] ?? 'Adviser'); ?>
                    </div>
                    <div style="font-size:0.85rem; color:#666; margin-top:4px;">Chapter Adviser</div>
                </div>
            </div>
        </div>

        <div class="cert-seal">
            <div class="seal-circle">
                Official<br>Member
            </div>
        </div>
        
        <div style="font-size: 0.8rem; color: #999; margin-top: 20px;">
            Verified by JPCS Malvar Chapter System on <?php echo date('F j, Y'); ?>
        </div>
    </div>
</div>

<script>
    (function(){
        // Elements
        const certType = document.getElementById('cert-type');
        const reasonLabel = document.getElementById('reason-label');
        const reasonInput = document.getElementById('reason-input');
        const titleEl = document.querySelector('.cert-title');
        const bodyEl = document.querySelector('.cert-body');
        const descEl = document.querySelector('.cert-desc');
        const sealEl = document.querySelector('.seal-circle');

        function updateForMembership(){
            titleEl.textContent = 'Certificate of Membership';
            bodyEl.textContent = 'This certifies that';
            descEl.innerHTML = 'is hereby recognized as an official active member of the <strong>JPCS Malvar Chapter</strong> for the academic year <?php echo date('Y') . "-" . (date('Y')+1); ?>. This certification grants all rights, privileges, and responsibilities appertaining thereto.';
            sealEl.innerHTML = 'Official<br>Member';
        }

        function updateForRecognition(reason){
            titleEl.textContent = 'Certificate of Recognition';
            bodyEl.textContent = 'This is to certify that';
            const safeReason = reason && reason.trim() !== '' ? reason : 'outstanding service and dedication to the JPCS Malvar Chapter';
            descEl.innerHTML = 'is hereby recognized <strong>for ' + escapeHtml(safeReason) + '</strong> by the <strong>JPCS Malvar Chapter</strong>.';
            sealEl.innerHTML = 'Recognition';
        }

        function escapeHtml(text){
            return text.replace(/[&"'<>]/g, function(match){
                return ({'&':'&amp;','"':'&quot;','\'':'&#39;','<':'&lt;','>':'&gt;'})[match];
            });
        }

        function update(){
            const type = certType.value;
            const details = document.querySelector('.cert-details');
            const creds = document.querySelector('.cert-credentials');
            if(type === 'recognition'){
                reasonLabel.style.display = 'flex';
                if(details) details.style.display = 'none';
                if(creds) creds.style.display = 'block';
                updateForRecognition(reasonInput.value);
            } else {
                reasonLabel.style.display = 'none';
                if(details) details.style.display = 'flex';
                if(creds) creds.style.display = 'none';
                updateForMembership();
            }
        }

        // Initial state (prefilled by server-side variables if any)
        update();

        certType.addEventListener('change', update);
        reasonInput.addEventListener('input', update);
    })();
</script>

<div class="no-print">
    <footer>
        <p>&copy; 2025 JPCS Malvar Chapter. All Rights Reserved.</p>
    </footer>
</div>

</body>
</html>