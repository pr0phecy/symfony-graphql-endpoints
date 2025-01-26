DOCKER_CONTAINER_NAME=afs-client-portal-test-php-1

.PHONY: help
help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: build
build: ## Build the development containers with the target
	docker build --target frankenphp_dev .

.PHONY: migrate
migrate: ## Run database migrations, not per se needed because of the frankenphp setup
	docker compose up -d
	docker exec -it ${DOCKER_CONTAINER_NAME} bin/console doctrine:migrations:migrate --no-interaction

.PHONY: fixtures
fixtures: ## Run database fixtures
	docker compose up -d
	docker exec -it ${DOCKER_CONTAINER_NAME} bin/console doctrine:fixtures:load

.PHONY: setup
setup: ## Set up the application
	docker exec -it ${DOCKER_CONTAINER_NAME} composer install
	docker exec -it ${DOCKER_CONTAINER_NAME} bin/console doctrine:migrations:migrate

.PHONY: shell
shell: ## Enter the PHP container shell
	docker compose up -d
	docker exec -it ${DOCKER_CONTAINER_NAME} bash

.PHONY: up
up: ## Start the containers in detached mode
	docker compose up -d

.PHONY: down
down: ## Stop and remove the Docker containers
	docker compose down

.PHONY: clean
clean: ## Remove all containers, volumes, and networks
	docker compose down -v

.PHONY: test
test: test-unit test-integration ## Run all tests

.PHONY: test-unit
test-unit: ## Run the unit tests
	docker exec -it ${DOCKER_CONTAINER_NAME} vendor/bin/phpunit --testsuite=unit

.PHONY: test-integration
test-integration: ## Run the integration tests
	docker exec -e APP_ENV=test -it ${DOCKER_CONTAINER_NAME} php bin/console doctrine:database:create --env=test
	docker exec -e APP_ENV=test -it ${DOCKER_CONTAINER_NAME} php bin/console doctrine:schema:create --env=test
	docker exec -e APP_ENV=test -it ${DOCKER_CONTAINER_NAME} vendor/bin/phpunit --testsuite=integration
	docker exec -e APP_ENV=test -it ${DOCKER_CONTAINER_NAME} php bin/console doctrine:database:drop --force --env=test

.PHONY: code-style
code-style: ## Fix code style and check when done
	docker exec -it ${DOCKER_CONTAINER_NAME} vendor/bin/phpcbf || true
	docker exec -it ${DOCKER_CONTAINER_NAME} vendor/bin/phpcs

.PHONY: phpstan
phpstan: ## Run phpstan globally
	phpstan analyse --memory-limit=-1
