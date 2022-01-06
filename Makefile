phpstan:
	php vendor/bin/phpstan analyse -c phpstan.neon src --no-progress

php-cs-fixer:
	php vendor/bin/php-cs-fixer fix

composer-valid:
	composer valid

doctrine:
	php bin/console doctrine:schema:valid --skip-sync

fix: php-cs-fixer

analyse: composer-valid doctrine phpstan
