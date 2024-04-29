<?php
if (!isset($show)) {
    error_log("Using Show partial without data");
    flash("Dev Alert: Show called without data", "danger");
}
?>
<?php if (isset($show)) : ?> 
    <div class="card mx-auto" style="width: 18rem;">
       
        <div class="card-body">
            <h5 class="card-title"><?php se($show, "title", "Unknown"); ?></h5>
            <div class="card-text">
                <ul class="list-group">
                 
                    <li class="list-group-item">Rating: <?php se($show, "imdb_rating", "N/A"); ?></li>
                    <li class="list-group-item">Audience: <?php se($show, "rated", "N/A"); ?></li>
                    <li class="list-group-item">Genre(s): <?php se($show, "genres", "N/A"); ?></li>
                </ul>

            </div>

                <div class="card-body">
                    <a href="<?php echo get_url('single_view_show.php?id=' . $show["id"]); ?>" class="btn btn-info">View</a>
                    <a href="<?php echo get_url('edit_show_content.php?id=' . $show["id"]); ?>" class="btn btn-secondary">Edit</a>
                    <a href="<?php echo get_url('delete_show_from_user.php?id=' . $show["id"]); ?>" class="btn btn-danger">Delete</a>
                </div>
           
                
           
        </div>
    </div>
<?php endif; ?>

           
