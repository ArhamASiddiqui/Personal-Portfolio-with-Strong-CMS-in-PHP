<?php
// Includes the database connection file
require_once 'includes/db.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender_name = trim($_POST['name']);
    $sender_email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    $sql = "INSERT INTO contact_messages (sender_name, sender_email, subject, message) VALUES (?, ?, ?, ?)";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $sender_name, $sender_email, $subject, $message);

        if (mysqli_stmt_execute($stmt)) {
            
            // --- START: NEW NOTIFICATION CODE ---
            // Create a notification for the admin after the message is saved
            $notification_message = "New contact message from " . htmlspecialchars($sender_name);
            $notification_link = "/my_portfolio_cms/admin/manage_messages.php";

            $sql_notify = "INSERT INTO notifications (message, link) VALUES (?, ?)";
            if ($stmt_notify = mysqli_prepare($conn, $sql_notify)) {
                mysqli_stmt_bind_param($stmt_notify, "ss", $notification_message, $notification_link);
                mysqli_stmt_execute($stmt_notify);
                mysqli_stmt_close($stmt_notify);
            }
            // --- END: NEW NOTIFICATION CODE ---

            header("Location: contact.php?success=1");
            exit();

        } else {
            header("Location: contact.php?success=0");
            exit();
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>
<?php include 'includes/header.php'; ?>

<!-- FontAwesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<section class="contact-section" id="contact-form-section">
    <div class="container" style="max-width: 700px; margin:auto;">
        <h2 style="text-align:center; margin-bottom:10px;">Contact Me</h2>
        <p style="text-align:center; margin-bottom:25px;">Feel free to get in touch for any inquiries or collaborations.</p>

        <div class="contact-form-card" style="background:rgba(255,255,255,0.05); padding:30px; border-radius:15px; box-shadow:0 8px 20px rgba(0,0,0,0.3);">

            <h3 style="margin-bottom:20px; text-align:center;">Send me a message</h3>

            <!-- Success / Error Messages -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div style="background: rgba(46, 204, 113, 0.15); border: 2px solid #2ecc71; color: #2ecc71; 
                            padding: 20px; border-radius: 15px; text-align: center; font-weight:600; 
                            margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); animation: fadeIn 0.6s ease;">
                    <div style="font-size: 28px; margin-bottom:10px;"><i class="fa-solid fa-circle-check"></i></div>
                    Your message has been sent successfully!
                    <br><br>
                    <a href="index.php" 
                       style="display:inline-block; background: linear-gradient(135deg, #2ecc71, #27ae60); 
                              color:#fff; padding:12px 25px; border-radius:8px; 
                              font-weight:600; text-decoration:none; transition:0.3s; 
                              box-shadow:0 4px 10px rgba(0,0,0,0.2);">
                       <i class="fa-solid fa-house"></i> Back to Home
                    </a>
                </div>
            <?php elseif (isset($_GET['success']) && $_GET['success'] == 0): ?>
                <div style="background: rgba(231, 76, 60, 0.15); border: 2px solid #e74c3c; color: #e74c3c; 
                            padding: 20px; border-radius: 15px; text-align: center; font-weight:600; 
                            margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); animation: fadeIn 0.6s ease;">
                    <div style="font-size: 28px; margin-bottom:10px;"><i class="fa-solid fa-circle-xmark"></i></div>
                    Something went wrong. Please try again.
                    <br><br>
                    <a href="#contact-form-section" 
                       style="display:inline-block; background: linear-gradient(135deg, #e74c3c, #c0392b); 
                              color:#fff; padding:12px 25px; border-radius:8px; 
                              font-weight:600; text-decoration:none; transition:0.3s; 
                              box-shadow:0 4px 10px rgba(0,0,0,0.2);">
                       <i class="fa-solid fa-rotate-right"></i> Try Again
                    </a>
                </div>
            <?php endif; ?>

            <!-- Contact Form -->
            <form method="post" action="contact.php" style="display:flex; flex-direction:column; gap:20px;">
                
                <div class="form-group" style="position:relative;">
                    <label for="name" style="font-weight:600;">Your Name</label>
                    <i class="fa-solid fa-user" style="position:absolute; top:44px; left:12px; color:#999;"></i>
                    <input type="text" id="name" name="name" required
                           style="padding:12px 12px 12px 40px; width:100%; border-radius:8px; border:1px solid #444; 
                                  background:#0b0b25; color:#fff; transition:0.3s;">
                </div>

                <div class="form-group" style="position:relative;">
                    <label for="email" style="font-weight:600;">Your Email</label>
                    <i class="fa-solid fa-envelope" style="position:absolute; top:44px; left:12px; color:#999;"></i>
                    <input type="email" id="email" name="email" required
                           style="padding:12px 12px 12px 40px; width:100%; border-radius:8px; border:1px solid #444; 
                                  background:#0b0b25; color:#fff; transition:0.3s;">
                </div>

                <div class="form-group" style="position:relative;">
                    <label for="subject" style="font-weight:600;">Subject</label>
                    <i class="fa-solid fa-tag" style="position:absolute; top:44px; left:12px; color:#999;"></i>
                    <input type="text" id="subject" name="subject" required
                           style="padding:12px 12px 12px 40px; width:100%; border-radius:8px; border:1px solid #444; 
                                  background:#0b0b25; color:#fff; transition:0.3s;">
                </div>

                <div class="form-group" style="position:relative;">
                    <label for="message" style="font-weight:600;">Message</label>
                    <i class="fa-solid fa-comment-dots" style="position:absolute; top:44px; left:12px; color:#999;"></i>
                    <textarea id="message" name="message" rows="5" required
                              style="padding:12px 12px 12px 40px; width:100%; border-radius:8px; border:1px solid #444; 
                                     background:#0b0b25; color:#fff; transition:0.3s;"></textarea>
                </div>

                <button type="submit" 
                        style="padding:14px; background:linear-gradient(135deg,#00c6ff,#0072ff); 
                               color:#fff; border:none; border-radius:8px; font-weight:600; 
                               cursor:pointer; transition:all 0.3s ease; box-shadow:0 4px 12px rgba(0,0,0,0.2);">
                    <i class="fa-solid fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Inline Animation -->
<style>
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}
input:focus, textarea:focus {
  border-color: #00c6ff !important;
  outline: none;
  box-shadow: 0 0 8px rgba(0,198,255,0.5);
}
button:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}
</style>
