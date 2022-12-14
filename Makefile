META_FOLDER=.castor
MAIN=lib
COMPOSE_CMD ?= docker-compose
COMPOSE = $(COMPOSE_CMD) --project-directory .castor/docker

setup: prepare boot

# Prepares the project to be initialized
prepare: build dependencies

# Builds docker images needed for this project from scratch
build:
	$(COMPOSE) build --no-cache --pull

# Installs dependencies with composer
dependencies:
	$(COMPOSE) run --rm $(MAIN) composer install

autoload:
	$(COMPOSE) exec $(MAIN) composer dump-autoload

# Boots all the services in the docker-compose stack
boot:
	$(COMPOSE) up -d --remove-orphans

# Formats the code according to php-cs-fixer rules
fmt:
	$(COMPOSE) exec $(MAIN) vendor/bin/php-cs-fixer fix

# Run static analysis on the code
analyze:
	$(COMPOSE) exec $(MAIN) vendor/bin/psalm --stats --no-cache --show-info=true

# Runs the test suite
test:
	$(COMPOSE) exec $(MAIN) vendor/bin/phpunit --coverage-text

bench:
	$(COMPOSE) exec $(MAIN) vendor/bin/phpbench run --report=default

# Stops all services and destroys all the containers.
# NOTE: Named kill to convey the more accurate meaning that the containers are destroyed.
kill:
	$(COMPOSE) down

# Stops the services. Use this when you are done with development for a while.
stop:
	$(COMPOSE) stop

# Prepares a PR
pr: autoload fmt analyze test