<?php
$title = 'Report Bug | GameCritic';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h2><i class="fas fa-bug me-2"></i>Report a Bug</h2>
                        <p class="text-muted">Help us improve GameCritic by reporting any issues you find.</p>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form action="<?php echo $baseUrl; ?>/report_bug" method="post">
                        <div class="mb-3">
                            <label for="bug_type" class="form-label fw-bold">Bug Type</label>
                            <select name="bug_type" id="bug_type" class="form-select" 
                                    style="background: #1a1a2e; color: white; border-color: #444;" required>
                                <option value="" disabled selected>Select bug category</option>
                                <option value="UI/Design">UI/Design Issue</option>
                                <option value="Functionality">Functional Bug</option>
                                <option value="Database">Database Error</option>
                                <option value="Suggestion AI">AI Suggestion Error</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Your Name</label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   placeholder="Enter your name" 
                                   value="<?php echo isset($currentUser) ? htmlspecialchars($currentUser['username'] ?? '') : ''; ?>"
                                   style="background: #1a1a2e; color: white; border-color: #444;" required>
                        </div>

                        <div class="mb-4">
                            <label for="fix_details" class="form-label fw-bold">How to Fix / Details</label>
                            <textarea name="fix_details" id="fix_details" class="form-control" rows="5"
                                      placeholder="Describe the bug and how you think it should be fixed..." 
                                      style="background: #1a1a2e; color: white; border-color: #444; resize: vertical;" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Submit Report</button>
                    </form>

                    <?php if (isset($currentUser) && ($currentUser['is_admin'] ?? false)): ?>
                        <div class="text-center mt-4">
                            <a href="<?php echo $baseUrl; ?>/bugs" class="text-decoration-none text-info">
                                <i class="fas fa-list-alt me-1"></i> View All Bug Reports
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <a href="<?php echo $baseUrl; ?>/" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


