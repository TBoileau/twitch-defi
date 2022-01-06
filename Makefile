.PHONY: tests install fixtures database prepare tests phpstan php-cs-fixer composer-valid doctrine fix analyse

install:
	cp .env.dist .env.$(env).local
	sed -i -e 's/DATABASE_USER/$(db_user)/' .env.$(env).local
	sed -i -e 's/DATABASE_PASSWORD/$(db_password)/' .env.$(env).local
	sed -i -e 's/ENV/$(env)/' .env.$(env).local
	composer install
	make prepare env=$(env)

fixtures:
	php bin/console doctrine:fixtures:load -n --env=$(env)

database:
	php bin/console doctrine:database:drop --if-exists --force --env=$(env)
	php bin/console doctrine:database:create --env=$(env)
	php bin/console doctrine:query:sql "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));" --env=$(env)
	php bin/console doctrine:schema:update --force --env=$(env)

prepare:
	make database env=$(env)
	make fixtures env=$(env)

tests:
	php bin/phpunit --testdox

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
