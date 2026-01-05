<?php
$title = 'GameCritic - Discover Games';
?>

<!-- Featured Games Carousel -->
<div id="featuredGames" class="carousel slide mt-3" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <a href="<?php echo $baseUrl; ?>/game/6">
                <img src="<?php echo $baseUrl; ?>/images/68bdd97e5b444.jpg" class="d-block w-100 carousel-img"
                    alt="God of War">
                <div class="carousel-caption d-none d-md-block">
                    <h5>God of War: Ragnarök</h5>
                    <p>Epic Norse adventure continues</p>
                </div>
            </a>
        </div>
        <div class="carousel-item">
            <a href="<?php echo $baseUrl; ?>/game/7">
                <img src="<?php echo $baseUrl; ?>/images/693bf966cb8da.webp" class="d-block w-100 carousel-img"
                    alt="Elden Ring">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Elden Ring</h5>
                    <p>FromSoftware's masterpiece of exploration</p>
                </div>
            </a>
        </div>
        <div class="carousel-item">
            <a href="<?php echo $baseUrl; ?>/game/8">
                <img src="<?php echo $baseUrl; ?>/images/pio.jpg" class="d-block w-100 carousel-img" alt="Zelda">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Zelda: Tears of the Kingdom</h5>
                    <p>A magical return to Hyrule</p>
                </div>
            </a>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#featuredGames" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#featuredGames" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- Community Features (Python Integration) -->
<div class="container mt-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card bg-dark text-white shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-poll fa-2x mb-3 text-warning"></i>
                    <h4>Community Polls</h4>
                    <p class="text-muted small">Vote on the next big game or share your opinion!</p>
                    <a href="/create_poll" class="btn btn-warning btn-sm">View Polls</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-lightbulb fa-2x mb-3 text-info"></i>
                    <h4>AI Suggested</h4>
                    <p class="text-muted small">Need something new? Get personalized recommendations!</p>
                    <a href="/suggestion" class="btn btn-info text-white btn-sm">Get Suggestions</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-bug fa-2x mb-3 text-danger"></i>
                    <h4>Report Bug</h4>
                    <p class="text-muted small">Found an issue? Help us improve by reporting it!</p>
                    <a href="/report_bug" class="btn btn-danger btn-sm">Report Now</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Featured Games -->
<?php if (!empty($topRatedGames)): ?>
    <div class="container mt-5">
        <h2 class="mb-4">Featured Games</h2>
        <div class="row row-cols-1 row-cols-md-4 g-3">
            <?php foreach ($topRatedGames as $game): ?>
                <div class="col">
                    <a href="<?php echo $baseUrl; ?>/game/<?php echo (int) $game['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 game-card">
                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image']); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                                <p class="meta mb-2">
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?php echo htmlspecialchars($game['release_year']); ?></small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Things You May Like -->
<div class="container mt-5">
    <h2 class="mb-4">Things You May Like</h2>
    <?php if ($currentUser): ?>
        <?php if (!empty($recommendedGames)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($recommendedGames as $game): ?>
                    <div class="col">
                        <a href="<?php echo $baseUrl; ?>/game/<?php echo $game['id']; ?>" class="text-decoration-none">
                            <div class="card h-100 game-card shadow-sm">
                                <?php
                                $cover = $game['cover_image'] ?? '/images/default.jpg';
                                if (strpos($cover, '/images/') === 0) {
                                    $imgSrc = $baseUrl . $cover;
                                } elseif (strpos($cover, 'images/') === 0) {
                                    $imgSrc = $baseUrl . '/' . $cover;
                                } else {
                                    $imgSrc = $baseUrl . '/images/' . $cover;
                                }
                                ?>
                                <img src="<?php echo $imgSrc; ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($game['title'] ?? ''); ?>"
                                    style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title text-white mb-2"><?php echo htmlspecialchars($game['title'] ?? ''); ?>
                                    </h6>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?php echo htmlspecialchars(substr($game['description'] ?? 'No description available', 0, 80)) . '...'; ?>
                                    </p>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span
                                                class="badge bg-primary"><?php echo htmlspecialchars($game['genre'] ?? 'N/A'); ?></span>
                                            <span
                                                class="badge bg-secondary"><?php echo htmlspecialchars($game['platform'] ?? 'N/A'); ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><?php echo $game['release_year'] ?? 'N/A'; ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="#all-games" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-gamepad me-2"></i>See More Games
                </a>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="card">
                    <div class="card-body">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted mb-3">Start Exploring Games!</h4>
                        <p class="text-muted mb-4">Review some games to get personalized recommendations based on your
                            preferences.</p>
                        <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary btn-lg">
                            <i class="fas fa-gamepad me-2"></i>Browse All Games
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="card">
                <div class="card-body">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h4 class="text-primary mb-3">Join GameCritic!</h4>
                    <p class="text-muted mb-4">Sign up to get personalized game recommendations based on your reviews and
                        preferences.</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="<?php echo $baseUrl; ?>/register" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Sign Up
                        </a>
                        <a href="<?php echo $baseUrl; ?>/login" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>


<!-- All Games -->
<div id="all-games" class="container mt-5">
    <h2 class="mb-4">All Games</h2>

    <!-- Filter Info -->
    <?php if (isset($filterGenre) || isset($filterPlatform)): ?>
        <div class="alert alert-info">
            Filtered by:
            <?php if (isset($filterGenre)): ?>
                <strong>Genre: <?php echo htmlspecialchars($filterGenre); ?></strong>
            <?php endif; ?>
            <?php if (isset($filterPlatform)): ?>
                <strong>Platform: <?php echo htmlspecialchars($filterPlatform); ?></strong>
            <?php endif; ?>
            <a href="/" class="float-end">Clear Filters</a>
        </div>
    <?php endif; ?>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
                <div class="col">
                    <a href="<?php echo $baseUrl; ?>/game/<?php echo (int) $game['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 game-card">
                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image']); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                                <p class="meta mb-2">
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                                </p>
                                <p class="card-text text-truncate"><?php echo htmlspecialchars($game['description']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <h4>No games found</h4>
                    <p>There are no games to display right now.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>