<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $title ?? 'GameCritic'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>/css/style.css?v=<?php echo time(); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo $baseUrl; ?>/">🎮 GameCritic</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/">Home</a></li>
                    <li class="nav-item"><a class="nav-link"
                            href="<?php echo $baseUrl; ?>/filter?genre=action">Action</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo $baseUrl; ?>/filter?genre=rpg">RPG</a>
                    </li>
                    <li class="nav-item"><a class="nav-link"
                            href="<?php echo $baseUrl; ?>/filter?genre=strategy">Strategy</a></li>
                </ul>

                <?php if (isset($currentUser)): ?>
                    <div class="d-flex align-items-center me-3">
                        <!-- Search Bar -->
                        <div class="position-relative me-3" style="min-width: 250px;">
                            <form class="d-flex" action="<?php echo $baseUrl; ?>/search" method="GET">
                                <input class="form-control form-control-sm" type="search" name="q" id="searchInput" 
                                       placeholder="Search games..." autocomplete="off"
                                       aria-label="Search" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                                <button class="btn btn-outline-light btn-sm ms-2" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                            <div id="searchSuggestions" class="search-suggestions"></div>
                        </div>
                        <div class="form-check form-switch me-3">
                            <input class="form-check-input" type="checkbox" id="neonToggle">
                            <label class="form-check-label text-light small" for="neonToggle">Neon</label>
                        </div>
                        <a href="<?php echo $baseUrl; ?>/notifications" class="btn btn-outline-info btn-sm me-3 position-relative" title="Notifications & Digest">
                            <i class="fas fa-bell"></i>
                            <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                0
                            </span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>/chat" class="btn btn-outline-success btn-sm me-3 position-relative" title="Chat">
                            <i class="fas fa-comments"></i>
                            <span id="chatBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                                0
                            </span>
                        </a>
                        <span class="navbar-text text-light me-2">Hello,
                            <?php echo htmlspecialchars($currentUser['username'] ?? $currentUser['name']); ?></span>
                        <a href="<?php echo $baseUrl; ?>/profile" class="text-decoration-none">
                            <?php if (!empty($currentUser['profile_picture'])): ?>
                                <img src="<?php echo $baseUrl . htmlspecialchars($currentUser['profile_picture']); ?>"
                                    alt="Profile Picture" class="rounded-circle"
                                    style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff;">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px; border: 2px solid #fff;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                    </div>
                    <?php if ($currentUser['is_admin']): ?>
                        <a href="<?php echo $baseUrl; ?>/admin/dashboard" class="btn btn-warning me-2">Admin</a>
                    <?php else: ?>
                        <a href="<?php echo $baseUrl; ?>/dashboard" class="btn btn-primary me-2">Dashboard</a>
                        <a href="<?php echo $baseUrl; ?>/followers" class="btn btn-warning me-2" title="View Followers">
                            <i class="fas fa-user-friends"></i> Followers
                        </a>
                        <a href="<?php echo $baseUrl; ?>/followed-reviews" class="btn btn-success me-2">
                            <i class="fas fa-users"></i> Followed
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo $baseUrl; ?>/logout" class="btn btn-outline-light">Logout</a>
                <?php else: ?>
                    <!-- Search Bar -->
                    <div class="position-relative me-3" style="min-width: 250px;">
                        <form class="d-flex" action="<?php echo $baseUrl; ?>/search" method="GET">
                            <input class="form-control form-control-sm" type="search" name="q" id="searchInput" 
                                   placeholder="Search games..." autocomplete="off"
                                   aria-label="Search" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                            <button class="btn btn-outline-light btn-sm ms-2" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <div id="searchSuggestions" class="search-suggestions"></div>
                    </div>
                    <div class="form-check form-switch me-3 d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" id="neonToggle">
                        <label class="form-check-label text-light small ms-2" for="neonToggle">Neon</label>
                    </div>
                    <a href="<?php echo $baseUrl; ?>/notifications" class="btn btn-outline-info btn-sm me-3 position-relative" title="Notifications & Digest">
                        <i class="fas fa-bell"></i>
                        <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                            0
                        </span>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/chat" class="btn btn-outline-success btn-sm me-3 position-relative" title="Chat">
                        <i class="fas fa-comments"></i>
                        <span id="chatBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none;">
                            0
                        </span>
                    </a>
                    <a href="<?php echo $baseUrl; ?>/login" class="btn btn-danger me-2">Login</a>
                    <a href="<?php echo $baseUrl; ?>/signup" class="btn btn-outline-light">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?php echo $content; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 p-4">
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div class="mb-3 mb-md-0">
                <strong>GameCritic</strong> © 2025 | All rights reserved.
            </div>
            <div>
                <a href="<?php echo $baseUrl; ?>/about" class="text-white me-3">About</a>
                <a href="<?php echo $baseUrl; ?>/contact" class="text-white me-3">Contact</a>
                <a href="https://facebook.com" class="text-white me-2">
                    <img src="<?php echo $baseUrl; ?>/images/facebook.png" height="24" alt="Facebook">
                </a>
                <a href="https://youtube.com" class="text-white">
                    <img src="<?php echo $baseUrl; ?>/images/youtube.png" height="24" alt="YouTube">
                </a>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS -->
    <script>
        window.__BASE_URL__ = '<?php echo $baseUrl; ?>';

        const neonToggle = document.getElementById('neonToggle');
        const body = document.body;
        const isLoggedIn = <?php echo isset($currentUser) ? 'true' : 'false'; ?>;

        if (isLoggedIn && localStorage.getItem('neonMode') === 'enabled') {
            body.classList.add('neon-mode');
            if (neonToggle) neonToggle.checked = true;
        } else if (!isLoggedIn) {
            body.classList.remove('neon-mode');
            if (neonToggle) neonToggle.checked = false;
        }

        if (neonToggle) {
            neonToggle.addEventListener('change', (e) => {
                if (!isLoggedIn) {
                    e.preventDefault();
                    neonToggle.checked = false;
                    alert("Log in na korle dekhba kemne?");
                    return;
                }

                if (neonToggle.checked) {
                    body.classList.add('neon-mode');
                    localStorage.setItem('neonMode', 'enabled');
                } else {
                    body.classList.remove('neon-mode');
                    localStorage.setItem('neonMode', 'disabled');
                }
            });
        }

        const searchInput = document.getElementById('searchInput');
        const searchSuggestions = document.getElementById('searchSuggestions');
        let searchTimeout = null;
        let currentSuggestions = [];

        if (searchInput && searchSuggestions) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                
                clearTimeout(searchTimeout);
                
                if (query.length < 1) {
                    searchSuggestions.innerHTML = '';
                    searchSuggestions.classList.remove('show');
                    return;
                }

                searchTimeout = setTimeout(() => {
                    fetchSuggestions(query);
                }, 150);
            });

            searchInput.addEventListener('focus', function() {
                if (currentSuggestions.length > 0) {
                    searchSuggestions.classList.add('show');
                }
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
                    searchSuggestions.classList.remove('show');
                }
            });

            searchInput.addEventListener('keydown', function(e) {
                const items = searchSuggestions.querySelectorAll('.suggestion-item');
                const activeItem = searchSuggestions.querySelector('.suggestion-item.active');
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (activeItem) {
                        activeItem.classList.remove('active');
                        const next = activeItem.nextElementSibling;
                        if (next) {
                            next.classList.add('active');
                        } else {
                            items[0]?.classList.add('active');
                        }
                    } else {
                        items[0]?.classList.add('active');
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (activeItem) {
                        activeItem.classList.remove('active');
                        const prev = activeItem.previousElementSibling;
                        if (prev) {
                            prev.classList.add('active');
                        } else {
                            items[items.length - 1]?.classList.add('active');
                        }
                    } else {
                        items[items.length - 1]?.classList.add('active');
                    }
                } else if (e.key === 'Enter' && activeItem) {
                    e.preventDefault();
                    const link = activeItem.querySelector('a');
                    if (link) {
                        window.location.href = link.href;
                    }
                } else if (e.key === 'Escape') {
                    searchSuggestions.classList.remove('show');
                }
            });
        }

        function fetchSuggestions(query) {
            const baseUrl = window.__BASE_URL__ || '';
            const pythonServiceUrl = 'http://127.0.0.1:5000';
            
            searchSuggestions.innerHTML = '<div class="suggestion-item no-results"><i class="fas fa-spinner fa-spin me-2"></i>Searching...</div>';
            searchSuggestions.classList.add('show');
            
            const timeoutPromise = new Promise((_, reject) => {
                setTimeout(() => reject(new Error('Timeout')), 3000);
            });
            
            Promise.race([
                fetch(`${pythonServiceUrl}/search/suggestions?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                }),
                timeoutPromise
            ])
                .then(response => {
                    if (response && response.ok) {
                        return response.json();
                    }
                    throw new Error('Python service not available');
                })
                .then(data => {
                    if (data && Array.isArray(data)) {
                        currentSuggestions = data;
                        displaySuggestions(data, query);
                        return;
                    }
                    throw new Error('Invalid response');
                })
                .catch(error => {
                    console.log('Python unavailable, trying PHP...', error.message);
                    return Promise.race([
                        fetch(`${baseUrl}/search/suggestions?q=${encodeURIComponent(query)}`, {
                            method: 'GET',
                            headers: { 'Accept': 'application/json' }
                        }),
                        timeoutPromise
                    ]);
                })
                .then(response => {
                    if (response && response.ok) {
                        return response.json();
                    }
                    throw new Error('PHP service not available');
                })
                .then(data => {
                    if (data && Array.isArray(data)) {
                        currentSuggestions = data;
                        displaySuggestions(data, query);
                    } else {
                        throw new Error('Invalid response');
                    }
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    searchSuggestions.innerHTML = '<div class="suggestion-item no-results"><i class="fas fa-exclamation-triangle me-2"></i>Unable to load suggestions</div>';
                    searchSuggestions.classList.add('show');
                });
        }

        function displaySuggestions(suggestions, query) {
            if (suggestions.length === 0) {
                searchSuggestions.innerHTML = '<div class="suggestion-item no-results"><i class="fas fa-search me-2"></i>No games found matching "' + query + '"</div>';
                searchSuggestions.classList.add('show');
                return;
            }

            const html = suggestions.map((game, index) => {
                const title = game.title;
                const gameUrl = `${window.__BASE_URL__ || ''}/game/${game.id}`;
                const coverImage = game.cover_image ? `${window.__BASE_URL__ || ''}${game.cover_image}` : '';
                const activeClass = index === 0 ? 'active' : '';
                
                return `
                    <div class="suggestion-item ${activeClass}">
                        <a href="${gameUrl}" class="d-flex align-items-center text-decoration-none text-white">
                            ${coverImage ? `<img src="${coverImage}" alt="${title}" class="suggestion-image me-2">` : '<div class="suggestion-image me-2 bg-secondary d-flex align-items-center justify-content-center"><i class="fas fa-gamepad"></i></div>'}
                            <span class="flex-grow-1">${highlightMatch(title, query)}</span>
                            <i class="fas fa-chevron-right ms-2 text-muted"></i>
                        </a>
                    </div>
                `;
            }).join('');

            searchSuggestions.innerHTML = html;
            searchSuggestions.classList.add('show');
        }

        function highlightMatch(text, query) {
            if (!query) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<strong>$1</strong>');
        }

        function updateNotificationBadge() {
            if (!isLoggedIn) return;
            
            const baseUrl = window.__BASE_URL__ || '';
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
                })
                .catch(error => {
                    console.error('Error fetching notification count:', error);
                });
        }

        function updateChatBadge() {
            if (!isLoggedIn) return;
            
            const baseUrl = window.__BASE_URL__ || '';
            fetch(baseUrl + '/chat/get-unread-count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('chatBadge');
                    if (badge) {
                        if (data.count > 0) {
                            badge.textContent = data.count;
                            badge.style.display = 'block';
                        } else {
                            badge.style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching chat count:', error);
                });
        }

        if (isLoggedIn) {
            updateNotificationBadge();
            updateChatBadge();
            setInterval(updateNotificationBadge, 30000);
            setInterval(updateChatBadge, 30000);
        }
    </script>

    <style>
        .search-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #212529;
            border: 1px solid #495057;
            border-radius: 0.25rem;
            margin-top: 0.25rem;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .search-suggestions.show {
            display: block;
        }

        .suggestion-item {
            padding: 0.75rem;
            border-bottom: 1px solid #495057;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .suggestion-item:last-child {
            border-bottom: none;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: #495057;
            transform: translateX(5px);
        }

        .suggestion-item a {
            width: 100%;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .suggestion-item a:hover {
            color: #fff !important;
        }

        .suggestion-image {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 0.25rem;
            flex-shrink: 0;
        }

        .suggestion-image.bg-secondary {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .suggestion-item.no-results {
            color: #6c757d;
            text-align: center;
            padding: 1rem;
            cursor: default;
        }

        .suggestion-item.no-results:hover {
            background-color: transparent;
            transform: none;
        }

        body.neon-mode .search-suggestions {
            background: rgba(0, 0, 0, 0.95);
            border-color: #00f3ff;
            box-shadow: 0 4px 6px rgba(0, 243, 255, 0.3);
        }

        body.neon-mode .suggestion-item:hover,
        body.neon-mode .suggestion-item.active {
            background-color: rgba(0, 243, 255, 0.2);
            box-shadow: 0 0 10px rgba(0, 243, 255, 0.5);
        }

        body.neon-mode .suggestion-item {
            border-bottom-color: #00f3ff;
        }
    </style>

    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>