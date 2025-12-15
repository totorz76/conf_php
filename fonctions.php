<?php 
function dg($data){
    echo '<pre style="background-color: #000; color: #fff; padding: 10px">';
    var_dump($data);
    echo '</pre>';
};

function dd($data){
    echo '<pre style="background-color: #000; color: #fff; padding: 10px">';
    var_dump($data);
    echo '</pre>';
    die();
};
?>