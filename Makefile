backend-build:
	docker-compose up --build -d backend

backend-dev:
	docker-compose -f docker-compose.yml -f docker-compose-backend.yml up --build -d

frontend-build:
	docker-compose up --build -d frontend

frontend-dev:
	docker-compose -f docker-compose.yml -f docker-compose-frontend.yml up --build -d frontend

mariadb-start:
	docker-compose up --build -d db

mapa-start:
	docker-compose up --build -d maps

proxy-start:
	docker-compose up --build -d proxy

start-dev:
	docker-compose -f docker-compose.yml -f docker-compose-frontend.yml -f docker-compose-backend.yml up --build -d