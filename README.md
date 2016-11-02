# packet-lite
[![Build Status](https://travis-ci.org/peacefulbit/packet-lite.svg?branch=master)](https://travis-ci.org/peacefulbit/packet-lite)
[![Coverage Status](https://coveralls.io/repos/github/peacefulbit/packet-lite/badge.svg?branch=master)](https://coveralls.io/github/peacefulbit/packet-lite?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/peacefulbit/packet-lite/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/peacefulbit/packet-lite/?branch=master)

My implementation of lisp-like language interpreter written on PHP.

## Installation
	$ composer global require peaceful-bit/slate-lang

## Usage
	$ slate program.st
	$ cat program.st | slate -s

## Code example
```
; Define constant
(def pi 3.14)

; Define function
(def (square-of-circle r) (* pi (pow r 2)))

; Call function
(print (square-of-circle 15))
```

