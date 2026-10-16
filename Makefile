.DEFAULT_GOAL := up

up: generate-certs
	docker compose up -d

generate-certs:
	@if [ ! -f docker/nginx/certs/ta0926.local.pem ] || [ ! -f docker/nginx/certs/ta0926.local-key.pem ]; then \
		mkdir -p docker/nginx/certs; \
		if command -v mkcert >/dev/null 2>&1; then \
			mkcert -cert-file docker/nginx/certs/ta0926.local.pem -key-file docker/nginx/certs/ta0926.local-key.pem ta0926.local; \
		else \
			openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout docker/nginx/certs/ta0926.local-key.pem -out docker/nginx/certs/ta0926.local.pem -subj "/CN=ta0926.local" -addext "subjectAltName = DNS:ta0926.local"; \
		fi \
	fi

down:
	docker compose down --remove-orphans

db-seed:
	docker compose run --rm php php bin/seed.php

db-clear:
	docker compose run --rm php php bin/dbreset.php