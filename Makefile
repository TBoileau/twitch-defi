phpstan:
	php vendor/bin/phpstan analyse -c phpstan.neon src --no-progress

php-cs-fixer:
	php vendor/bin/php-cs-fixer fix

fix: php-cs-fixer


