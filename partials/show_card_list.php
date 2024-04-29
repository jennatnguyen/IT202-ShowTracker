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
                    
                </ul>

            </div>

            <?php if (!isset($show["user_id"]) || $show["user_id"] === "N/A") : ?>
                <div class="card-body">
                    <a href="<?php echo get_url('api/add_show.php?show_id=' . $show["id"]); ?>" class="btn btn-success">Add to Watchlist</a>
                </div>
            <?php else : ?>
                <div class="card-body">
                    <div class="bg-warning text-dark text-center">Show Already Added</div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

           
