<?php
$title = 'Top Reviews | GameCritic';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-trophy text-warning me-2"></i>Top Reviews
            </h1>
            <p class="text-muted">Reviews sorted by upvotes - The community's most appreciated reviews</p>
        </div>
        <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Home
        </a>
    </div>

    <?php if (!empty($topReviews)): ?>
        <div class="row">
            <div class="col-12">
                <?php foreach ($topReviews as $index => $review): ?>
                    <div class="card mb-4 bg-dark text-white border-secondary shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-warning text-dark me-3" style="font-size: 1rem; padding: 0.5rem 0.75rem;">
                                            #<?php echo $index + 1; ?>
                                        </span>
                                        <div>
                                            <strong class="fs-5"><?php echo htmlspecialchars($review['username'] ?? 'User'); ?></strong>
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($review['created_at'] ?? ''); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)($review['game_id'] ?? 0); ?>" 
                                           class="text-info text-decoration-none fs-6">
                                            <i class="fas fa-gamepad me-1"></i>
                                            <strong><?php echo htmlspecialchars($review['game_title'] ?? 'Unknown Game'); ?></strong>
                                        </a>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="text-success">
                                            <i class="bi bi-hand-thumbs-up fs-5"></i>
                                            <span class="ms-2 fw-bold fs-5"><?php echo (int)($review['upvotes'] ?? 0); ?></span>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">Upvotes</small>
                                        </div>
                                        <div class="text-danger">
                                            <i class="bi bi-hand-thumbs-down fs-5"></i>
                                            <span class="ms-2 fw-bold fs-5"><?php echo (int)($review['downvotes'] ?? 0); ?></span>
                                            <small class="d-block text-muted" style="font-size: 0.75rem;">Downvotes</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-top border-secondary pt-3 mt-3">
                                <p class="mb-0" style="line-height: 1.8; font-size: 1.05rem;">
                                    <?php echo nl2br(htmlspecialchars($review['comment'] ?? '')); ?>
                                </p>
                            </div>
                            <?php if (isset($review['game_id'])): ?>
                                <div class="mt-3">
                                    <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)$review['game_id']; ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-comment me-1"></i>View Full Review & Game Details
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="card bg-dark text-white">
                <div class="card-body py-5">
                    <i class="fas fa-trophy fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">No reviews yet</h3>
                    <p class="text-muted mb-4">Be the first to write a review and get upvoted by the community!</p>
                    <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary">
                        <i class="fas fa-gamepad me-1"></i>Browse Games
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3) !important;
    }
</style>

