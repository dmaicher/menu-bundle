clear_cache:
	rm -rf tests/Functional/cache/*

test: clear_cache
	vendor/bin/phpunit -c tests tests
	make phpstan

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon -l 1 src/
