<?php
function createHeadSection($title = 'mRSS', $description = 'mRSS is the best RSS reader ever. You know why? Because it\'s made in PHP.') {
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<link rel="stylesheet" href="/assets/css/style.css?rand=' . mt_rand(1, 2147483647) . '">';
    echo '<title>' . $title  . '</title>';
    echo '<meta name="description" content="' . $description  .'">';
    echo '<script src="https://kit.fontawesome.com/f0874d0cd9.js" crossorigin="anonymous"></script>';

}