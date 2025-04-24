# === Build & Run ===

build:
	docker compose build

run:
	docker compose run --rm app bin/console commission:calculate input.txt

run-file:
	docker compose run --rm app bin/console commission:calculate $(file)

# === Testing ===

test:
	docker compose run --rm app vendor/bin/phpunit

test-debug:
	docker compose run --rm app vendor/bin/phpunit --display-deprecations --debug

# === Dev Tools ===

sh:
	docker compose run --rm app sh

migrate-phpunit:
	docker compose run --rm app vendor/bin/phpunit --migrate-configuration

# === Cleaning ===

clean:
	docker compose down -v

rebuild: clean build
