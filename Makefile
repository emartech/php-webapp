DOCKER = docker
DOCKER_COMPOSE = docker-compose
CONTAINER = php-webapp

ifndef TESTMETHOD
FILTERARGS=
else
FILTERARGS=--filter $(TESTMETHOD)
endif

.PHONY: all test destroy update

help:
	@echo "Targets:"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/\(.*\):.*##[ \t]*/	\1 ## /' | column -t -s '##'
	@echo

all: destroy packages ## Build all

destroy: ## Destroy containers
	$(DOCKER_COMPOSE) rm -f

packages: ## Install packages
	$(DOCKER_COMPOSE) run $(CONTAINER) composer install

pu: packages-update
packages-update: ## Update packages
	$(DOCKER_COMPOSE) run $(CONTAINER) composer update

test: phpunit ## Run all test

phpunit: ## Test with phpunit
	$(DOCKER_COMPOSE) run $(CONTAINER) vendor/bin/phpunit -c test/phpunit.xml $(FILTERARGS) $(TESTFILE)
