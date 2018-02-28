BOX := $(shell command -v box 2> /dev/null)

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@echo "  phar            Build the PHAR"

phar:
ifndef BOX
	$(error "The box command is not available. Please install: kherge/box")
endif
	rm -rf ./vendor
	composer install --no-dev
	php -d phar.readonly=Off $(BOX) build --ansi --verbose
