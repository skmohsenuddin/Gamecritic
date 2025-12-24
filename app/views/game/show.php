<?php
$title = htmlspecialchars($game['title']) . ' | GameCritic';
?>

<div class="container mt-4">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card feature-card">
                <img src="<?php echo htmlspecialchars($game['cover_resolved']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                    <h5 class="mt-3">Description</h5>
                    <div class="row g-4">
                        <p><?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
                    </div>
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
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="gameTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="true">
                        Reviews
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ratings-tab" data-bs-toggle="tab" data-bs-target="#ratings" type="button" role="tab" aria-controls="ratings" aria-selected="false">
                        Ratings
                    </button>
                </li>
            </ul>

            <!-- Tab content -->
            <div class="tab-content" id="gameTabsContent">

            <!-- Reviews Tab -->
            <div class="tab-pane fade show active" id="reviews" role="tabpanel">

            <!-- Write Review -->
            <div class="mb-4">
                <h5 class = "mt-3">Write a Review</h5>
                <form id="reviewForm" method="POST" action="<?= rtrim($baseUrl,'/') ?>/game/<?= (int)$game['id'] ?>/review">
                    <div class="mb-3">
                        <textarea
                            id="reviewTextarea"
                            class="form-control"
                            name="review"
                            rows="3"
                            placeholder="Share your thoughts about this game..."
                            <?php if (!$currentUser) echo 'readonly'; ?>></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger" <?php if (!$currentUser) echo 'disabled'; ?>>Submit Review</button>
                </form>

                <!-- Guidelines -->
                <div id="guidelinesOverlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5);">
                    <div style="background:rgba(48, 45, 45, 1); max-width:500px; margin:5% auto; max-width:500px; 
                    max-height:80vh; overflow-y:auto; padding:20px; border-radius:6px;">
                        <div 
                            class="text-white bg-danger fw-bold d-flex justify-content-center align-items-center rounded-top"
                            style="width: 100%; height: 60px; font-size: 1.8rem;">
                            Community Guidelines
                        </div>
                        <p class="mt-3">Please follow these guidelines before submitting your review:</p>
                        <ul>
                            <li><strong>Be Respectful: </strong>Treat developers and other users with
                            respect. No personal attacks, harassment, hate speech, or threats.</li>
                            <li><strong>Be Honest & Constructive: </strong>Share genuine experiences, positive or 
                            negative. Explain your reasoning and avoid one-word reviews.</li>
                            <li><strong>No Spoilers Without Warning: </strong>Do not post major story spoilers. If 
                            necessary, clearly mark them at the start of your review.</li>
                            <li><strong>Keep Reviews Relevant: </strong>Avoid off-topic discussions. Focus on the 
                            game itself to make it enjoyable for game enthusiasts.</li>
                            <li><strong>No Hate or Discrimination: </strong>Content targeting individuals or 
                            groups based on race, gender, nationality, religion, or identity will be removed.</li>
                            <li><strong>No Spam or Self-Promotion: </strong>Do not post ads, referral links, or 
                            promotional content. Repeated copy-paste reviews are not allowed.</li> 
                            <li><strong>Appropriate Language: </strong>Do not include excessive swearing, sexual references, 
                            graphic violence, or offensive jokes. Keep it readable and respectful to all users.</li>
                        </ul>
                        <p class="mt-2">Moderators may remove content that violates any of the above guidelines. 
                        Repeated violations may result in restrictions or bans.</p>
                        <p class="mt-1">Have Fun: Enjoy discussing, rating, and discovering games responsibly!</p>
                        <div class="guidelinesButtons d-flex justify-content-end gap-2 mt-3">
                            <button type="button" id="cancelGuidelines" class="btn btn-secondary">Cancel</button>
                            <button type="button" id="agreeGuidelines" class="btn btn-success">Agree & Continue</button>
                        </div>
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const form = document.getElementById('reviewForm');
                    const overlay = document.getElementById('guidelinesOverlay');
                    const agreeBtn = document.getElementById('agreeGuidelines');
                    const cancelBtn = document.getElementById('cancelGuidelines');
                    let confirmed = false;
                    form.addEventListener('submit', function(e) {
                        if (!confirmed) {
                            e.preventDefault();             
                            overlay.style.display = 'flex'; 
                        }
                    });
                    agreeBtn.addEventListener('click', () => {
                        confirmed = true;
                        overlay.style.display = 'none';
                        form.submit();
                    });
                    cancelBtn.addEventListener('click', () => {
                        overlay.style.display = 'none';
                    });
                });
                </script>


                <?php if (!$currentUser): ?>
                <script>
                    (function() {
                        const textarea = document.getElementById('reviewTextarea');
                        const form = document.getElementById('reviewForm');

                        const loginUrl = '<?= rtrim($baseUrl,'/') ?>/login?redirected=1&reason=' + encodeURIComponent('comment');

                        // Redirect to login when textarea is focused
                        textarea.addEventListener('focus', function() {
                            window.location.href = loginUrl;
                        });

                        // Prevent form submission and redirect
                        form.addEventListener('submit', function(e) {
                            window.location.href = loginUrl;
                        });
                    })();
                </script>
                <?php endif; ?>

                <!-- Reviews List -->
                <?php if (empty($reviews)): ?>
                    <p class="text-muted">No reviews yet.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="border rounded p-3 mb-3">
                            <strong><?= htmlspecialchars($rev['username'] ?? 'User') ?></strong>
                            <small class="text-muted ms-2"><?= htmlspecialchars($rev['created_at']) ?></small>
                            <p class="mt-2 mb-0">
                                <?= nl2br(htmlspecialchars($rev['comment'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                </div>
            </div>

            <!-- Ratings Tab -->
            <div class="tab-pane fade" id="ratings" role="tabpanel">
                <h5 class="mt-3">Overall Rating</h5>
                <p class="fs-4">
                    <?= $ratings['overall'] ?? 'N/A' ?>/5
                    <small class="text-muted">
                        (<?= $ratings['total_votes'] ?? 0 ?> votes)
                    </small>
                </p>
                <?php if ($userRatingAvg !== null): ?>
                    <h5 class="mt-3">Your Rating</h5>
                    <p class="fs-4">
                        <?= number_format($userRatingAvg, 1) ?>/5
                    </p>
                <?php endif; ?>
                <hr>
                <ul class="list-group mb-4 rating-list">
                    <li class="list-group-item rating-item">
                        <span class="rating-label">Fun & Engagement</span>
                        <span class="rating-stars-display">
                            <?php
                                $val = (int) floor($ratings['fun'] ?? 0);
                                echo str_repeat('★', $val);
                                echo str_repeat('☆', 5 - $val);
                            ?>
                        </span>
                    </li>
                    <li class="list-group-item rating-item">
                        <span class="rating-label">Graphics & Art</span>
                        <span class="rating-stars-display">
                            <?php
                                $val = (int) floor($ratings['graphics'] ?? 0);
                                echo str_repeat('★', $val);
                                echo str_repeat('☆', 5 - $val);
                            ?>
                        </span>
                    </li>
                    <li class="list-group-item rating-item">
                        <span class="rating-label">Audio & Music</span>
                        <span class="rating-stars-display">
                            <?php
                                $val = (int) floor($ratings['audio'] ?? 0);
                                echo str_repeat('★', $val);
                                echo str_repeat('☆', 5 - $val);
                            ?>
                        </span>
                    </li>
                    <li class="list-group-item rating-item">
                        <span class="rating-label">Story & Narrative</span>
                        <span class="rating-stars-display">
                            <?php
                                $val = (int) floor($ratings['story'] ?? 0);
                                echo str_repeat('★', $val);
                                echo str_repeat('☆', 5 - $val);
                            ?>
                        </span>
                    </li>
                    <li class="list-group-item rating-item">
                        <span class="rating-label">User Interface & Experience</span>
                        <span class="rating-stars-display">
                            <?php
                                $val = (int) floor($ratings['ux_ui'] ?? 0);
                                echo str_repeat('★', $val);
                                echo str_repeat('☆', 5 - $val);
                            ?>
                        </span>
                    </li>
                    <li class="list-group-item rating-item">
                        <span class="rating-label">Technical Performance</span>
                        <span class="rating-stars-display">
                            <?php
                                $val = (int) floor($ratings['technical'] ?? 0);
                                echo str_repeat('★', $val);
                                echo str_repeat('☆', 5 - $val);
                            ?>
                        </span>
                    </li>
                </ul>
                <div class="d-flex justify-content-center">
                    <a href="<?= rtrim($baseUrl,'/') ?>/game/<?= (int)$game['id'] ?>/rate"
                    id="rateNowBtn"
                    class="btn btn-warning btn-lg <?= $hasRated ? 'disabled' : '' ?>"
                    data-rate-url="<?= rtrim($baseUrl,'/') ?>/game/<?= (int)$game['id'] ?>/rate"
                    data-rated="<?= $hasRated ? '1' : '0' ?>"
                    <?= $hasRated ? 'aria-disabled="true"' : '' ?>>
                        Rate Now
                    </a>
                </div>
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const rateBtn = document.getElementById('rateNowBtn');
                    const warningDiv = document.getElementById('rating-warning');
                    if (!rateBtn || !warningDiv) return;
                    rateBtn.addEventListener('click', function (e) {
                        const hasRated = rateBtn.dataset.rated === '1';
                        if (hasRated) {
                            e.preventDefault(); // stop link
                            warningDiv.style.display = 'block';
                        } else {
                            window.location.href = rateBtn.dataset.rateUrl;
                        }
                    });
                });
                </script>
                </div>
            </div>
        <div class="mb-4 d-flex justify-content-center">
            <a href="<?= $baseUrl ?>/" class="text-decoration-none text-light fs-1">&laquo;</a>
        </div>
        </div>
    </div>
</div>
