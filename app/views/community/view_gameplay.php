<?php
$title = htmlspecialchars($gameplay['title']) . ' | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <a href="<?php echo $baseUrl; ?>/gameplays" class="btn btn-outline-secondary mb-3">
                    <i class="fas fa-arrow-left"></i> Back to Gameplays
                </a>
            </div>

            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <?php if (!empty($gameplay['profile_picture'])): ?>
                            <img src="<?php echo htmlspecialchars($gameplay['profile_picture']); ?>" 
                                 alt="<?php echo htmlspecialchars($gameplay['username']); ?>" 
                                 class="rounded-circle me-3" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        <?php endif; ?>
                        <div>
                            <h5 class="mb-0"><?php echo htmlspecialchars($gameplay['username']); ?></h5>
                            <small class="text-muted"><?php echo date('F d, Y \a\t g:i A', strtotime($gameplay['created_at'])); ?></small>
                        </div>
                    </div>

                    <h2 class="mb-3"><?php echo htmlspecialchars($gameplay['title']); ?></h2>
                    
                    <div class="mb-3">
                        <a href="<?php echo $baseUrl; ?>/game/<?php echo $gameplay['game_id']; ?>" class="text-info text-decoration-none">
                            <i class="fas fa-gamepad me-1"></i><?php echo htmlspecialchars($gameplay['game_title']); ?>
                        </a>
                    </div>
                    
                    <?php if (!empty($gameplay['description'])): ?>
                        <p class="text-white mb-4"><?php echo nl2br(htmlspecialchars($gameplay['description'])); ?></p>
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <?php 
                        $videoSrc = $baseUrl . htmlspecialchars($gameplay['video_path']);
                        $videoExt = strtolower(pathinfo($gameplay['video_path'], PATHINFO_EXTENSION));
                        $videoType = 'video/mp4';
                        if ($videoExt === 'webm') $videoType = 'video/webm';
                        elseif ($videoExt === 'ogg') $videoType = 'video/ogg';
                        elseif ($videoExt === 'mov') $videoType = 'video/quicktime';
                        elseif ($videoExt === 'avi') $videoType = 'video/x-msvideo';
                        ?>
                        <video class="w-100 rounded" controls preload="metadata" style="max-height: 600px;">
                            <source src="<?php echo $videoSrc; ?>" type="<?php echo $videoType; ?>">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    
                    <?php if (isset($currentUser) && $currentUser && (int)$currentUser['id'] === (int)$gameplay['user_id']): ?>
                        <div class="mt-4">
                            <form action="<?php echo $baseUrl; ?>/gameplay/<?php echo $gameplay['id']; ?>/delete" method="POST" class="d-inline">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this gameplay?');">
                                    <i class="fas fa-trash me-1"></i>Delete Gameplay
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

