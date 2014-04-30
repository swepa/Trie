<?php
define("NUMBER_OF_LETTERS_IN_ALPHABET","26");				//number of letters in the alphabet
define("MAX_KEY_BITS","128");								//if you change the key type you need to change this to 
															//the number of bits in that type or if you use a machine 
															//that is not 32 bit
define("EMPTY_NODE","0");									//like null
define("START_BIT_COUNT","0");								//starts the key splitting level (check the zero bit for 1 or 0 first)
define("END_OF_STRING","\0");
define("PAD", decbin(128));

//you could have an instruction of the letter that is represented by 1, so the max number of bits
//the plus one is for the end of string, just in case we really do have all e's		
define("MAX_STRING_FOR_KEY", MAX_KEY_BITS+1);

?>