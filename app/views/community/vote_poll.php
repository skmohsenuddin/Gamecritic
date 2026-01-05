<?php
$title = 'Vote on Poll | GameCritic';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2><i class="fas fa-vote-yea me-2"></i>Community Vote</h2>
                        <p class="text-muted">Your opinion matters! Cast your vote below.</p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <div class="bg-secondary rounded p-4" style="background: #1a1a2e !important;">
                        <h3 class="mb-4" style="font-size: 1.2rem;"><?php echo htmlspecialchars($poll['question']); ?></h3>

                        <form action="<?php echo $baseUrl; ?>/poll/<?php echo (int)$poll['id']; ?>/vote" method="post">
                            <div class="d-flex flex-column gap-2">
                                <?php foreach ($poll['options'] as $option): ?>
                                    <label class="form-check-label p-3 rounded border border-secondary" 
                                           style="cursor: pointer; background: #252545; transition: background 0.2s;"
                                           onmouseover="this.style.background='#2a2a55'" 
                                           onmouseout="this.style.background='#252545'">
                                        <input type="radio" name="option" value="<?php echo htmlspecialchars($option); ?>" 
                                               required class="me-3" style="transform: scale(1.2);">
                                        <span><?php echo htmlspecialchars($option); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-4">Submit Vote</button>
                        </form>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?php echo $baseUrl; ?>/polls" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Back to Polls
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


