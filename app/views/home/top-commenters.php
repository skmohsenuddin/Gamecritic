<?php
$title = 'Top Contributors | GameCritic';
?>

<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-2">
                <i class="fas fa-trophy text-warning me-2"></i>Top Contributors
            </h1>
            <p class="text-muted">Most Active Reviewers - Ranked by Total Comments</p>
        </div>
        <a href="<?php echo $baseUrl; ?>/" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Home
        </a>
    </div>

    <?php if (!empty($topCommenters)): ?>
        <div class="row g-4">
            <?php 
            $rankColors = [
                1 => ['bg' => 'bg-warning', 'text' => 'text-dark', 'icon' => 'fa-trophy', 'badge' => '1st'],
                2 => ['bg' => 'bg-secondary', 'text' => 'text-white', 'icon' => 'fa-medal', 'badge' => '2nd'],
                3 => ['bg' => 'bg-danger', 'text' => 'text-white', 'icon' => 'fa-award', 'badge' => '3rd'],
                4 => ['bg' => 'bg-info', 'text' => 'text-white', 'icon' => 'fa-star', 'badge' => '4th'],
                5 => ['bg' => 'bg-success', 'text' => 'text-white', 'icon' => 'fa-certificate', 'badge' => '5th']
            ];
            foreach ($topCommenters as $index => $commenter): 
                $rank = $index + 1;
                $rankStyle = $rankColors[$rank] ?? ['bg' => 'bg-dark', 'text' => 'text-white', 'icon' => 'fa-hashtag', 'badge' => '#' . $rank];
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card bg-dark text-white border-secondary shadow-lg h-100 position-relative overflow-hidden">
                        <!-- Rank Badge -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge <?php echo $rankStyle['bg']; ?> <?php echo $rankStyle['text']; ?> px-3 py-2" style="font-size: 1rem;">
                                <i class="fas <?php echo $rankStyle['icon']; ?> me-1"></i>
                                <?php echo $rankStyle['badge']; ?>
                            </span>
                        </div>
                        
                        <div class="card-body text-center p-4">
                            <!-- Profile Picture or Avatar -->
                            <div class="mb-3">
                                <?php if (!empty($commenter['profile_picture'])): ?>
                                    <img src="<?php echo $baseUrl; ?><?php echo htmlspecialchars($commenter['profile_picture']); ?>" 
                                         alt="<?php echo htmlspecialchars($commenter['username']); ?>"
                                         class="rounded-circle" 
                                         style="width: 100px; height: 100px; object-fit: cover; border: 3px solid var(--primary-color);">
                                <?php else: ?>
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-primary" 
                                         style="width: 100px; height: 100px; border: 3px solid var(--primary-color);">
                                        <i class="fas fa-user fa-3x text-white"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Username -->
                            <h4 class="mb-2"><?php echo htmlspecialchars($commenter['username'] ?? 'User'); ?></h4>
                            
                            <!-- Comment Count -->
                            <div class="mt-3">
                                <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                                    <i class="fas fa-comments fa-2x text-info"></i>
                                    <div>
                                        <div class="fs-1 fw-bold text-info"><?php echo (int)($commenter['total_comments'] ?? 0); ?></div>
                                        <small class="text-muted">Total Comments</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- View Profile Button -->
                            <?php if ($currentUser && (int)($commenter['id'] ?? 0) === (int)($currentUser['id'] ?? 0)): ?>
                                <div class="mt-3">
                                    <a href="<?php echo $baseUrl; ?>/profile" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-user-circle me-1"></i>My Profile
                                    </a>
                                </div>
                            <?php elseif ($currentUser): ?>
                                <div class="mt-3">
                                    <a href="<?php echo $baseUrl; ?>/dashboard" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-user me-1"></i>View Dashboard
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <div class="card bg-dark text-white">
                <div class="card-body py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted mb-3">No contributors yet</h3>
                    <p class="text-muted mb-4">Start writing reviews to appear on the leaderboard!</p>
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
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.4) !important;
    }
</style>

