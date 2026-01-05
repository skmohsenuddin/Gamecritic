<?php
$title = 'Followers | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">👥 Followers</h1>
                <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Tabs for Followers and Following -->
            <ul class="nav nav-tabs mb-4" id="followTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="followers-tab" data-bs-toggle="tab" data-bs-target="#followers" type="button" role="tab" aria-controls="followers" aria-selected="true">
                        <i class="fas fa-users"></i> Followers (<?php echo count($followers); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="following-tab" data-bs-toggle="tab" data-bs-target="#following" type="button" role="tab" aria-controls="following" aria-selected="false">
                        <i class="fas fa-user-check"></i> Following (<?php echo count($following); ?>)
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="followTabsContent">
                <!-- Followers Tab -->
                <div class="tab-pane fade show active" id="followers" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">People Following You</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($followers)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No Followers Yet</h5>
                                    <p class="text-muted">When people follow you, they'll appear here.</p>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($followers as $follower): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($follower['profile_picture'])): ?>
                                                            <img src="<?php echo $baseUrl . htmlspecialchars($follower['profile_picture']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($follower['username']); ?>" 
                                                                 class="rounded-circle me-3" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-user fa-2x text-white"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($follower['username']); ?></h6>
                                                            <small class="text-muted">
                                                                Following since <?php echo date('M j, Y', strtotime($follower['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <div class="ms-3">
                                                            <?php if (!$follower['is_following_back']): ?>
                                                                <button class="btn btn-sm btn-success follow-back-btn" 
                                                                        data-user-id="<?php echo (int)$follower['id']; ?>"
                                                                        data-username="<?php echo htmlspecialchars($follower['username']); ?>">
                                                                    <i class="fas fa-user-plus"></i> Follow Back
                                                                </button>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">
                                                                    <i class="fas fa-check"></i> Following
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
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

                <!-- Following Tab -->
                <div class="tab-pane fade" id="following" role="tabpanel">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">People You're Following</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($following)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Not Following Anyone Yet</h5>
                                    <p class="text-muted">Start following users to see their reviews and chat with them!</p>
                                    <a href="<?php echo $baseUrl; ?>/" class="btn btn-primary mt-3">
                                        <i class="fas fa-gamepad"></i> Browse Games
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($following as $user): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($user['profile_picture'])): ?>
                                                            <img src="<?php echo $baseUrl . htmlspecialchars($user['profile_picture']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                                                 class="rounded-circle me-3" 
                                                                 style="width: 60px; height: 60px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                                                 style="width: 60px; height: 60px;">
                                                                <i class="fas fa-user fa-2x text-white"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($user['username']); ?></h6>
                                                            <small class="text-muted">
                                                                Following since <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                                            </small>
                                                        </div>
                                                        <div class="ms-3">
                                                            <a href="<?php echo $baseUrl; ?>/chat?user_id=<?php echo (int)$user['id']; ?>" 
                                                               class="btn btn-sm btn-primary">
                                                                <i class="fas fa-comments"></i> Chat
                                                            </a>
                                                        </div>
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
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const baseUrl = '<?php echo $baseUrl; ?>';
    
    $('.follow-back-btn').on('click', function(e) {
        e.preventDefault();
        const $btn = $(this);
        const userId = $btn.data('user-id');
        const username = $btn.data('username');
        
        if (!userId) {
            alert('Invalid user ID');
            return;
        }
        
        if (confirm(`Follow ${username} back? You'll be able to chat with them.`)) {
            $.ajax({
                url: baseUrl + '/user/follow',
                method: 'POST',
                data: {
                    user_id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $btn.html('<i class="fas fa-check"></i> Following');
                        $btn.removeClass('btn-success follow-back-btn');
                        $btn.addClass('btn-secondary');
                        $btn.prop('disabled', true);
                        alert('You are now following ' + username + '! You can now chat with them.');
                    } else {
                        alert(response.message || 'Failed to follow user');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to follow user. Please try again.';
                    try {
                        const response = typeof xhr.responseJSON !== 'undefined' 
                            ? xhr.responseJSON 
                            : JSON.parse(xhr.responseText);
                        if (response && response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                    }
                    alert(errorMessage);
                }
            });
        }
    });
});
</script>

<style>
.follow-back-btn:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}
</style>

