<?php

///////////////////////////////////////////////////////////////////////////////////////////
//PURPOSE::: every node in the tree is a collection of either left and right links
//:::::::::: or the payload and key. the key has to be stored to check that the text is
//:::::::::: correct, if you want a mud deal where you could type the letter t for 
//:::::::::: tell, then you do not need to store the key and could take the full check out
//:::::::::: of the code
//:::::::::: 
//:::::::::: 
//NOTES::::: a node will either store links to nodes or the payload
//:::::::::: 
//:::::::::: 
///////////////////////////////////////////////////////////////////////////////////////////
class PatriciaNodeC 
{
	var $payload;
	var $key;
		//remember if we are a leaf or not
	var $is_leaf;
		//we are either internal or a leaf (we can overlap the data to save space)
	var $left;
	var $right;
			
		//if payload is given, then create a leaf
	public function __construct ($_payload=null, $_key=null) { 
		
		if  ($_payload == null && $_key == null) {
			$this->left = EMPTY_NODE; 
			$this->right = EMPTY_NODE; 
			$this->is_leaf = false; 
		} else {
			$this->payload = $_payload; 
			$this->key = $_key; 
			$this->is_leaf = true; 
		}
	}
	
	public function __unset($name) {
		echo "$name";
	}
}

class PatriciaTrieCSub 
{
	
	function kart($pos, $k, $klen, $CHAR_BIT) {
		if ($pos == $klen ) {
			$CHAR_BIT <<= 1;
			return $CHAR_BIT;
		}		
		if ($pos < $klen) {
			$pad = PAD;
			$pad |= decbin($CHAR_BIT);
			return $pad;
		}
		return $CHAR_BIT;
	}
	
	function BuildKey ( $_txt) {
		
		$key = 0;									//key being built up
		$bit_count = MAX_KEY_BITS;					//how many bits have been filled
		$txt_count = 0;								//which character are we on
		$shift_bits = 0;							//how many bits to shift to make space
		$huff_code = 0;								//the current huffman code to add on

		//for security we copy text string into our own buffer
		$txt[MAX_STRING_FOR_KEY] = "\0";

		//copy everything up to the end of string or end of buffer
		//we ensure that there will be one end of string character at the end
		//so we can just check for that instead of the txt_count guy... easier
		//
		//also, no buffer overrun crap will be allowed
		for ($i = 0; $end1=(MAX_STRING_FOR_KEY-1), $end2=strlen($_txt), $i<$end1 && $i<$end2; $i++) {
			$txt [$i] = substr($_txt,$i,1);
		}
		$txt [$i]  = END_OF_STRING;
		$end=$end2-1;
		reset($txt);
		//~ print_r($txt);
		
		//convert text to integer
		do
		{	
			//if we get to the end of the text string before the key is filled that is okay
			//we will just use what we have!
			if ($txt [$txt_count] == END_OF_STRING || $txt_count > $end)
				break;
			
			$shift_bits = 0;
			$backup = $huff_code = bindec($this->kart($txt_count,  $txt, $end, ord($txt [$txt_count])));
			//~ echo $backup."\n";
			
			//calculate how many bits to shift on to key
			do
			{
				$huff_code >>= 1;				//shift bits off until we get to zero
				//~ echo "h".$huff_code."|";
				++$shift_bits;				        //number of bits we need to shift before the add
				--$bit_count;						//track how many bits have been shifted so far
			} 
			//quit when we have counted how many bits are used to represent number
			while ($huff_code > 0);

			//if we do not have enough bits in the key, then quit with what we have
			if ($bit_count < 0)
				break;

			//~ echo "-D:".$shift_bits;
			//make space to add the next character on (shift zero to add to)
			$key <<= $shift_bits;
			//~ echo "k:".decbin($key)."\n";
			//add the next character on
			$key |= decbin($backup);
			//~ echo decbin($key)."\n";
			//~ echo "-A:".$backup."-B:".$key."-C:".$shift_bits;
			
			//we added one letter
			++$txt_count;
		}
		//we will probably quit with the break, but just to make sure we have this
		//to kill the loop, ...when we have added as many bits as possible
		//
		//notice that if bit_count is 0 it will not break, but we should not continue
		//so this little check saves us some stupidity
		while ($bit_count > 0);
		//~ echo  $key."\n";
			//return the formed key
		return $key;
	}

	//_n - the current node to insert at
	//_payload - the class to associate with the string of text
	//_key - the integer key to store (or text)
	//_bp - the position of the bit in the key to check against
	
	function insertSub (&$_n, $_payload, $_key, $_bp) {
		
		//empty pointer from an internal node
		if (!is_object($_n))
		{
			//auto leaf
			$_n = new PatriciaNodeC ($_payload, $_key);
			return $_n;
		}                                                                                        
		
		//if no more bits to discern, then you cannot insert
		else if ($_bp > MAX_KEY_BITS)
		{
			//was already inserted or we do not have the appropriate number of bits to differentiate from
			//what ever was stored (obviously there is something else down there that took the max bits
			//to store and we need more bits to tell them apart!)
			return EMPTY_NODE;
		}

		//we hit a leaf and need to store the leaf and the new node too
		else if ($_n->is_leaf)
		{
			//if you tried to insert duplicates, then do not insert anything else
			//
			//without this duplicates with create an immediate depth of the number of bits in our key
			//because they will be the same until _bp is greater than MAX_KEY_BITS (above) so even though
			//it is a whole key comparison, it will save on the average case (a lot)
			//
			if ($_n->key == $_key)
				return EMPTY_NODE;
			
			 $_original = $_n;

			//split leaf to internal
			$_n = new PatriciaNodeC ();

			//save what was there in the appropriate child
			if ($this->IsBitOn ($_original->key, $_bp))
				$_n->right = $_original;
			else
				$_n->left = $_original;

			//we placed the leaf in an appropriate position and will
			//now continue with our new internal node.
		}

		//try left (if last bit on key is not zero)
		if ($this->IsBitOn ($_key, $_bp))
			return $this->insertSub ($_n->right, $_payload, $_key, ++$_bp);
		
		//if last bit on key is zero then go right
		else
			return $this->insertSub ($_n->left, $_payload, $_key, ++$_bp);
	}

	//////////////////////////////////////////////////////////////////////////
	function searchSub (&$_n, $_key, $_bp)
	{
		//if tree is empty
		if (!is_object($_n)) 
			return EMPTY_NODE;

		//if we found a leaf, then this is either it or it does not exist
		else if ($_n->is_leaf)
		{
			//check if we found it or not
			if ($_n->key == $_key) {
				//found it
				return $_n->payload->fetch();

			} else {
				//does not exist
				return  $_n->payload->error();
			}
		}

		//try left (if last bit on key is one)
		else if ($this->IsBitOn ($_key, $_bp))
			return $this->searchSub ($_n->right, $_key, ++$_bp);

		//if last bit on key is zero then go right
		else
			return $this->searchSub ($_n->left, $_key, ++$_bp);
	}

	//////////////////////////////////////////////////////////////////////////
	function removeSub (&$_n, $_key, $_bp) {
		//to remember payload so we can also tail recurse through
		//the list and remove any internal nodes that are not used
		//in the tree
		$tmp = EMPTY_NODE;

		//if tree is empty
		if (!is_object($_n)) 
			return EMPTY_NODE;

		else if ($_n->is_leaf)
		{
			$send_payload_back = $_n->payload;

			//because in this case we do not want the payload deleted
			//do the node class will not wax it, we are passing it back
			$_n->payload = EMPTY_NODE;
			//~ unset ($_n);
			$_n = EMPTY_NODE;
			return $send_payload_back;
		}

		//try left (if last bit on key is one)
		else if ($this->IsBitOn ($_key, $_bp))
			//save so we can tail recurse
			$tmp = $this->removeSub ($_n->right, $_key, ++$_bp);

		//if last bit on key is zero then go right
		else
			//save so we can tail recurse
			$tmp = $this->removeSub ($_n->left, $_key, ++$_bp);

		//clean up any unused internal nodes on your way up
		////////////////////////////////////////////////////////////////
		if (!$_n->is_leaf && !$_n->left && !$_n->right)
		{
			unset  ($_n);
			$_n = EMPTY_NODE;
		}
		////////////////////////////////////////////////////////////////

		//return the payload
		return $tmp;
	} 
	
	//////////////////////////////////////////////////////////////////////////
	function clearSub(&$kill_me) {
		//quit if NULL
		if (!$kill_me) {
			return;
		}
		//go down every branch
		else  {
			if (!$kill_me->is_leaf)
			{
				if ($kill_me->left)
					$this->clear ($kill_me->left);
				
				if ($kill_me->right)
					$this->clear ($kill_me->right);
			}
			
			//~ unset($kill_me);
			$kill_me = EMPTY_NODE;
		}
		//we assume anything they wanted they got
		//we cannot assume we are allowed to delete the payload, could be
		//static..!
	}
}



///////////////////////////////////////////////////////////////////////////////////////////
//PURPOSE::: The data that is associated with a key, you can store anything in this 
//:::::::::: class that you want to link to your search key
//:::::::::: 
//:::::::::: 
//NOTES::::: Use polymorphism and make your own class or just edit this one....
//:::::::::: 
//:::::::::: 
///////////////////////////////////////////////////////////////////////////////////////////

class PayloadC extends PatriciaTrieCSub
{
	var $payload;
	
	public function __construct($string) {
		$this->payload = $string;
	}
	
	public function fetch() {
		echo $this->payload;
	}
	
	public function error() {
		echo "Not Found!";
	}
};

?>