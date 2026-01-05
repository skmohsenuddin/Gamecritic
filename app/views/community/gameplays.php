<?php
$title = 'Gameplays | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary"><i class="fas fa-video me-2"></i>Gameplays</h1>
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

            <?php if (isset($currentUser) && $currentUser): ?>
            <div class="card bg-dark text-white mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-upload me-2"></i>Upload New Gameplay</h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo $baseUrl; ?>/gameplays/upload" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="game_id" class="form-label">Select Game</label>
                                <select class="form-select bg-dark text-white" id="game_id" name="game_id" required>
                                    <option value="">Choose a game...</option>
                                    <?php foreach ($games as $game): ?>
                                        <option value="<?php echo $game['id']; ?>"><?php echo htmlspecialchars($game['title']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control bg-dark text-white" id="title" name="title" placeholder="Enter gameplay title" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control bg-dark text-white" id="description" name="description" rows="3" placeholder="Describe your gameplay..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="video" class="form-label">Video File</label>
                            <input type="file" class="form-control bg-dark text-white" id="video" name="video" accept="video/*" required>
                            <small class="text-muted">Supported formats: MP4, WebM, OGG, MOV, AVI (Max 100MB)</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Gameplay
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Please <a href="<?php echo $baseUrl; ?>/login">login</a> to upload gameplays.
            </div>
            <?php endif; ?>

            <h3 class="text-white mb-3">Recent Gameplays</h3>
            <?php if (empty($gameplays)): ?>
                <div class="alert alert-secondary">
                    <i class="fas fa-video-slash me-2"></i>No gameplays uploaded yet. Be the first to share!
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($gameplays as $gameplay): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card bg-dark text-white h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($gameplay['profile_picture'])): ?>
                                            <img src="<?php echo htmlspecialchars($gameplay['profile_picture']); ?>" 
                                                 alt="<?php echo htmlspecialchars($gameplay['username']); ?>" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-2" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <strong><?php echo htmlspecialchars($gameplay['username']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo date('M d, Y', strtotime($gameplay['created_at'])); ?></small>
                                        </div>
                                    </div>
                                    
                                    <h5 class="card-title"><?php echo htmlspecialchars($gameplay['title']); ?></h5>
                                    
                                    <div class="mb-2">
                                        <a href="<?php echo $baseUrl; ?>/game/<?php echo $gameplay['game_id']; ?>" class="text-info text-decoration-none">
                                            <i class="fas fa-gamepad me-1"></i><?php echo htmlspecialchars($gameplay['game_title']); ?>
                                        </a>
                                    </div>
                                    
                                    <?php if (!empty($gameplay['description'])): ?>
                                        <p class="card-text text-muted small"><?php echo htmlspecialchars(substr($gameplay['description'], 0, 100)); ?><?php echo strlen($gameplay['description']) > 100 ? '...' : ''; ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <?php 
                                        $videoSrc = $baseUrl . htmlspecialchars($gameplay['video_path']);
                                        $videoExt = strtolower(pathinfo($gameplay['video_path'], PATHINFO_EXTENSION));
                                        $videoType = 'video/mp4';
                                        if ($videoExt === 'webm') $videoType = 'video/webm';
                                        elseif ($videoExt === 'ogg') $videoType = 'video/ogg';
                                        elseif ($videoExt === 'mov') $videoType = 'video/quicktime';
                                        elseif ($videoExt === 'avi') $videoType = 'video/x-msvideo';
                                        ?>
                                        <video class="w-100 rounded" controls preload="metadata" style="max-height: 200px;">
                                            <source src="<?php echo $videoSrc; ?>" type="<?php echo $videoType; ?>">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <a href="<?php echo $baseUrl; ?>/gameplay/<?php echo $gameplay['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Full
                                        </a>
                                        <?php if (isset($currentUser) && $currentUser && (int)$currentUser['id'] === (int)$gameplay['user_id']): ?>
                                            <form action="<?php echo $baseUrl; ?>/gameplay/<?php echo $gameplay['id']; ?>/delete" method="POST" class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this gameplay?');">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

