Trie
====

Trie | (Insert, Manage and Search assosiative array with kart tree)


This package can be used to manage and search associative arrays using a Kart tree (key alteration radix tree).

It can insert text strings that act as keys in a tree structure. The package can also search for the presence of such keys in the tree.


Trie is an efficient information retrieval data structure. Using trie, search complexities can be brought to optimal limit 
(key length). If we store keys in binary search tree, a well balanced BST will need time proportional to M * log N, 
where M is maximum string length and N is number of keys in tree. Using trie, we can search the key in O(M) time. 
However the penalty is on trie storage requirements.

Every node of trie consists of multiple branches. Each branch represents a possible character of keys. We need to 
mark the last node of every key as leaf node. A trie node field value will be used to distinguish the node as 
leaf node (there are other uses of the value field).

The following tree explains construction of trie using keys given in the example below,

                      root
                    /   \    \
                    t   a     b
                    |   |     |
                    h   n     y
                    |   |  \  |
                    e   s  y  e
                 /  |   |
                 i  r   w
                 |  |   |
                 r  e   e
                        |
                        r
                        

Read more about Trie here (http://www.geeksforgeeks.org/trie-insert-and-search/)

Note: This is open source solution for searching in assosiative array. Please feel free to alter this solutoin according to your
requirements.
