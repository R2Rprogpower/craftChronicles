SHELL := /bin/sh

.PHONY: install-hooks check fmt lint test unit-test feature-test up build telegram-poll restart-clear telegram-poll-restart schedule-work telegram-runtime telegram-runtime-restart telegram-stop

install-hooks:
	@mkdir -p .git/hooks
	@cp scripts/pre-commit .git/hooks/pre-commit
	@chmod +x .git/hooks/pre-commit
	@echo "Installed .git/hooks/pre-commit"

up:
	docker compose up -d

build:
	docker compose up -d --build

telegram-poll:
	@$(MAKE) telegram-stop
	@if [ -n "$(BOT_ID)" ]; then \
		docker compose exec -T app php artisan telegram:poll --sleep=1 --bot-id=$(BOT_ID); \
	else \
		docker compose exec -T app php artisan telegram:poll --sleep=1; \
	fi

telegram-poll-restart:
	@if [ -z "$(BOT_ID)" ]; then \
		echo "BOT_ID is required (example: make telegram-poll-restart BOT_ID=2)"; \
		exit 1; \
	fi
	@$(MAKE) telegram-stop
	docker compose exec -T app php artisan optimize:clear
	docker compose exec -T app php artisan telegram:poll --sleep=1 --bot-id=$(BOT_ID)

schedule-work:
	docker compose exec -T app sh -lc 'while true; do php artisan schedule:run; sleep 60; done # rebe-schedule-loop'

telegram-runtime:
	@if [ -z "$(BOT_ID)" ]; then \
		echo "BOT_ID is required (example: make telegram-runtime BOT_ID=2)"; \
		exit 1; \
	fi
	@$(MAKE) telegram-stop
	docker compose exec -T app php artisan optimize:clear
	@echo "Starting scheduler loop in background..."
	@docker compose exec -T app sh -lc 'while true; do php artisan schedule:run; sleep 60; done # rebe-schedule-loop' >/tmp/rebe_schedule_run.log 2>&1 &
	docker compose exec -T app php artisan telegram:poll --sleep=1 --bot-id=$(BOT_ID)

telegram-runtime-restart: telegram-runtime

telegram-stop:
	@docker compose exec -T app sh -lc "pkill -f 'php artisan telegram:poll' || true; pkill -f 'php artisan schedule:work' || true; pkill -f 'rebe-schedule-loop' || true; pkill -f 'while true; do php artisan schedule:run; sleep 60; done' || true; pkill -f 'php artisan schedule:run' || true" || true

restart-clear:
	docker compose exec -T app php artisan optimize:clear
	docker compose restart app

fmt:
	docker compose exec -T app ./vendor/bin/pint

lint:
	docker compose exec -T -u root app rm -rf /tmp/phpstan
	docker compose exec -T app ./vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G

unit-test:
	docker compose exec -T app php artisan test --testsuite=Unit

feature-test:
	docker compose exec -T app php artisan test --testsuite=Feature

test: unit-test feature-test

check: fmt lint test
	@echo "All checks passed."

.PHONY: ci-up ci-down ci-setup ci-check

ci-up:
	docker compose -f docker-compose.yml -f docker-compose.ci.yml up -d --build

ci-down:
	docker compose -f docker-compose.yml -f docker-compose.ci.yml down -v

ci-setup:
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app git config --global --add safe.directory /var/www/html
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app composer install --no-interaction --no-progress --prefer-dist
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app npm ci --no-audit --no-fund
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app npm run build
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app cp .env.example .env
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app php artisan key:generate
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app php artisan migrate --force

ci-check:
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app ./vendor/bin/pint
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app rm -rf /tmp/phpstan
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app ./vendor/bin/phpstan analyse -c phpstan.neon --memory-limit=1G
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app php artisan test --testsuite=Unit
	docker compose -f docker-compose.yml -f docker-compose.ci.yml exec -T -u root app php artisan test --testsuite=Feature
	@echo "All CI checks passed."
