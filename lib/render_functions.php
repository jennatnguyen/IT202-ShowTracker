<?php

function render_input($data = array())
{
    include(__dir__ . "/../partials/input_field.php");
}

function render_button($data = array())
{
    include(__DIR__ . "/../partials/button.php");
}

function render_table($data = array())
{
    include(__DIR__ . "/../partials/table.php");
}

function render_show_card($show = array())
{
    include(__DIR__ . "/../partials/show_card.php");
}

function render_single_show_card($show = array())
{
    include(__DIR__ . "/../partials/single_show_card.php");
}

function render_list_show_card($show = array())
{
    include(__DIR__ . "/../partials/show_card_list.php");
}

function render_result_counts($result_count, $total_count)
{
    include(__DIR__ . "/../partials/result_counts.php");
}

function render_all_users_show_card($show = array())
{
    include(__DIR__ . "/../partials/all_users_card.php");
}
