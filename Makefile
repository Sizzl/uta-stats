up:
	docker-compose up -d

down:
	docker-compose rm -vsf
	docker-compose down -v --remove-orphans

build:
	make down
	docker-compose build
	make up

jump-in-db:
	docker-compose run db /bin/bash	

logs-db:
	docker-compose logs -f db

db-backup:
	docker exec uta-stats-db /usr/bin/mysqldump -u root --password=secret utassault_pug | gzip > support/docker/db/utassault_pug.sql.gz