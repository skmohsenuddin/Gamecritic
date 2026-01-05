<?php
$title = 'AI Game Suggestions | GameCritic';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card bg-dark text-white">
                <div class="card-body p-5">
                    <div class="text-center mb-5">
                        <h2 class="display-6 fw-bold mb-2">
                            <i class="fas fa-robot text-primary me-2"></i>AI Game Suggestions
                        </h2>
                        <p class="text-muted">Find your next obsession by category or how you're feeling.</p>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <!-- Mode Toggle -->
                    <div class="d-flex justify-content-center mb-5">
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" name="mode_toggle" id="mode_cat" 
                                   value="category" <?php echo (!isset($details) || $details['mode'] === 'category') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="mode_cat">By Category</label>

                            <input type="radio" class="btn-check" name="mode_toggle" id="mode_mood" 
                                   value="mood" <?php echo (isset($details) && $details['mode'] === 'mood') ? 'checked' : ''; ?>>
                            <label class="btn btn-outline-primary" for="mode_mood">By Mood</label>
                        </div>
                    </div>

                    <form action="<?php echo $baseUrl; ?>/suggestion" method="post" id="suggestionForm">
                        <input type="hidden" name="mode" id="modeInput" value="<?php echo isset($details) ? htmlspecialchars($details['mode']) : 'category'; ?>">

                        <!-- Category Mode -->
                        <div id="categoryWrapper" class="<?php echo (!isset($details) || $details['mode'] === 'category') ? '' : 'd-none'; ?>">
                            <div class="d-flex justify-content-center gap-3">
                                <select name="genre" class="form-select" style="max-width: 300px; background: #1a1a2e; color: white; border-color: #444;">
                                    <option value="All">All Categories</option>
                                    <?php foreach ($availableGenres as $genre): ?>
                                        <option value="<?php echo htmlspecialchars($genre); ?>" 
                                                <?php echo (isset($details) && $details['genre'] === $genre) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($genre); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary px-4">
                                    Generate <i class="fas fa-sparkles ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Mood Mode -->
                        <div id="moodWrapper" class="<?php echo (isset($details) && $details['mode'] === 'mood') ? '' : 'd-none'; ?>">
                            <div class="row g-3 mb-4">
                                <?php 
                                $moods = [
                                    ['value' => 'Happy', 'icon' => 'fa-smile', 'color' => '#ffca28'],
                                    ['value' => 'Sad', 'icon' => 'fa-sad-tear', 'color' => '#42a5f5'],
                                    ['value' => 'Angry', 'icon' => 'fa-angry', 'color' => '#ef5350'],
                                    ['value' => 'Chill', 'icon' => 'fa-couch', 'color' => '#66bb6a'],
                                    ['value' => 'Adventurous', 'icon' => 'fa-mountain', 'color' => '#ffa726']
                                ];
                                ?>
                                <?php foreach ($moods as $mood): ?>
                                    <div class="col-md-2 col-6">
                                        <input type="radio" name="mood" id="mood_<?php echo strtolower($mood['value']); ?>" 
                                               value="<?php echo htmlspecialchars($mood['value']); ?>" 
                                               class="d-none" 
                                               <?php echo (isset($details) && $details['mood'] === $mood['value']) ? 'checked' : ''; ?>>
                                        <label for="mood_<?php echo strtolower($mood['value']); ?>" 
                                               class="d-flex flex-column align-items-center p-3 rounded border border-secondary mood-card"
                                               style="cursor: pointer; background: rgba(255,255,255,0.04); transition: all 0.3s;">
                                            <i class="fas <?php echo $mood['icon']; ?> mb-2" style="font-size: 2rem; color: #555;"></i>
                                            <span style="font-size: 0.85rem; font-weight: 600; color: #777;">
                                                <?php echo htmlspecialchars($mood['value']); ?>
                                            </span>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    Suggest for my Mood
                                </button>
                            </div>
                        </div>
                    </form>

                    <?php if (isset($details) && !empty($details['games'])): ?>
                        <hr class="my-5 border-secondary opacity-25">
                        <div class="results-section">
                            <div class="text-center mb-4">
                                <h4 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-magic me-2"></i>Our AI Picked:
                                </h4>
                                <p class="text-light-emphasis small px-4"><?php echo htmlspecialchars($details['reason']); ?></p>
                            </div>

                            <div class="list-group">
                                <?php foreach ($details['games'] as $game): ?>
                                    <?php if ($game['id'] > 0): ?>
                                        <a href="<?php echo $baseUrl; ?>/game/<?php echo (int)$game['id']; ?>" 
                                           class="list-group-item list-group-item-action bg-dark text-white border-secondary d-flex justify-content-between align-items-center">
                                            <span><?php echo htmlspecialchars($game['title']); ?></span>
                                            <i class="fas fa-chevron-right text-primary opacity-50"></i>
                                        </a>
                                    <?php else: ?>
                                        <div class="list-group-item bg-dark text-white border-secondary">
                                            <?php echo htmlspecialchars($game['title']); ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                            <?php if (!empty($details['games']) && $details['games'][0]['id'] > 0): ?>
                                <div class="text-center mt-4">
                                    <form action="<?php echo $baseUrl; ?>/suggestion/submit" method="post">
                                        <input type="hidden" name="games_json" value='<?php echo json_encode($details['games']); ?>'>
                                        <input type="hidden" name="reason" value="<?php echo htmlspecialchars($details['reason']); ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-save me-1"></i> Save Recommendations
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modeInput = document.getElementById('modeInput');
    const catWrapper = document.getElementById('categoryWrapper');
    const moodWrapper = document.getElementById('moodWrapper');
    const modeCat = document.getElementById('mode_cat');
    const modeMood = document.getElementById('mode_mood');

    function updateMode(mode) {
        modeInput.value = mode;
        if (mode === 'mood') {
            catWrapper.classList.add('d-none');
            moodWrapper.classList.remove('d-none');
            modeMood.checked = true;
        } else {
            catWrapper.classList.remove('d-none');
            moodWrapper.classList.add('d-none');
            modeCat.checked = true;
        }
    }

    modeCat.addEventListener('change', () => updateMode('category'));
    modeMood.addEventListener('change', () => updateMode('mood'));

    document.querySelectorAll('.mood-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(255,255,255,0.08)';
            this.style.borderColor = 'rgba(255,255,255,0.3)';
            this.style.transform = 'scale(1.05)';
        });
        card.addEventListener('mouseleave', function() {
            if (!this.previousElementSibling.checked) {
                this.style.background = 'rgba(255,255,255,0.04)';
                this.style.borderColor = 'rgba(255,255,255,0.1)';
                this.style.transform = 'scale(1)';
            }
        });
    });

    document.querySelectorAll('input[name="mood"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('.mood-card').forEach(card => {
                const input = card.previousElementSibling;
                if (input.checked) {
                    card.style.background = 'rgba(74, 144, 226, 0.15)';
                    card.style.borderColor = 'var(--bs-primary)';
                    card.style.transform = 'translateY(-8px) scale(1.05)';
                } else {
                    card.style.background = 'rgba(255,255,255,0.04)';
                    card.style.borderColor = 'rgba(255,255,255,0.1)';
                    card.style.transform = 'scale(1)';
                }
            });
        });
    });
});
</script>

<style>
.mood-card:hover {
    background: rgba(255,255,255,0.08) !important;
    border-color: rgba(255,255,255,0.3) !important;
}

.list-group-item:hover {
    background: rgba(255,255,255,0.1) !important;
    transform: translateX(10px);
    transition: all 0.3s ease;
}
</style>


