<?php

require_once("constants.php");
require_once("classes/main_trie.class.php");

$pat = new PatriciaTrieC();

$pat->Insert ("FISCH");
$pat->Insert ("ANTEE");
$pat->Insert ("TEARE");
$pat->Insert ("Mario Circuit");

$pat->Insert ("Mario Circuit");
$pat->Insert ("Mario Circuit Builder");
$pat->Insert ("Marioc");

//echo "<pre>";
//$pat->varDump();
//echo "</pre><br>";

echo "</br>";
$pat->Search("FISCH");
echo "</br>";
$pat->Search("Mario");
echo "</br>";
$pat->Search("Mario Circuit");
//~ $pat->remove("FISCH");
//~ $pat->Search("FISCH");



?>