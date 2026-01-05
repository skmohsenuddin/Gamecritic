<?php
$title = 'Bug Reports | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-bug me-2"></i>Bug Reports</h1>
                <a href="<?php echo $baseUrl; ?>/report_bug" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Report New Bug
                </a>
            </div>

            <?php if (empty($bugs)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bug fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Bug Reports</h5>
                        <p class="text-muted">No bug reports have been submitted yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($bugs as $bug): ?>
                        <div class="col-md-6">
                            <div class="card bg-dark text-white border-secondary h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title text-primary">
                                            <i class="fas fa-tag me-2"></i><?php echo htmlspecialchars($bug['bug_type']); ?>
                                        </h5>
                                        <span class="badge bg-secondary">
                                            <?php echo date('Y-m-d', strtotime($bug['created_at'])); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted small mb-2">
                                        <i class="fas fa-user me-1"></i>
                                        Reported by: <strong><?php echo htmlspecialchars($bug['reporter_name']); ?></strong>
                                    </p>
                                    
                                    <div class="mt-3">
                                        <h6 class="text-info">Details / Fix:</h6>
                                        <p class="text-white-50" style="white-space: pre-wrap;">
                                            <?php echo htmlspecialchars($bug['fix_details']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>
</div>


