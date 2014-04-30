<?php
require_once("sub_trie.class.php");
///////////////////////////////////////////////////////////////////////////////////////////

// PURPOSE::: To store strings of text and associate that text with a container class. 
//:::::::::: 
//:::::::::: 
//NOTES::::: Odd numbers are on the right
//:::::::::: Even on the left
//:::::::::: and so on... (2, 4, 8) multiples of 2 break it down
///////////////////////////////////////////////////////////////////////////////////////////

class PatriciaTrieC extends PayloadC
{
	//the root node of the PAT trie
	var $head;
	
	function IsBitOn ($_key, $_bp) { 
		//~ echo "k:".decbin($_key).">>".$_bp."\n";
		return (($_key >>= $_bp) & 1); 
	}
	
	public function __construct()	{ 
		$this->head = new PatriciaNodeC();
	}
	
	public function Insert ($_key) {
		return ($this->insertSub ($this->head, new payloadC($_key), $this->BuildKey ($_key), START_BIT_COUNT) != EMPTY_NODE);
	}
	
	public function Search ($_key) {
		return $this->searchSub ($this->head, $this->BuildKey ($_key), START_BIT_COUNT); 
	}
	
	public function Remove ($_key){	
		return $this->removeSub($this->head, $this->BuildKey ($_key), START_BIT_COUNT); 
	}
	
	public function Clear (){ 
		$this->clear ($head); $this->head = EMPTY_NODE; 
	}
	
	public function varDump () {
		//var_dump($this);
		print_r($this);
	}
}

?>