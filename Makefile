build:
	docker build . -t nutgram-issue-468-demo:latest

up:
	docker compose up

down:
	docker compose down --remove-orphans