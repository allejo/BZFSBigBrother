VERSION = $(shell dasel -f composer.json -r json '.version' --plain)

dev:
	docker build . \
		--progress=plain \
		--target=development \
		--label="version=$(VERSION)-dev" \
		--tag allejo/bzfs-big-brother:latest

prod:
	docker build . \
		--progress=plain \
		--target=production \
		--label="version=$(VERSION)" \
		--tag allejo/bzfs-big-brother:${VERSION}
