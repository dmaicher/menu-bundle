clear_cache:
	rm -rf tests/Functional/cache/*
	rm -rf tests/Functional/var/cache/*

test: clear_cache
	vendor/bin/phpunit -c tests tests
	make phpstan php_cs_fixer_check

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon -l 1 src/

php_cs_fixer_fix: clear_cache
	vendor/bin/php-cs-fixer fix --config .php_cs src tests

php_cs_fixer_check: clear_cache
	vendor/bin/php-cs-fixer fix --config .php_cs src tests --dry-run
