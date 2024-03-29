exec:
	docker exec -it symfony_php ${cmd}

bash:
	make exec cmd=bash

composer:
	make exec cmd='composer ${cmd}'

require:
	make composer cmd='require ${cmd}'

install:
	make composer cmd='install'

console:
	make exec cmd='./bin/console ${cmd}'

cache-clear:
	make exec cmd='rm -rf var/cache/*/*'

cache-warmup: cache-clear
	make console cmd="cache:warmup"

phpunit:
	make exec cmd='./bin/phpunit ${cmd}'

phpunit-coverage:
	make exec cmd='./bin/phpunit --coverage-text ${cmd}'

csfixer:
	make exec cmd='./vendor/bin/php-cs-fixer fix src/ --rules=@Symfony,@PSR1,@PSR2,@PSR12'

phpcs:
	make exec cmd='./vendor/bin/phpcs --standard=Symfony src/'

phpcbf:
	make exec cmd='./vendor/bin/phpcbf --standard=Symfony src/'

phpstan:
	make exec cmd='./vendor/bin/phpstan analyse src tests'

psalm:
	make exec cmd='./vendor/bin/psalm src/ tests/'

migrate:
	make console cmd='doctrine:migrations:migrate'

generate-migration:
	make console cmd='doctrine:migrations:diff'

fixtures-load:
	make console cmd='doctrine:fixtures:load'