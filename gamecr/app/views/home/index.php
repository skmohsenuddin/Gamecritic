<?php
$title = 'GameCritic - Discover Games';
$isSearch = isset($searchQuery) && !empty($searchQuery);
?>

<?php if ($isSearch): ?>
<!-- Search Results - Show at Top -->
<div id="search-results" class="container mt-3">
    <h2 class="mb-4">Search Results</h2>
    <div class="alert alert-info">
        Search results for: <strong><?php echo htmlspecialchars($searchQuery); ?></strong>
        <a href="<?php echo $baseUrl; ?>/" class="float-end">Clear Search</a>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php if (!empty($games)): ?>
            <?php foreach ($games as $game): ?>
                <div class="col">
                    <div class="card h-100 game-card">
                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int) $game['id']; ?>" class="text-decoration-none">
                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image']); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                            <div class="card-body pb-1">
                                <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                                <p class="meta mb-2">
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                                </p>
                                <small class="text-muted"><?php echo htmlspecialchars($game['release_year']); ?></small>
                                <p class="card-text text-truncate small mt-2">
                                    <?php echo htmlspecialchars($game['description']); ?>
                                </p>
                            </div>
                        </a>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <button class="btn btn-sm btn-outline-info w-100 compare-btn" data-id="<?php echo $game['id']; ?>"
                                data-title="<?php echo htmlspecialchars($game['title']); ?>"
                                data-genre="<?php echo htmlspecialchars($game['genre']); ?>"
                                data-platform="<?php echo htmlspecialchars($game['platform']); ?>"
                                data-year="<?php echo htmlspecialchars($game['release_year']); ?>"
                                data-ratings='<?php echo json_encode($game['ratings'] ?? []); ?>'
                                onclick="event.preventDefault(); event.stopPropagation(); toggleCompare(this);"
                                style="position: relative; z-index: 5;">
                                <i class="fas fa-plus me-1"></i>Compare
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No games found matching your search.</h4>
                    <p class="text-muted">Try a different search term.</p>
                    <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary">View All Games</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!$isSearch): ?>
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
                    <a href="/polls" class="btn btn-warning btn-sm">View Polls</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-dark text-white shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <i class="fas fa-lightbulb fa-2x mb-3 text-info"></i>
                    <h4>AI Suggestions</h4>
                    <p class="text-muted small">Find games based on your **Category** or **Mood**!</p>
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
                    <div class="card h-100 game-card">
                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int) $game['id']; ?>" class="text-decoration-none">
                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image']); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                            <div class="card-body">
                                <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                                <p class="meta mb-2">
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                                </p>
                                <small class="text-muted"><?php echo htmlspecialchars($game['release_year']); ?></small>
                            </div>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Floating Compare Bar -->
<div id="compareBar" class="compare-bar shadow-lg d-none">
    <div class="container d-flex justify-content-between align-items-center py-2">
        <div id="compareSelected" class="text-white small">
            <!-- Selected items will appear here -->
        </div>
        <div>
            <button class="btn btn-sm btn-secondary me-2" onclick="clearCompare()">Clear</button>
            <button class="btn btn-sm btn-primary" onclick="showComparison()">Compare Now</button>
        </div>
    </div>
</div>

<!-- Comparison Modal -->
<div class="modal fade" id="compareModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-info">
            <div class="modal-header border-secondary">
                <h5 class="modal-title"><i class="fas fa-balance-scale me-2"></i>Game Comparison</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-dark table-bordered m-0">
                        <tbody id="compareTableBody">
                            <!-- Comparison data injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .compare-bar {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(30, 30, 30, 0.95);
        border: 2px solid var(--primary-color);
        border-radius: 50px;
        z-index: 1050;
        min-width: 300px;
    }

    body.neon-mode .compare-bar {
        border-color: #00f3ff;
        box-shadow: 0 0 15px #00f3ff;
        background: rgba(0, 0, 0, 0.9);
    }

    .compare-item-tag {
        background: var(--primary-color);
        padding: 2px 10px;
        border-radius: 20px;
        margin-right: 10px;
        font-size: 0.8rem;
    }

    body.neon-mode .compare-item-tag {
        background: #bc13fe;
        box-shadow: 0 0 5px #bc13fe;
    }
</style>

<script>
    let compareList = [];

    function toggleCompare(btn) {
        const game = {
            id: btn.dataset.id,
            title: btn.dataset.title,
            genre: btn.dataset.genre,
            platform: btn.dataset.platform,
            year: btn.dataset.year,
            ratings: JSON.parse(btn.dataset.ratings || '{}')
        };

        const index = compareList.findIndex(g => g.id === game.id);
        if (index > -1) {
            compareList.splice(index, 1);
            btn.classList.remove('btn-info');
            btn.classList.add('btn-outline-info');
            btn.innerHTML = '<i class="fas fa-plus me-1"></i>Compare';
        } else {
            if (compareList.length >= 2) {
                alert("You can only compare 2 games at a time!");
                return;
            }
            compareList.push(game);
            btn.classList.remove('btn-outline-info');
            btn.classList.add('btn-info');
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Selected';
        }

        updateCompareBar();
    }

    function updateCompareBar() {
        const bar = document.getElementById('compareBar');
        const display = document.getElementById('compareSelected');

        if (compareList.length > 0) {
            bar.classList.remove('d-none');
            display.innerHTML = compareList.map(g => `<span class="compare-item-tag">${g.title}</span>`).join('');
        } else {
            bar.classList.add('d-none');
        }
    }

    function clearCompare() {
        compareList = [];
        document.querySelectorAll('.compare-btn').forEach(btn => {
            btn.classList.remove('btn-info');
            btn.classList.add('btn-outline-info');
            btn.innerHTML = '<i class="fas fa-plus me-1"></i>Compare';
        });
        updateCompareBar();
    }

    function showComparison() {
        if (compareList.length < 2) {
            alert("Please select 2 games to compare!");
            return;
        }

        const g1 = compareList[0];
        const g2 = compareList[1];
        const tbody = document.getElementById('compareTableBody');

        const renderStars = (val) => {
            const num = parseFloat(val) || 0;
            const full = Math.floor(num);
            const half = num % 1 >= 0.5 ? 1 : 0;
            const empty = 5 - full - half;
            return '<i class="fas fa-star text-warning"></i>'.repeat(full) +
                (half ? '<i class="fas fa-star-half-alt text-warning"></i>' : '') +
                '<i class="far fa-star text-muted"></i>'.repeat(empty) +
                ` <small class="ms-1">(${num})</small>`;
        };

        const renderPollBar = (val1, val2) => {
            const v1 = parseFloat(val1) || 0;
            const v2 = parseFloat(val2) || 0;
            const total = v1 + v2;
            const p1 = total > 0 ? (v1 / total) * 100 : 50;
            const p2 = total > 0 ? (v2 / total) * 100 : 50;
            return `
                <div class="progress bg-secondary" style="height: 10px; border-radius: 5px; overflow: hidden;">
                    <div class="progress-bar bg-info" style="width: ${p1}%"></div>
                    <div class="progress-bar bg-danger" style="width: ${p2}%"></div>
                </div>
                <div class="d-flex justify-content-between x-small text-muted mt-1" style="font-size: 0.7rem;">
                    <span>${v1}</span>
                    <span>Vs</span>
                    <span>${v2}</span>
                </div>
            `;
        };

        tbody.innerHTML = `
        <tr class="table-primary">
            <th>Category</th>
            <th class="text-center text-info" style="width: 35%">${g1.title}</th>
            <th class="text-center text-info" style="width: 35%">${g2.title}</th>
        </tr>
        <tr>
            <td><strong>Overall Community Score</strong></td>
            <td class="text-center">${renderStars(g1.ratings.overall)}</td>
            <td class="text-center">${renderStars(g2.ratings.overall)}</td>
        </tr>
        <tr>
            <td colspan="3" class="bg-dark p-2 text-center small text-muted">Community Verdict Comparison</td>
        </tr>
        <tr>
            <td><strong>Fun Factor</strong></td>
            <td>${renderPollBar(g1.ratings.fun, g2.ratings.fun)}</td>
            <td>${renderPollBar(g2.ratings.fun, g1.ratings.fun)}</td>
        </tr>
        <tr>
            <td><strong>Graphics</strong></td>
            <td>${renderPollBar(g1.ratings.graphics, g2.ratings.graphics)}</td>
            <td>${renderPollBar(g2.ratings.graphics, g1.ratings.graphics)}</td>
        </tr>
        <tr>
            <td><strong>Story/Lore</strong></td>
            <td>${renderPollBar(g1.ratings.story, g2.ratings.story)}</td>
            <td>${renderPollBar(g2.ratings.story, g1.ratings.story)}</td>
        </tr>
        <tr>
            <td><strong>Technical Quality</strong></td>
            <td>${renderPollBar(g1.ratings.technical, g2.ratings.technical)}</td>
            <td>${renderPollBar(g2.ratings.technical, g1.ratings.technical)}</td>
        </tr>
        <tr>
            <td><strong>Game Info</strong></td>
            <td class="text-center small">${g1.genre} | ${g1.platform}</td>
            <td class="text-center small">${g2.genre} | ${g2.platform}</td>
        </tr>
    `;

        const modal = new bootstrap.Modal(document.getElementById('compareModal'));
        modal.show();
    }
</script>

<!-- Things You May Like -->
<div class="container mt-5">
    <h2 class="mb-4">Things You May Like</h2>
    <?php if ($currentUser): ?>
        <?php if (!empty($recommendedGames)): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($recommendedGames as $game): ?>
                    <div class="col">
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
                            <a href="<?php echo $baseUrl; ?>/game/<?php echo $game['id']; ?>" class="text-decoration-none">
                                <img src="<?php echo $imgSrc; ?>" class="card-img-top"
                                    alt="<?php echo htmlspecialchars($game['title'] ?? ''); ?>"
                                    style="height: 200px; object-fit: cover;">
                                <div class="card-body d-flex flex-column pb-1">
                                    <h6 class="card-title text-white mb-2"><?php echo htmlspecialchars($game['title'] ?? ''); ?>
                                    </h6>
                                    <p class="card-text text-muted small flex-grow-1 mb-2">
                                        <?php echo htmlspecialchars(substr($game['description'] ?? 'No description available', 0, 80)) . '...'; ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span
                                            class="badge bg-primary"><?php echo htmlspecialchars($game['genre'] ?? 'N/A'); ?></span>
                                        <span
                                            class="badge bg-secondary"><?php echo htmlspecialchars($game['platform'] ?? 'N/A'); ?></span>
                                    </div>
                                    <small class="text-muted"><?php echo $game['release_year'] ?? 'N/A'; ?></small>
                                </div>
                            </a>
                            <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                                <button class="btn btn-sm btn-outline-info w-100 compare-btn" data-id="<?php echo $game['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($game['title'] ?? ''); ?>"
                                    data-genre="<?php echo htmlspecialchars($game['genre'] ?? 'N/A'); ?>"
                                    data-platform="<?php echo htmlspecialchars($game['platform'] ?? 'N/A'); ?>"
                                    data-year="<?php echo htmlspecialchars($game['release_year'] ?? 'N/A'); ?>"
                                    data-ratings='<?php echo json_encode($game['ratings'] ?? []); ?>'
                                    onclick="event.preventDefault(); event.stopPropagation(); toggleCompare(this);"
                                    style="position: relative; z-index: 5;">
                                    <i class="fas fa-plus me-1"></i>Compare
                                </button>
                            </div>
                        </div>
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


<!-- All Games (only show when not searching) -->
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
                    <div class="card h-100 game-card">
                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int) $game['id']; ?>" class="text-decoration-none">
                            <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($game['cover_image']); ?>"
                                class="card-img-top" alt="<?php echo htmlspecialchars($game['title']); ?>">
                            <div class="card-body pb-1">
                                <h5 class="card-title text-white"><?php echo htmlspecialchars($game['title']); ?></h5>
                                <p class="meta mb-2">
                                    <span class="badge bg-primary me-1"><?php echo htmlspecialchars($game['genre']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($game['platform']); ?></span>
                                </p>
                                <small class="text-muted"><?php echo htmlspecialchars($game['release_year']); ?></small>
                                <p class="card-text text-truncate small mt-2">
                                    <?php echo htmlspecialchars($game['description']); ?>
                                </p>
                            </div>
                        </a>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <button class="btn btn-sm btn-outline-info w-100 compare-btn" data-id="<?php echo $game['id']; ?>"
                                data-title="<?php echo htmlspecialchars($game['title']); ?>"
                                data-genre="<?php echo htmlspecialchars($game['genre']); ?>"
                                data-platform="<?php echo htmlspecialchars($game['platform']); ?>"
                                data-year="<?php echo htmlspecialchars($game['release_year']); ?>"
                                data-ratings='<?php echo json_encode($game['ratings'] ?? []); ?>'
                                onclick="event.preventDefault(); event.stopPropagation(); toggleCompare(this);"
                                style="position: relative; z-index: 5;">
                                <i class="fas fa-plus me-1"></i>Compare
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-gamepad fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No games found</h4>
                    <p class="text-muted">There are no games to display right now.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>