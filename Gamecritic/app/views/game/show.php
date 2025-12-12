<?php
$title = htmlspecialchars($game['title']) . ' | GameCritic';
?>

<div class="container mt-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card">
                <img src="<?php echo htmlspecialchars($game['cover_resolved']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
            </div>
        </div>
        <div class="col-md-8">
            <h1 class="mb-3"><?php echo htmlspecialchars($game['title']); ?></h1>
            <p class="mb-2">
                <span class="badge bg-primary me-2"><?php echo htmlspecialchars($game['genre']); ?></span>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
            </p>
            <p class="text-muted mb-3">Released: <?php echo htmlspecialchars($game['release_year']); ?></p>
            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
            </div>
            <?php if (!empty($game['review'])): ?>
            <div class="mb-4">
                <h5>Our Review</h5>
                <p class="fst-italic"><?php echo nl2br(htmlspecialchars($game['review'])); ?></p>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <h5>Write a Comment</h5>
                <form method="POST" action="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>/review">
                    <div class="mb-3">
                        <textarea class="form-control" name="comment" rows="3" placeholder="Share your thoughts..." required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit Comment</button>
                </form>
            </div>

            <div class="mb-4">
                <h5>Comments</h5>
                <?php if (empty($reviews)): ?>
                    <div class="text-muted">No comments yet. Be the first!</div>
                <?php else: ?>
                    <div id="comments-container">
                        <?php foreach ($reviews as $rev): ?>
                            <?php if (empty($rev['comment'])) continue; ?>
                            <div class="comment-item mb-3 p-3 border rounded" style="background: rgba(255,255,255,0.05);">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <strong class="text-white me-2"><?php echo htmlspecialchars($rev['username'] ?? 'User'); ?></strong>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($rev['created_at']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="comment-content mb-3">
                                    <?php echo nl2br(htmlspecialchars($rev['comment'])); ?>
                                </div>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-light">Back to Home</a>
        </div>
    </div>
</div>



