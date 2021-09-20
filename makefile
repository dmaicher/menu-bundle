clear_cache:
	rm -rf tests/Functional/cache/*
	rm -rf tests/Functional/var/cache/*

test: clear_cache
	vendor/bin/phpunit -c tests tests
	make phpstan

phpstan:
	vendor/bin/phpstan analyse -c phpstan.neon -l max src/

php_cs_fixer_fix: php-cs-fixer.phar
	./php-cs-fixer.phar fix --config .php-cs-fixer.php src tests

php_cs_fixer_check: php-cs-fixer.phar
	./php-cs-fixer.phar fix --config .php-cs-fixer.php src tests --dry-run --diff

php-cs-fixer.phar:
	wget https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v3.1.0/php-cs-fixer.phar && chmod 777 php-cs-fixer.phar
