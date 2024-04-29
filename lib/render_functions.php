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
    include(__DIR__ . "/../partials/show_card.php");
}
