<?php
$title = 'Reviews from Followed Users | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">📰 Reviews from Followed Users</h1>
                <div>
                    <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-outline-primary me-2">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                    <a href="<?php echo $baseUrl; ?>/profile" class="btn btn-outline-secondary">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </div>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="alert alert-info">
                    <h5>No reviews yet</h5>
                    <p>You're not following anyone yet, or the users you follow haven't posted any reviews.</p>
                    <p class="mb-0">Start following users on game pages to see their reviews here!</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($reviews as $review): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($review['profile_picture'])): ?>
                                            <img src="<?php echo $baseUrl . htmlspecialchars($review['profile_picture']); ?>" 
                                                 alt="<?php echo htmlspecialchars($review['username']); ?>" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($review['username']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($review['created_at']); ?></small>
                                        </div>
                                    </div>
                                    
                                    <h5 class="card-title">
                                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)$review['game_id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($review['game_title']); ?>
                                        </a>
                                    </h5>
                                    
                                    <?php if (!empty($review['cover_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($review['cover_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($review['game_title']); ?>" 
                                             class="img-fluid mb-3" 
                                             style="max-height: 150px; width: auto;">
                                    <?php endif; ?>
                                    
                                    <p class="card-text">
                                        <?php echo nl2br(htmlspecialchars($review['comment'])); ?>
                                    </p>
                                    
                                    <div class="d-flex align-items-center mt-3">
                                        <form method="POST" action="<?php echo $baseUrl; ?>/review/vote" class="d-inline me-2">
                                            <input type="hidden" name="review_id" value="<?php echo (int)$review['id']; ?>">
                                            <input type="hidden" name="vote" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-primary vote-btn <?= ($review['user_vote'] ?? null) === 'up' ? 'active' : '' ?>">
                                                <i class="bi bi-hand-thumbs-up"></i>
                                                <span class="vote-count"><?php echo $review['upvotes'] ?? 0; ?></span>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?php echo $baseUrl; ?>/review/vote" class="d-inline">
                                            <input type="hidden" name="review_id" value="<?php echo (int)$review['id']; ?>">
                                            <input type="hidden" name="vote" value="-1">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary vote-btn <?= ($review['user_vote'] ?? null) === 'down' ? 'active' : '' ?>">
                                                <i class="bi bi-hand-thumbs-down"></i>
                                                <span class="vote-count"><?php echo $review['downvotes'] ?? 0; ?></span>
                                            </button>
                                        </form>
                                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)$review['game_id']; ?>" 
                                           class="btn btn-sm btn-outline-info ms-auto">
                                            View Game <i class="fas fa-arrow-right"></i>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.vote-btn').on('click', function(e) {
        e.preventDefault();
        const $form = $(this).closest('form');
        const $btn = $(this);
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else if (response.redirect) {
                    window.location.href = response.redirect;
                } else {
                    alert(response.message || 'Failed to vote');
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    window.location.href = '<?php echo $baseUrl; ?>/login';
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });
});
</script>

