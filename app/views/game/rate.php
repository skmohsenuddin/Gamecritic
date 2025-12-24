<div id="rating-warning" class="alert alert-warning" style="display:none;">
    Please select a rating for all categories.
</div>

<form id="ratingForm" method="POST" action="<?= rtrim($baseUrl,'/') ?>/game/<?= (int)$game['id'] ?>/submitRate">
    <?php if (isset($_SESSION['rating_error'])): ?>
        <div class="alert alert-warning">
            <?= $_SESSION['rating_error'] ?>
        </div>
        <?php unset($_SESSION['rating_error']); ?>
    <?php endif; ?>
  <div class="rating-group">
    <div class="rating-title-bar">
      Fun & Engagement
        <div class="rating-stars">
        <input type="radio" id="fun5" name="fun" value="5">
        <label for="fun5">★</label>
        <input type="radio" id="fun4" name="fun" value="4">
        <label for="fun4">★</label>
        <input type="radio" id="fun3" name="fun" value="3">
        <label for="fun3">★</label>
        <input type="radio" id="fun2" name="fun" value="2">
        <label for="fun2">★</label>
        <input type="radio" id="fun1" name="fun" value="1">
        <label for="fun1">★</label>
        </div>
    </div>
  </div>
  <div class="rating-group">
    <div class="rating-title-bar">
      Graphics & Art
        <div class="rating-stars">
        <input type="radio" id="graphics5" name="graphics" value="5">
        <label for="graphics5">★</label>
        <input type="radio" id="graphics4" name="graphics" value="4">
        <label for="graphics4">★</label>
        <input type="radio" id="graphics3" name="graphics" value="3">
        <label for="graphics3">★</label>
        <input type="radio" id="graphics2" name="graphics" value="2">
        <label for="graphics2">★</label>
        <input type="radio" id="graphics1" name="graphics" value="1">
        <label for="graphics1">★</label>
        </div>
    </div>
  </div>
  <div class="rating-group">
    <div class="rating-title-bar">
      Audio & Music
        <div class="rating-stars">
        <input type="radio" id="audio5" name="audio" value="5">
        <label for="audio5">★</label>
        <input type="radio" id="audio4" name="audio" value="4">
        <label for="audio4">★</label>
        <input type="radio" id="audio3" name="audio" value="3">
        <label for="audio3">★</label>
        <input type="radio" id="audio2" name="audio" value="2">
        <label for="audio2">★</label>
        <input type="radio" id="audio1" name="audio" value="1">
        <label for="audio1">★</label>
        </div>
    </div>
  </div>
  <div class="rating-group">
    <div class="rating-title-bar">
      Story & Narrative
        <div class="rating-stars">
        <input type="radio" id="story5" name="story" value="5">
        <label for="story5">★</label>
        <input type="radio" id="story4" name="story" value="4">
        <label for="story4">★</label>
        <input type="radio" id="story3" name="story" value="3">
        <label for="story3">★</label>
        <input type="radio" id="story2" name="story" value="2">
        <label for="story2">★</label>
        <input type="radio" id="story1" name="story" value="1">
        <label for="story1">★</label>
        </div>
    </div>
  </div>
  <div class="rating-group">
    <div class="rating-title-bar">
      User Interface & Experience
        <div class="rating-stars">
        <input type="radio" id="ux_ui5" name="ux_ui" value="5">
        <label for="ux_ui5">★</label>
        <input type="radio" id="ux_ui4" name="ux_ui" value="4">
        <label for="ux_ui4">★</label>
        <input type="radio" id="ux_ui3" name="ux_ui" value="3">
        <label for="ux_ui3">★</label>
        <input type="radio" id="ux_ui2" name="ux_ui" value="2">
        <label for="ux_ui2">★</label>
        <input type="radio" id="ux_ui1" name="ux_ui" value="1">
        <label for="ux_ui1">★</label>
        </div>
    </div>
  </div>
  <div class="rating-group">
    <div class="rating-title-bar">
      Technical Performance
        <div class="rating-stars">
        <input type="radio" id="technical5" name="technical" value="5">
        <label for="technical5">★</label>
        <input type="radio" id="technical4" name="technical" value="4">
        <label for="technical4">★</label>
        <input type="radio" id="technical3" name="technical" value="3">
        <label for="technical3">★</label>
        <input type="radio" id="technical2" name="technical" value="2">
        <label for="technical2">★</label>
        <input type="radio" id="technical1" name="technical" value="1">
        <label for="technical1">★</label>
        </div>
    </div>
  </div>
    <button type="submit" class="btn btn-success center-btn">
        Submit Rating
    </button>
</form>

<!-- 
<script>
    const warning = document.querySelector('.alert-warning');
    if (warning) {
        warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
</script> -->

<!-- <script>
document.getElementById('ratingForm').addEventListener('submit', function (e) {
    const groups = ['fun', 'graphics', 'audio', 'story', 'ux_ui', 'technical'];
    let missing = false;

    groups.forEach(name => {
        if (!document.querySelector(`input[name="${name}"]:checked`)) {
            missing = true;
        }
    });

    if (missing) {
        e.preventDefault(); // STOP form submit
        document.getElementById('rating-warning').style.display = 'block';
    }
});
</script> -->
<script>
document.getElementById('ratingForm').addEventListener('submit', function (e) {
    const groups = ['fun', 'graphics', 'audio', 'story', 'ux_ui', 'technical'];
    let missing = false;

    groups.forEach(name => {
        if (!document.querySelector(`input[name="${name}"]:checked`)) {
            missing = true;
        }
    });

    if (missing) {
        e.preventDefault(); // stop form submission

        const warning = document.getElementById('rating-warning');
        warning.style.display = 'block';   // make it visible
        warning.classList.add('show');     // trigger animation

        warning.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
