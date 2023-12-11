fix:
	php-cs-fixer fix ./src/


sniff:
	phpcs --standard=PSR12 ./src/

analyse:
	phpstan analyse ./src/


check: fix sniff analyse
