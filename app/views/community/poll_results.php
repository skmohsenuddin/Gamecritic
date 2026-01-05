<?php
$title = 'Poll Results | GameCritic';
$results = $poll['result'] ?? [];
$totalVotes = array_sum($results);
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2><i class="fas fa-chart-bar me-2"></i>Poll Results</h2>
                        <p class="text-muted">The community has spoken. Here are the latest standings.</p>
                    </div>

                    <div class="bg-secondary rounded p-4" style="background: #1a1a2e !important;">
                        <h3 class="mb-4 text-primary" style="font-size: 1.2rem;">
                            <?php echo htmlspecialchars($poll['question']); ?>
                        </h3>

                        <div class="results-container">
                            <?php foreach ($poll['options'] as $option): ?>
                                <?php 
                                $count = isset($results[$option]) ? (int)$results[$option] : 0;
                                $percentage = $totalVotes > 0 ? ($count / $totalVotes * 100) : 0;
                                ?>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2" style="font-size: 0.95rem;">
                                        <span><?php echo htmlspecialchars($option); ?></span>
                                        <span class="text-muted">
                                            <?php echo $count; ?> votes (<?php echo number_format($percentage, 1); ?>%)
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 10px; background: #252545;">
                                        <div class="progress-bar bg-primary" 
                                             role="progressbar" 
                                             style="width: <?php echo $percentage; ?>%;"
                                             aria-valuenow="<?php echo $percentage; ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="text-center mt-4 pt-3 border-top border-secondary">
                            <small class="text-muted">Total Community Votes: <?php echo $totalVotes; ?></small>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?php echo $baseUrl; ?>/polls" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Polls
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


