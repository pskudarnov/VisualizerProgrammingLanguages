<?php
if ($_REQUEST["ajax"] == "Y" && $_REQUEST["clear"] == "Y") {
    include_once ($_SERVER['DOCUMENT_ROOT']."/include/event.php");
    $bd = new BDZ\Event();
    echo $bd->eventClearAll();
}