# lisp-machine
[![Build Status](https://travis-ci.org/peacefulbit/lisp-machine.svg?branch=master)](https://travis-ci.org/peacefulbit/lisp-machine)
[![Coverage Status](https://coveralls.io/repos/github/peacefulbit/lisp-machine/badge.svg?branch=master)](https://coveralls.io/github/peacefulbit/lisp-machine?branch=master)

My own light lisp-interpreter written on PHP.

```
; Define constant
(def pi 3.14)

; Define function
(def (square-of-circle r) (* pi (^ r 2)))

; Make list
(list 1 2 3 4 5 6 7 8 9 10)

; Call function
(print (square-of-circle 15))
```

## checklist
1. ~~Parse code into list of lexemes~~
2. ~~Make ast tree from list of lexemes~~
3. ~~Make interpreter that will run our ast~~
4. ~~Must support modules~~
5. ~~Must support functions and variables definition~~
6. Implement using visitors and finite state machine 
7. Import PHP functions from pocket code
