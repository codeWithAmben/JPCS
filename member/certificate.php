<?php
require_once '../config.php';
require_once INCLUDES_PATH . '/db_helper.php';
require_once INCLUDES_PATH . '/functions.php';
requireLogin();

$user = getCurrentUser();
$member = getMemberByUserId($user['id']);

// Ensure member is active
if (!$member || ($member['membership_status'] ?? '') !== 'active') {
    setFlash('You must be an active member to view your certificate.', 'error');
    redirect('dashboard.php');
    exit;
}

// Load officers for signatures
$officers = getAllOfficers();
$president = null; $adviser = null;
foreach ($officers as $off) {
    if (stripos($off['position'] ?? '', 'president') !== false) $president = $off;
    if (stripos($off['position'] ?? '', 'adviser') !== false || stripos($off['position'] ?? '', 'advisor') !== false) $adviser = $off;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Membership Certificate - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/member.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* Simplified, valid certificate styles */
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;600;800&display=swap');
        body { background:#f3f4f6; }
        .certificate-container{max-width:1000px;margin:30px auto;padding:20px}
        .certificate-paper{background:#fff;border:10px solid #fff;outline:6px solid #ff6a00;padding:30px;box-shadow:0 10px 30px rgba(0,0,0,0.12)}
        .certificate-inner{background:#fff;padding:30px;border:2px solid #ff6a00;text-align:center}
        .cert-title{font-family:'Playfair Display',serif;font-size:2.6rem;color:#2c3e50;margin:10px 0}
        .member-name{font-family:'Great Vibes',cursive;font-size:3.8rem;color:#2c3e50;margin:12px 0}
        .cert-body,.cert-desc{font-family:'Playfair Display',serif;color:#555;margin:10px 0}
        .cert-details{display:flex;justify-content:center;gap:40px;margin-top:20px;flex-wrap:wrap}
        .detail-item{background:rgba(255,255,255,0.9);padding:10px 18px;border-radius:8px}
        .cert-footer{display:flex;justify-content:space-around;margin-top:40px}
        .signature-line{width:200px;border-top:2px solid #2c3e50;margin:0 auto 8px}
        .print-actions{margin-top:20px;text-align:center;display:flex;gap:12px;justify-content:center}
        @media print{.no-print{display:none!important}} 
    </style>
</head>
<body class="inner-page">

<?php include INCLUDES_PATH . '/header.php'; ?>

<div class="certificate-container">
    <div class="no-print" style="margin-bottom:16px;display:flex;gap:10px;align-items:center;justify-content:center;">
        <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
        <button class="btn btn-primary" onclick="window.print()">Download / Print Certificate 🖨️</button>
        <button class="btn btn-outline" onclick="downloadCertificate()">Download Image</button>
    </div>

    <div class="certificate-paper">
        <div class="certificate-inner" id="certificate">
            <img src="../assets/images/LOGO.png" alt="Logo" style="width:100px;margin-bottom:8px;">
            <div class="cert-title">Certificate of Membership</div>
            <div class="cert-body">This is to certify that</div>
            <div class="member-name"><?php echo htmlspecialchars(($member['first_name'] ?? '') . ' ' . ($member['last_name'] ?? '')); ?></div>
            <div class="cert-desc">is an active member of the <strong>JPCS Malvar Chapter</strong> for the academic year <?php echo date('Y'); ?>-<?php echo date('Y', strtotime('+1 year')); ?>.</div>

            <div class="cert-details">
                <div class="detail-item"><div style="font-size:0.85rem;color:#888">Member ID</div><div style="font-weight:700"><?php echo htmlspecialchars($member['member_id'] ?? 'N/A'); ?></div></div>
                <div class="detail-item"><div style="font-size:0.85rem;color:#888">Valid Until</div><div style="font-weight:700"><?php echo formatDate($member['expiry_date'] ?? date('Y-m-d')); ?></div></div>
            </div>

            <div class="cert-footer">
                <div style="text-align:center">
                    <div class="signature-line"></div>
                    <div style="font-weight:700"><?php echo htmlspecialchars($president['name'] ?? 'President'); ?></div>
                    <div style="font-size:0.85rem;color:#777">President</div>
                </div>
                <div style="text-align:center">
                    <div class="signature-line"></div>
                    <div style="font-weight:700"><?php echo htmlspecialchars($adviser['name'] ?? 'Adviser'); ?></div>
                    <div style="font-size:0.85rem;color:#777">Chapter Adviser</div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="text-align:center;margin-top:12px;color:#999;font-size:0.85rem;">Verified by JPCS Malvar Chapter System on <?php echo date('F j, Y'); ?></div>
</div>

<script>
    lucide.createIcons();
    function downloadCertificate(){
        const cert = document.getElementById('certificate');
        html2canvas(cert, {scale:2,useCORS:true,backgroundColor:'#ffffff'}).then(canvas=>{
            const link=document.createElement('a');
            link.download='JPCS_Membership_Certificate.png';
            link.href=canvas.toDataURL('image/png');
            link.click();
        });
    }
</script>
</body>
</html>
