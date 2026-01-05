<?php
$title = 'Chat | GameCritic';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">💬 Chat</h1>
                <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">Conversations</h5>
                        </div>
                        <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                            <?php if (empty($conversations) && empty($following)): ?>
                                <div class="p-3 text-center text-muted">
                                    <p>No conversations yet. Start following users to chat with them!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($conversations as $conv): ?>
                                    <a href="#" class="conversation-item d-flex align-items-center p-3 border-bottom text-decoration-none text-dark" 
                                       data-user-id="<?php echo (int)$conv['other_user_id']; ?>"
                                       data-username="<?php echo htmlspecialchars($conv['username']); ?>">
                                        <?php if (!empty($conv['profile_picture'])): ?>
                                            <img src="<?php echo $baseUrl . htmlspecialchars($conv['profile_picture']); ?>" 
                                                 alt="<?php echo htmlspecialchars($conv['username']); ?>" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong><?php echo htmlspecialchars($conv['username']); ?></strong>
                                                <?php if ((int)$conv['unread_count'] > 0): ?>
                                                    <span class="badge bg-danger"><?php echo (int)$conv['unread_count']; ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($conv['last_message'])): ?>
                                                <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                                    <?php echo htmlspecialchars(substr($conv['last_message'], 0, 50)); ?>
                                                    <?php echo strlen($conv['last_message']) > 50 ? '...' : ''; ?>
                                                </small>
                                                <small class="text-muted">
                                                    <?php echo date('M j, g:i A', strtotime($conv['last_message_time'])); ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endforeach; ?>

                                <?php 
                                $chattedUserIds = array_column($conversations, 'other_user_id');
                                foreach ($following as $user): 
                                    if (!in_array($user['id'], $chattedUserIds)):
                                ?>
                                    <a href="#" class="conversation-item d-flex align-items-center p-3 border-bottom text-decoration-none text-dark" 
                                       data-user-id="<?php echo (int)$user['id']; ?>"
                                       data-username="<?php echo htmlspecialchars($user['username']); ?>">
                                        <?php if (!empty($user['profile_picture'])): ?>
                                            <img src="<?php echo $baseUrl . htmlspecialchars($user['profile_picture']); ?>" 
                                                 alt="<?php echo htmlspecialchars($user['username']); ?>" 
                                                 class="rounded-circle me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                            <small class="text-muted d-block">Start a conversation</small>
                                        </div>
                                    </a>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card" style="background: #1a1a1a; border-color: #333;">
                        <div class="card-header bg-dark text-white" id="chatHeader" style="background: #2d2d2d !important; border-color: #444;">
                            <h5 class="mb-0">Select a conversation to start chatting</h5>
                        </div>
                        <div class="card-body p-0" style="height: 500px; display: flex; flex-direction: column; background: #000000;">
                            <div id="chatMessages" class="flex-grow-1 p-3" style="overflow-y: auto; height: 400px; background: #000000; color: #ffffff;">
                                <div class="text-center text-muted mt-5" style="color: #888 !important;">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>Select a conversation from the list to start chatting</p>
                                </div>
                            </div>

                            <div class="border-top p-3" id="chatInputArea" style="display: none; background: #1a1a1a; border-color: #333 !important;">
                                <form id="chatForm" class="d-flex">
                                    <input type="hidden" id="chatReceiverId" name="receiver_id">
                                    <input type="text" 
                                           id="chatMessageInput" 
                                           class="form-control me-2" 
                                           placeholder="Type a message..." 
                                           autocomplete="off"
                                           required
                                           style="background: #2d2d2d; border-color: #444; color: #ffffff;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Send
                                    </button>
                                </form>
                            </div>
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
    let currentChatUserId = null;
    let lastMessageId = null;
    let pollInterval = null;

    $('.conversation-item').on('click', function(e) {
        e.preventDefault();
        const userId = $(this).data('user-id');
        const username = $(this).data('username');
        
        $('.conversation-item').removeClass('active bg-light');
        $(this).addClass('active bg-light');
        
        loadConversation(userId, username);
    });

    function loadConversation(userId, username) {
        currentChatUserId = userId;
        lastMessageId = null;
        
        $('#chatHeader h5').text('Chat with ' + username);
        $('#chatReceiverId').val(userId);
        $('#chatInputArea').show();
        
        $('#chatMessages').empty();
        
        fetchMessages();
        
        if (pollInterval) {
            clearInterval(pollInterval);
        }
        pollInterval = setInterval(fetchNewMessages, 2000);
    }

    function fetchMessages() {
        if (!currentChatUserId) return;
        
        $.ajax({
            url: '<?php echo $baseUrl; ?>/chat/get-conversation?user_id=' + currentChatUserId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.messages) {
                    displayMessages(response.messages);
                    if (response.messages.length > 0) {
                        lastMessageId = response.messages[response.messages.length - 1].id;
                    }
                }
            },
            error: function() {
                console.error('Failed to load messages');
            }
        });
    }

    function fetchNewMessages() {
        if (!currentChatUserId) return;
        
        const url = lastMessageId 
            ? '<?php echo $baseUrl; ?>/chat/get-new-messages?user_id=' + currentChatUserId + '&last_message_id=' + lastMessageId
            : '<?php echo $baseUrl; ?>/chat/get-conversation?user_id=' + currentChatUserId;
        
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.messages && response.messages.length > 0) {
                    appendMessages(response.messages);
                    lastMessageId = response.messages[response.messages.length - 1].id;
                }
            },
            error: function() {
                console.error('Failed to fetch new messages');
            }
        });
    }

    function displayMessages(messages) {
        const messagesContainer = $('#chatMessages');
        messagesContainer.empty();
        
        messages.forEach(function(msg) {
            appendMessage(msg);
        });
        
        scrollToBottom();
    }

    function appendMessages(messages) {
        messages.forEach(function(msg) {
            appendMessage(msg);
        });
        scrollToBottom();
    }

    function appendMessage(msg) {
        const currentUserId = <?php echo isset($currentUser['id']) ? (int)$currentUser['id'] : 0; ?>;
        const isSender = msg.sender_id == currentUserId;
        const messageClass = isSender ? 'sent' : 'received';
        const alignClass = isSender ? 'text-end' : 'text-start';
        const profilePicture = isSender 
            ? '<?php echo !empty($currentUser['profile_picture']) ? $baseUrl . htmlspecialchars($currentUser['profile_picture']) : ""; ?>'
            : (msg.sender_profile_picture ? '<?php echo $baseUrl; ?>' + msg.sender_profile_picture : '');
        const username = isSender 
            ? '<?php echo htmlspecialchars($currentUser['username'] ?? ($currentUser['name'] ?? 'You')); ?>'
            : msg.sender_username;
        
        const messageHtml = `
            <div class="message ${messageClass} mb-3 ${alignClass}">
                <div class="d-flex ${isSender ? 'flex-row-reverse' : 'flex-row'} align-items-start">
                    ${!isSender ? `
                        <div class="me-2">
                            ${profilePicture ? `
                                <img src="${profilePicture}" alt="${username}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                            ` : `
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            `}
                        </div>
                    ` : ''}
                    <div class="message-bubble ${isSender ? 'bg-primary text-white' : 'bg-dark text-white border border-secondary'} rounded p-2" style="max-width: 70%;">
                        ${!isSender ? `<small class="d-block text-muted mb-1">${username}</small>` : ''}
                        <div>${escapeHtml(msg.message)}</div>
                        <small class="d-block mt-1 ${isSender ? 'text-white-50' : 'text-muted'}" style="font-size: 0.75rem;">
                            ${formatTime(msg.created_at)}
                        </small>
                    </div>
                    ${isSender ? `
                        <div class="ms-2">
                            ${profilePicture ? `
                                <img src="${profilePicture}" alt="${username}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                            ` : `
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            `}
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        $('#chatMessages').append(messageHtml);
    }

    function scrollToBottom() {
        const messagesContainer = $('#chatMessages');
        messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        
        if (minutes < 1) return 'Just now';
        if (minutes < 60) return minutes + 'm ago';
        if (minutes < 1440) return Math.floor(minutes / 60) + 'h ago';
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    $('#chatForm').on('submit', function(e) {
        e.preventDefault();
        
        const message = $('#chatMessageInput').val().trim();
        if (!message || !currentChatUserId) return;
        
        $.ajax({
            url: '<?php echo $baseUrl; ?>/chat/send-message',
            method: 'POST',
            data: {
                receiver_id: currentChatUserId,
                message: message
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.message) {
                    appendMessage(response.message);
                    $('#chatMessageInput').val('');
                    scrollToBottom();
                    lastMessageId = response.message.id;
                } else {
                    alert(response.message || 'Failed to send message');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send message. Please try again.';
                
                if (xhr.status === 401) {
                    window.location.href = '<?php echo $baseUrl; ?>/login';
                    return;
                } else if (xhr.status === 403) {
                    errorMessage = 'You can only send messages to users you follow. Please follow this user first.';
                } else {
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
                }
                
                alert(errorMessage);
                console.error('Chat error:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    responseText: xhr.responseText,
                    responseJSON: xhr.responseJSON
                });
            }
        });
    });

    $(window).on('beforeunload', function() {
        if (pollInterval) {
            clearInterval(pollInterval);
        }
    });
});
</script>

<style>
.conversation-item:hover {
    background-color: #f8f9fa !important;
    cursor: pointer;
}

.conversation-item.active {
    background-color: #e3f2fd !important;
}

.message-bubble {
    word-wrap: break-word;
    word-break: break-word;
}

#chatMessages {
    scrollbar-width: thin;
    scrollbar-color: #555 #1a1a1a;
}

#chatMessages::-webkit-scrollbar {
    width: 6px;
}

#chatMessages::-webkit-scrollbar-track {
    background: #1a1a1a;
}

#chatMessages::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb:hover {
    background: #777;
}

#chatMessageInput::placeholder {
    color: #999;
}

#chatMessageInput:focus {
    background: #333 !important;
    border-color: #555 !important;
    color: #ffffff !important;
}
</style>

