<?php
$title = 'Community Polls | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary"><i class="fas fa-poll-h me-2"></i>Community Polls</h1>
                <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
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

            <?php if (empty($polls)): ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-poll-h fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Polls Yet</h5>
                        <p class="text-muted">No polls have been created yet.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($polls as $poll): ?>
                        <div class="col-md-6">
                            <div class="card bg-dark text-white border-secondary h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title text-info"><?php echo htmlspecialchars($poll['question']); ?></h5>
                                    <p class="text-muted small">
                                        <i class="far fa-calendar-alt me-1"></i> 
                                        <?php echo date('Y-m-d', strtotime($poll['created_at'])); ?>
                                    </p>
                                    
                                    <?php if (!empty($poll['options'])): ?>
                                        <div class="mt-3">
                                            <h6>Options:</h6>
                                            <ul class="list-unstyled">
                                                <?php foreach ($poll['options'] as $option): ?>
                                                    <li class="mb-2">
                                                        <i class="fas fa-circle text-primary me-2" style="font-size: 0.5rem;"></i>
                                                        <?php echo htmlspecialchars($option); ?>
                                                        <?php if (isset($poll['result'][$option])): ?>
                                                            <span class="badge bg-secondary ms-2">
                                                                <?php echo (int)$poll['result'][$option]; ?> votes
                                                            </span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex gap-2 mt-3">
                                        <a href="<?php echo $baseUrl; ?>/poll/<?php echo (int)$poll['id']; ?>/vote" 
                                           class="btn btn-outline-primary btn-sm flex-grow-1">
                                            <i class="fas fa-vote-yea me-1"></i> Vote
                                        </a>
                                        <a href="<?php echo $baseUrl; ?>/poll/<?php echo (int)$poll['id']; ?>/results" 
                                           class="btn btn-outline-info btn-sm flex-grow-1">
                                            <i class="fas fa-chart-bar me-1"></i> Results
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}
</style>


