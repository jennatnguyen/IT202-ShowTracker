<?php
if (!isset($show)) {
    error_log("Using Show partial without data");
    flash("Dev Alert: Show called without data", "danger");
}
?>
<?php if (isset($show)) : ?>
    <div class="card mx-auto" style="width: 70rem;">
       
        <div class="card-body">
            <h5 class="card-title"><?php se($show, "title", "Unknown"); ?></h5>
            <div class="card-text">
                <ul class="list-group">
                     <li class="list-group-item">Release Date: <?php se($show, "release_date", "Unknown"); ?></li>
                    <li class="list-group-item">Rating: <?php se($show, "imdb_rating", "Unknown"); ?></li>
                    <li class="list-group-item">Audience: <?php se($show, "rated", "Unknown"); ?></li>
                    <li class="list-group-item">Genre(s): <?php se($show, "genres", "Unknown"); ?></li>
                    <li class="list-group-item">Description: <?php se($show, "description", "Unknown"); ?></li>
                </ul>

            </div>

           
        </div>
    </div>
<?php endif; ?> 