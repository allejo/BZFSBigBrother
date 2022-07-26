VERSION = $(shell cat composer.json | dasel -r json '.version')

define generate_dockerfile
	$(eval tagver = $(if $(3),$(3),$(2)))

	cat BigBrotherBase.Dockerfile BigBrother$(1).Dockerfile > Dockerfile
	sed -i.bak 's/X.X.X/$(2)/' Dockerfile
	docker build . \
		$(if $(filter $(1),Prod),--no-cache,) \
		--secret id=composer_gh_pat,env=COMPOSER_GH_PAT \
		--progress=plain \
		--tag allejo/bzfs-big-brother:${tagver}
	rm Dockerfile
	rm Dockerfile.bak
endef

dev: VERSION := $(VERSION)-dev
dev:
	$(call generate_dockerfile,Dev,$(VERSION),latest)

prod:
	$(call generate_dockerfile,Prod,$(VERSION))

clean:
	rm Dockerfile
	rm Dockerfile.bak
