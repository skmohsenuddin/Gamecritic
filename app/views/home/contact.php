<?php
$title = 'Contact Us | GameCritic';
?>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card bg-dark text-white border-secondary shadow-lg">
                <div class="card-header bg-primary border-secondary">
                    <h1 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </h1>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <p class="lead text-center">
                            Have questions, suggestions, or need support? We're here to help!
                        </p>
                    </div>

                    <div class="mb-4">
                        <h3 class="text-primary mb-3">
                            <i class="fas fa-headset me-2"></i>Get in Touch
                        </h3>
                        <p>
                            If you have any inquiries, feedback, or need assistance, please contact our admin team using the email addresses below:
                        </p>
                    </div>

                    <div class="mb-4">
                        <h4 class="text-info mb-3">
                            <i class="fas fa-user-shield me-2"></i>Admin Contact Information
                        </h4>
                        <?php if (!empty($adminEmails)): ?>
                            <div class="list-group">
                                <?php foreach ($adminEmails as $index => $email): ?>
                                    <div class="list-group-item bg-secondary text-white border-secondary mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-envelope-circle-check text-primary me-3" style="font-size: 1.5rem;"></i>
                                            <div class="flex-grow-1">
                                                <strong>Admin <?php echo $index + 1; ?></strong>
                                                <div>
                                                    <a href="mailto:<?php echo htmlspecialchars($email); ?>" 
                                                       class="text-info text-decoration-none">
                                                        <i class="fas fa-at me-1"></i><?php echo htmlspecialchars($email); ?>
                                                    </a>
                                                </div>
                                            </div>
                                            <a href="mailto:<?php echo htmlspecialchars($email); ?>" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-paper-plane me-1"></i>Send Email
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Admin contact information is currently unavailable. Please try again later.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4 p-3 bg-secondary rounded">
                        <h5 class="text-warning mb-2">
                            <i class="fas fa-info-circle me-2"></i>What to Include in Your Email
                        </h5>
                        <ul class="mb-0">
                            <li>Your username or account email</li>
                            <li>A clear description of your question or issue</li>
                            <li>Any relevant screenshots or details</li>
                            <li>Your preferred method of response</li>
                        </ul>
                    </div>

                    <div class="mb-4 p-3 bg-info bg-opacity-10 rounded border border-info">
                        <h5 class="text-info mb-2">
                            <i class="fas fa-clock me-2"></i>Response Time
                        </h5>
                        <p class="mb-0">
                            We aim to respond to all inquiries within 24-48 hours. For urgent matters, please mark your email as "Urgent" in the subject line.
                        </p>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                        <a href="<?php echo $baseUrl; ?>/about" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Learn More About Us
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .list-group-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
    }
    
    a[href^="mailto:"] {
        word-break: break-all;
    }
</style>

