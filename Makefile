include ${PWD}/.env

USER:=$(shell id -u)
GROUP:=$(shell id -g)

build:
	make down && sudo chown -R ${USER}:${USER} postgres-data && docker-compose build && docker-compose up -d && make exec
up:
	docker-compose up -d && make log
stop:
	docker-compose stop
exec:
	docker-compose exec app bash
log:
	docker-compose logs -f app