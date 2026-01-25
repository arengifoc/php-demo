# Makefile for PHP Slim Demo

.PHONY: help install start stop restart logs test phpstan cs-check cs-fix ci clean

help: ## Show this help message
	@echo 'Usage: make [target]'
	@echo ''
	@echo 'Available targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  %-15s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

install: ## Install composer dependencies
	docker-compose exec php composer install

start: ## Start Docker containers
	docker-compose up -d

stop: ## Stop Docker containers
	docker-compose down

restart: stop start ## Restart Docker containers

logs: ## Show container logs
	docker-compose logs -f

test: ## Run PHPUnit tests
	docker-compose exec php composer test

phpstan: ## Run PHPStan static analysis
	docker-compose exec php composer phpstan

cs-check: ## Check code style
	docker-compose exec php composer cs:check

cs-fix: ## Fix code style issues
	docker-compose exec php composer cs:fix

ci: ## Run all CI checks
	docker-compose exec php composer ci

clean: ## Clean up generated files
	rm -rf vendor/ coverage/ .phpunit.cache/ .phpstan/
	docker-compose down -v

build: ## Build Docker images
	docker-compose build --no-cache
