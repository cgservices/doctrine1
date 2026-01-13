.PHONY: help build test test-smoke test-core test-tickets shell clean composer-install composer-update lint fix-tests fix-tests-dry fix-test-names fix-all test-mysql mysql-up mysql-down

# Default target
help:
	@echo "Doctrine1 Development Commands"
	@echo "==============================="
	@echo ""
	@echo "Available targets:"
	@echo "  make build              - Build the Docker image"
	@echo "  make test               - Run all tests (SQLite, quick)"
	@echo "  make test-mysql         - Run all tests with MySQL (recommended for full testing)"
	@echo "  make mysql-up           - Start MySQL container"
	@echo "  make mysql-down         - Stop MySQL container"
	@echo "  make test-smoke         - Run smoke tests only"
	@echo "  make test-core          - Run core tests only"
	@echo "  make test-tickets       - Run ticket tests only"
	@echo "  make test-filter F=     - Run tests matching a filter (e.g., make test-filter F=Connection)"
	@echo "  make shell              - Open a shell inside the Docker container (with MySQL)"
	@echo "  make composer-install   - Install Composer dependencies"
	@echo "  make composer-update    - Update Composer dependencies"
	@echo "  make lint               - Check PHP syntax errors"
	@echo "  make fix-tests          - Fix test files for PHP 8.4 compatibility"
	@echo "  make fix-tests-dry      - Dry run: show what would be fixed without making changes"
	@echo "  make fix-test-names     - Rename test classes to match file names (PHPUnit 11)"
	@echo "  make fix-all            - Run all test fixing scripts"
	@echo "  make clean              - Remove Docker image and vendor directory"
	@echo ""

# Docker image name
IMAGE_NAME=doctrine1-dev
CONTAINER_NAME=doctrine1-tests

# Build the Docker image
build:
	docker build -t $(IMAGE_NAME) .

# Run all tests
test: build
	docker run --rm --name $(CONTAINER_NAME) $(IMAGE_NAME) --testsuite "Core Tests"

# Run smoke tests only
test-smoke: build
	docker run --rm --name $(CONTAINER_NAME) $(IMAGE_NAME) --testsuite "Smoke Tests"

# Run core tests only
test-core: build
	docker run --rm --name $(CONTAINER_NAME) $(IMAGE_NAME) --testsuite "Core Tests"

# Run ticket tests only
test-tickets: build
	docker run --rm --name $(CONTAINER_NAME) $(IMAGE_NAME) --testsuite "Tickets"

# Run tests with a filter
test-filter: build
	docker run --rm --name $(CONTAINER_NAME) $(IMAGE_NAME) --filter $(F)

# Open a shell in the container
shell: build
	docker compose run --rm shell

# Start MySQL container
mysql-up:
	docker compose up -d mysql
	@echo "Waiting for MySQL to be ready..."
	@docker compose exec mysql mysqladmin ping -h localhost -u root -pdoctrine --wait=30 --silent || true
	@echo "MySQL is ready!"

# Stop MySQL and clean up
mysql-down:
	docker compose down -v

# Run tests with MySQL (recommended for full testing)
test-mysql: mysql-up build
	docker compose run --rm tests
	@echo "Tests completed with MySQL"

# Install dependencies via Composer
composer-install: build
	docker run --rm --entrypoint composer -v $(PWD):/app $(IMAGE_NAME) install

# Update dependencies via Composer
composer-update: build
	docker run --rm --entrypoint composer -v $(PWD):/app $(IMAGE_NAME) update

# Check PHP syntax
lint: build
	docker run --rm --entrypoint /bin/bash $(IMAGE_NAME) -c "find lib -name '*.php' -print0 | xargs -0 -n1 php -l 2>&1 | grep -v 'No syntax errors'"

# Clean up Docker image and vendor
clean:
	docker rmi $(IMAGE_NAME) 2>/dev/null || true
	rm -rf vendor composer.lock .phpunit.cache

# Fix test files for PHP 8.4 compatibility
fix-tests: build
	docker run --rm -v $(PWD):/app --entrypoint php $(IMAGE_NAME) tools/fix-tests-php84.php -v

# Dry run: show what would be fixed without making changes
fix-tests-dry: build
	docker run --rm -v $(PWD):/app --entrypoint php $(IMAGE_NAME) tools/fix-tests-php84.php --dry-run -v

# Rename test classes to match file names (required for PHPUnit 11)
fix-test-names: build
	docker run --rm -v $(PWD):/app --entrypoint php $(IMAGE_NAME) tools/fix-test-class-names.php -v

# Comprehensive test fixer for PHP 8.4 / PHPUnit 11
fix-tests-comprehensive: build
	docker run --rm -v $(PWD):/app --entrypoint php $(IMAGE_NAME) tools/fix-tests-comprehensive.php -v

# Fix dynamic property deprecation warnings
fix-dynamic-properties: build
	docker run --rm -v $(PWD):/app --entrypoint php $(IMAGE_NAME) tools/fix-dynamic-properties.php -v

# Full test fix: run all test fixing scripts
fix-all: fix-tests fix-test-names fix-tests-comprehensive fix-dynamic-properties
	@echo "All test fixes applied!"

# Show test summary (just the final numbers)
test-summary: build
	@docker run --rm $(IMAGE_NAME) --testsuite "Core Tests" 2>&1 | tail -5

# Run a specific test class
test-class: build
	@docker run --rm $(IMAGE_NAME) $(CLASS)

