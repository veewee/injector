[![Build Status](https://travis-ci.org/veewee/injector.svg?branch=master)](https://travis-ci.org/veewee/injector)

# injector
Copy / paste code at specific locations in a list of files


## Installation

**WIP**: You can use the executable inside this repo for now ....

```
$ 
```


## Usage
```
Usage:
  copy-paste [options] [--] <src> <location>

Arguments:
  src                   The files you want to copy / paste in
  location              The location where you want to copy / paste

Options:
      --dry-run         Dont change the code but print the results to the screen
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

## Examples

```sh
echo " implements MyInterface" | ./bin/injector 'src/Testing' 'T_CLASS { <' --dry-run
echo "\nuse New\Imported\ClassName;" | ./bin/injector 'src/Testing' 'NEWUSE(New\\Imported\\Class)' --dry-run
echo "return 'Do something';\n        " | ./bin/injector 'src/Testing' 'METHODNAME(myFunction) { > >' --dry-run

```

## Location operations

Every operation is split based on a space.
For example.: `T_CLASS { <` contains out 3 tokens:

- `T_CLASS`: Will search for the next occurrence of the `class` keyword
- `{`: Will search for the next occurrence of the brace with type `{`
- `<`: Will select the previous token

These tokens will be detected in this orde.

### Available location operations

**Token lookups**
- `T_*`: Detect next token of [PHP parser token constant](http://php.net/manual/en/tokens.php). (Or the CT class in php-cs-fixer)

**Brace lookups**
- `{}[]()`: Detect next token of the selected brace type
- `ENDBLOCK(]}))`: Detect the end block of the selected brace type.
- `STARTBLOCK([{()`: Detect the start block of the selected brace type.

**Next / Previous lookups**
- `<`: Select previous token
- `>`: Select next token
- `<<`: Select Previous non-whitespace token
- `>>`: Select Next non-whitespace token
- `<<<`: Select Previous meaningful token (no whitespace, comment, docblocks, ...)
- `>>>`: Select Next meaningful token (no whitespace, comment, docblocks, ...)

**Macro lookups**
- `METHODNAME(myFunction)`: Detect the function declaration of method `myFunction`
- `NEWUSE(MyNew\MyNamespace)`: Detect the best place to insert a new use statement `MyNew\MyNamespace`
