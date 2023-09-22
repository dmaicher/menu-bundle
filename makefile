clear_cache:
	rm -rf tests/Functional/cache/*
	rm -rf tests/Functional/var/cache/*

test: clear_cache
	vendor/bin/phpunit -c tests tests

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon -l max src/

php_cs_fixer_fix:
	vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php src tests

php_cs_fixer_check:
	vendor/bin/php-cs-fixer fix --config .php-cs-fixer.php src tests --dry-run --diff
