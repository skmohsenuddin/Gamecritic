<?php
$title = 'Login | GameCritic';
?>

<div class="container-fluid login-page">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-5">
            <div class="card login-card shadow-lg">
                <div class="card-body p-4">
                    <?php
                    $alertMessage = null;

                    if (isset($_GET['redirected'])) {
                        $reason = $_GET['reason'] ?? 'login_required';
                        switch ($reason) {
                            case 'comment':
                                $alertMessage = 'You must log in to write a review on games.';
                                break;
                            case 'rating':
                                $alertMessage = 'You must log in to rate games.';
                                break;
                            case 'vote':
                                $alertMessage = 'You must log in to upvote or downvote reviews.';
                                break;
                        }
                    }

                    $headerText = 'Welcome Back';
                    if (isset($_GET['action']) && $_GET['action'] === 'signup') {
                        $headerText = 'Create Account';
                    }

                    if ($alertMessage) {
                        echo "<div class='alert alert-danger text-center'>{$alertMessage}</div>";
                    } else {
                        echo "<h2 class='text-center mb-4'>{$headerText}</h2>";
                    }
                    ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            $error = $_GET['error'];
                            switch ($error) {
                                case 'missing_fields':
                                    echo 'Please fill in all fields.';
                                    break;
                                case 'invalid_credentials':
                                    echo 'Invalid email or password.';
                                    break;
                                default:
                                    echo 'An error occurred. Please try again.';
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success']) && $_GET['success'] === 'account_created'): ?>
                        <div class="alert alert-success">
                            Account created successfully! Please log in.
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo $baseUrl; ?>/login">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">Log In</button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="<?php echo $baseUrl; ?>/signup" class="text-decoration-none">Don't have an account? <strong>Sign up</strong></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>