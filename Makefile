# set all to phony tail
.PHONY: *

all: install cs-fix cs-check static-analysis test ## Runs everything

test: test-unit test-mutation ## Runs all test suite

test-unit: ## Runs unit tests
	vendor/bin/phpunit --testsuite unit $(FLAGS)

test-integration: ## Runs integration tests
	vendor/bin/phpunit --testsuite integration $(FLAGS)

test-mutation: ## Runs mutation tests, maximum strictness
	XDEBUG_MODE=coverage vendor/bin/infection  --test-framework-options='--testsuite=unit' -s --threads=4 --min-msi=100 --min-covered-msi=100 $(FLAGS)

cs-fix: ## Runs phpcbf
	vendor/bin/phpcbf

cs-check: ## Runs phpcs
	vendor/bin/phpcs

static-analysis: ## Runs static analysis with phpstan
	vendor/bin/phpstan analyse

composer-require-checker: ## Checks if all root dependencies are declared
	vendor/bin/composer-require-checker check

install-ci: ## Install dependencies with composer with flags -a -n
	composer install $(CI_COMPOSER_FLAGS)

install: ## Install dependencies with composer
	composer install $(FLAGS)

help:
	@echo "\033[33mUsage:\033[0m\n  make [target] [FLAGS=\"val\"...]\n\n\033[33mTargets:\033[0m"
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[32m%-18s\033[0m %s\n", $$1, $$2}'
