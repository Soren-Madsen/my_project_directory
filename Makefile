
start:
	docker-compose up

stop:
	docker-compose down

ssh-app:
	docker exec -ti app bash