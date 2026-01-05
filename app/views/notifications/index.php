<?php
$baseUrl = $baseUrl ?? '';
$notifications = $notifications ?? [];
$unreadCount = $unreadCount ?? 0;
$currentUser = $currentUser ?? null;
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-bell me-2"></i>Notifications & Digest</h2>
                <?php if ($unreadCount > 0): ?>
                    <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                        <i class="fas fa-check-double me-1"></i>Mark All as Read
                    </button>
                <?php endif; ?>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="card bg-dark border-secondary">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-white">No notifications yet</h5>
                        <p class="text-muted">You'll see notifications here when there are new reviews, games, or activity from followed reviewers.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="list-group-item list-group-item-action text-white <?php echo $notification['is_read'] ? 'bg-dark' : 'bg-secondary'; ?>" 
                             data-notification-id="<?php echo $notification['id']; ?>"
                             style="background-color: <?php echo $notification['is_read'] ? '#1e1e1e' : '#2a2a2a'; ?> !important; border-color: #333;">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <?php
                                        $icon = '';
                                        $badgeClass = '';
                                        $showFollowBack = false;
                                        $followerUserId = null;
                                        
                                        switch ($notification['type']) {
                                            case 'new_review':
                                                $icon = 'fas fa-comment';
                                                $badgeClass = 'bg-info';
                                                break;
                                            case 'followed_reviewer_review':
                                                $icon = 'fas fa-user-friends';
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'new_game':
                                                $icon = 'fas fa-gamepad';
                                                $badgeClass = 'bg-warning';
                                                break;
                                            case 'new_follower':
                                                $icon = 'fas fa-user-plus';
                                                $badgeClass = 'bg-primary';
                                                $showFollowBack = true;
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?> me-2">
                                            <i class="<?php echo $icon; ?>"></i>
                                        </span>
                                        <h6 class="mb-0 text-white <?php echo $notification['is_read'] ? '' : 'fw-bold'; ?>">
                                            <?php echo htmlspecialchars($notification['title']); ?>
                                        </h6>
                                        <?php if (!$notification['is_read']): ?>
                                            <span class="badge bg-danger ms-2">New</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="mb-1 text-white">
                                        <?php echo htmlspecialchars($notification['message']); ?>
                                    </p>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?php 
                                        $createdAt = new DateTime($notification['created_at']);
                                        $now = new DateTime();
                                        $diff = $now->diff($createdAt);
                                        
                                        if ($diff->days > 0) {
                                            echo $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->h > 0) {
                                            echo $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
                                        } elseif ($diff->i > 0) {
                                            echo $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
                                        } else {
                                            echo 'Just now';
                                        }
                                        ?>
                                    </small>
                                </div>
                                <div class="d-flex align-items-center ms-3">
                                    <?php if ($showFollowBack && $notification['link']): ?>
                                        <?php
                                        $link = $notification['link'];
                                        $followerUserId = isset($notification['follower_user_id']) 
                                            ? $notification['follower_user_id'] 
                                            : null;
                                        
                                        if (!$followerUserId && preg_match('/user_id=(\d+)/', $link, $matches)) {
                                            $followerUserId = (int)$matches[1];
                                        }
                                        
                                        $isFollowingBack = isset($notification['is_following_back']) 
                                            ? $notification['is_following_back'] 
                                            : false;
                                        ?>
                                        <?php if (!$isFollowingBack && $followerUserId): ?>
                                            <button class="btn btn-sm btn-success me-2 follow-back-btn" 
                                                    data-user-id="<?php echo $followerUserId; ?>"
                                                    data-username="<?php echo htmlspecialchars(explode(' started', $notification['message'])[0] ?? ''); ?>">
                                                <i class="fas fa-user-plus"></i> Add Back
                                            </button>
                                        <?php elseif ($isFollowingBack): ?>
                                            <span class="badge bg-secondary me-2">
                                                <i class="fas fa-check"></i> Following
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if ($notification['link'] && !$showFollowBack): ?>
                                        <a href="<?php echo $baseUrl . htmlspecialchars($notification['link']); ?>" 
                                           class="btn btn-sm btn-outline-primary me-2">
                                            <i class="fas fa-external-link-alt"></i> View
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-outline-danger delete-notification" 
                                            data-notification-id="<?php echo $notification['id']; ?>"
                                            title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = window.__BASE_URL__ || '';
    
    document.querySelectorAll('.list-group-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.delete-notification') || e.target.closest('a')) {
                return;
            }
            
            const notificationId = this.dataset.notificationId;
            if (notificationId && this.classList.contains('bg-dark')) {
                return; // Already read
            }
            
            fetch(baseUrl + '/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'notification_id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.classList.remove('bg-secondary');
                    this.classList.add('bg-dark');
                    this.style.backgroundColor = '#1e1e1e';
                    this.querySelector('.fw-bold')?.classList.remove('fw-bold');
                    this.querySelector('.badge.bg-danger')?.remove();
                    updateNotificationBadge();
                }
            });
        });
    });
    
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            fetch(baseUrl + '/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    }
    
    document.querySelectorAll('.delete-notification').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const notificationId = this.dataset.notificationId;
            
            if (confirm('Are you sure you want to delete this notification?')) {
                fetch(baseUrl + '/notifications/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'notification_id=' + notificationId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.list-group-item').remove();
                        updateNotificationBadge();
                    }
                });
            }
        });
    });
    
    function updateNotificationBadge() {
        fetch(baseUrl + '/notifications/get-unread-count')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            });
    }
    
    updateNotificationBadge();
    
    document.querySelectorAll('.follow-back-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const userId = this.dataset.userId;
            const username = this.dataset.username;
            
            if (!userId) {
                alert('Invalid user ID');
                return;
            }
            
            if (confirm(`Add ${username} back? You'll be able to chat with them.`)) {
                const formData = new FormData();
                formData.append('user_id', userId);
                
                fetch(baseUrl + '/user/follow', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.innerHTML = '<i class="fas fa-check"></i> Following';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-secondary');
                        this.disabled = true;
                        alert('You are now following ' + username + '! You can now chat with them.');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        alert(data.message || 'Failed to follow user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        });
    });
});
</script>

