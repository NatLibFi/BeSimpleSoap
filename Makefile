DOCKER_COMPOSE  = docker-compose
EXEC        	= $(DOCKER_COMPOSE) exec app
RUN        		= $(DOCKER_COMPOSE) run app
COMPOSER        = $(RUN) composer
QA        		= docker run -it --rm -v `pwd`:/project mykiwi/phaudit:7.2

##
## Project
## -------
##

kill:
	$(DOCKER_COMPOSE) kill
	$(DOCKER_COMPOSE) down --volumes --remove-orphans

start: up server-tests ## Start the project

up: ## Up the project
	$(DOCKER_COMPOSE) up -d --build --remove-orphans

stop: ## Stop the project
	$(DOCKER_COMPOSE) stop

composer-install: ## Execute composer instalation
	$(COMPOSER) install --prefer-dist

test: composer-install ## Run tests
	$(RUN) bin/simple-phpunit

server-tests: composer-install ## Run tests that need servers
	$(RUN) ./server-tests.sh

composer-update: ## Execute package update
	$(COMPOSER) update $(BUNDLE)

enter: ## enter docker container
	$(EXEC) bash

qa: ## Quality Assurance
	bin/phpcs --cache=tests/phpcs.cache.json --standard=tests/phpcs.xml -s
	bin/php-cs-fixer fix --config=tests/php-cs-fixer.php -vvv --dry-run
	bin/phpstan --configuration=tests/phpstan.neon --memory-limit=1G analyse

fix: ## Apply automatic fixes
	-bin/phpcs --cache=tests/phpcs.cache.json --standard=tests/phpcs.xml
	bin/phpcbf --cache=tests/phpcs.cache.json --standard=tests/phpcs.xml

php-cs-fixer: ## Apply php-cs-fixer fixes
	bin/php-cs-fixer fix --config=tests/php-cs-fixer.php -vvv

phpstan:
	bin/phpstan --configuration=tests/phpstan.neon --memory-limit=1G analyse

.PHONY: up start stop enter

.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
