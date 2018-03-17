clear_cache:
	rm -rf tests/Functional/cache/*

test: clear_cache
	vendor/bin/phpunit -c tests tests
	make phpstan

phpstan:
	vendor/bin/phpstan analyse -a vendor/autoload.php -l 1 src/
