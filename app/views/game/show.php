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
                    
                    <?php
                    $overallRating = isset($ratings['overall']) ? (float)$ratings['overall'] : 0;
                    $ratingOutOf10 = $overallRating * 2;
                    $totalVotes = isset($ratings['total_votes']) ? (int)$ratings['total_votes'] : 0;
                    ?>
                    <div class="mt-4 p-3 border rounded" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="mb-0 text-white-50" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">GameCritic Rating</h6>
                                <div class="d-flex align-items-baseline mt-1">
                                    <span class="display-4 fw-bold me-2" style="color: #ffd700; line-height: 1;"><?php echo number_format($ratingOutOf10, 1); ?></span>
                                    <span class="text-white-50" style="font-size: 1.2rem;">/ 10</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="mb-1">
                                    <?php
                                    $fullStars = floor($ratingOutOf10 / 2);
                                    $halfStar = ($ratingOutOf10 % 2) >= 1;
                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                    for ($i = 0; $i < $fullStars; $i++) {
                                        echo '<i class="fas fa-star text-warning" style="font-size: 1.2rem;"></i>';
                                    }
                                    if ($halfStar) {
                                        echo '<i class="fas fa-star-half-alt text-warning" style="font-size: 1.2rem;"></i>';
                                    }
                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<i class="far fa-star text-white-50" style="font-size: 1.2rem;"></i>';
                                    }
                                    ?>
                                </div>
                                <small class="text-white-50">Based on <?php echo $totalVotes; ?> <?php echo $totalVotes === 1 ? 'rating' : 'ratings'; ?></small>
                            </div>
                        </div>
                        <?php if ($totalVotes > 0): ?>
                            <div class="progress mt-2" style="height: 6px; background: rgba(255,255,255,0.2);">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                     style="width: <?php echo ($ratingOutOf10 / 10) * 100; ?>%;" 
                                     aria-valuenow="<?php echo $ratingOutOf10; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="10">
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center mt-2">
                                <small class="text-white-50">No ratings yet. Be the first to rate!</small>
                            </div>
                        <?php endif; ?>
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
                <?php if (!empty($_SESSION['error'])): ?>
                    <div class="alert alert-warning">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <div id="review-warning" class="alert alert-warning d-none">
                    Review cannot be empty
                </div>
<div id="spam-warning" class="alert alert-warning d-none">
    <strong>Warning: Spam detected! You cannot submit this review.</strong>
</div>
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
                    <div style="background:rgba(48, 45, 45, 1); max-width:500px; margin:5% auto; max-height:80vh; overflow-y:auto; padding:20px; border-radius:6px;">
                        <div class="text-white bg-danger fw-bold d-flex justify-content-center align-items-center rounded-top" style="width: 100%; height: 60px; font-size: 1.8rem;">
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
                    const textarea = document.getElementById('reviewTextarea');
                    const reviewWarning = document.getElementById('review-warning');
                    const spamWarning = document.getElementById('spam-warning');
                    const overlay = document.getElementById('guidelinesOverlay');
                    const agreeBtn = document.getElementById('agreeGuidelines');
                    const cancelBtn = document.getElementById('cancelGuidelines');

                    async function checkSpam(text) {
                        if (!text) return false;
                        try {
                            const res = await fetch('<?= rtrim($baseUrl, "/") ?>/spam/check', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({ text: text })
                            });
                            if (!res.ok) {
                                console.error('Spam API returned error', res.status);
                                return false;
                            }
                            const data = await res.json();
                            return !!data.spam;
                        } catch (err) {
                            console.error('Spam check failed', err);
                            return false;
                        }
                    }

                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const review = textarea.value.trim();
                        if (!review) {
                            reviewWarning.classList.remove('d-none');
                            textarea.focus();
                            return;
                        }
                        reviewWarning.classList.add('d-none');
                        overlay.style.display = 'flex';
                    });

                    agreeBtn.addEventListener('click', async () => {
                        overlay.style.display = 'none';
                        const reviewText = textarea.value.trim();
                        const isSpam = await checkSpam(reviewText);
                        if (isSpam) {
                            spamWarning.classList.remove('d-none');
                            textarea.focus();
                            return;
                        }
                        form.submit();
                    });

                    cancelBtn.addEventListener('click', () => overlay.style.display = 'none');
                    textarea.addEventListener('input', () => spamWarning.classList.add('d-none'));

                });
                </script>
                <?php if (!$currentUser): ?>
                <script>
                    (function() {
                        const textarea = document.getElementById('reviewTextarea');
                        const form = document.getElementById('reviewForm');

                        const loginUrl = '<?= rtrim($baseUrl,'/') ?>/login?redirected=1&reason=' + encodeURIComponent('comment');

                        textarea.addEventListener('focus', function() {
                            window.location.href = loginUrl;
                        });

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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                    <strong><?= htmlspecialchars($rev['username'] ?? 'User') ?></strong>
                    <small class="text-muted ms-2"><?= htmlspecialchars($rev['created_at']) ?></small>
                        </div>
                        <?php if ($currentUser && isset($rev['user_id']) && (int)$rev['user_id'] !== (int)$currentUser['id']): ?>
                            <button 
                                class="btn btn-sm <?= ($rev['is_following'] ?? false) ? 'btn-secondary' : 'btn-primary' ?> follow-btn" 
                                data-user-id="<?= (int)$rev['user_id'] ?>"
                                data-username="<?= htmlspecialchars($rev['username']) ?>"
                                data-is-following="<?= ($rev['is_following'] ?? false) ? '1' : '0' ?>">
                                <i class="fas fa-<?= ($rev['is_following'] ?? false) ? 'user-check' : 'user-plus' ?>"></i>
                                <?= ($rev['is_following'] ?? false) ? 'Following' : 'Follow' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    <p class="mt-1 mb-4">
                        <?= nl2br(htmlspecialchars($rev['comment'])) ?>
                    </p>
                    <form method="POST" action="<?= $baseUrl ?>/review/vote" class="d-inline">
                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                        <input type="hidden" name="vote" value="1">
                        <button class="vote-btn icon-btn <?= ($rev['user_vote'] ?? null) === 'up' ? 'voted' : '' ?>">
                            <i class="bi bi-hand-thumbs-up"></i>
                        </button>
                    </form>
                    <span class="vote-count"><?= $rev['upvotes'] ?? 0 ?></span>
                    <form method="POST" action="<?= $baseUrl ?>/review/vote" class="d-inline">
                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                        <input type="hidden" name="vote" value="-1">
                        <button class="vote-btn icon-btn <?= ($rev['user_vote'] ?? null) === 'down' ? 'voted' : '' ?>">
                            <i class="bi bi-hand-thumbs-down"></i>
                        </button>
                    </form>
                    <span class="vote-count"><?= $rev['downvotes'] ?? 0 ?></span>
                    </div>

                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                    $('.review-votes form').submit(function(e) {
                        e.preventDefault(); // stop normal form submission

                        let $form = $(this);
                        let reviewId = $form.find('input[name="review_id"]').val();
                        let vote     = $form.find('input[name="vote"]').val();

                        $.post($form.attr('action'), { review_id: reviewId, vote: vote }, function(response) {
                            if (response.success) {
                                location.reload();
                            } else if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                alert(response.message);
                            }
                        }, 'json');
                    });

                    $('.follow-btn').click(function(e) {
                        e.preventDefault();
                        const $btn = $(this);
                        const userId = $btn.data('user-id');
                        const username = $btn.data('username');
                        const isFollowing = $btn.data('is-following') === '1';
                        const action = isFollowing ? 'unfollow' : 'follow';
                        const url = '<?= $baseUrl ?>/user/' + action;

                        $.ajax({
                            url: url,
                            method: 'POST',
                            data: { user_id: userId },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    const newIsFollowing = !isFollowing;
                                    $btn.data('is-following', newIsFollowing ? '1' : '0');
                                    $btn.toggleClass('btn-primary btn-secondary');
                                    $btn.html(
                                        '<i class="fas fa-' + (newIsFollowing ? 'user-check' : 'user-plus') + '"></i> ' +
                                        (newIsFollowing ? 'Following' : 'Follow')
                                    );
                                } else {
                                    alert(response.message || 'Failed to ' + action + ' user');
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 401) {
                                    window.location.href = '<?= $baseUrl ?>/login';
                                } else {
                                    alert('An error occurred. Please try again.');
                                }
                            }
                        });
                    });
                    </script>

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
                            e.preventDefault(); 
                            warningDiv.style.display = 'block';
                        } else {
                            window.location.href = rateBtn.dataset.rateUrl;
                        }
                    });
                });
                </script>
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    if (window.location.hash === '#ratings') {
                        const tabBtn = document.querySelector('[data-bs-target="#ratings"]');
                        if (tabBtn) new bootstrap.Tab(tabBtn).show();
                    }
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
