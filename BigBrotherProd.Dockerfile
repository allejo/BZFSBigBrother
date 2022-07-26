FROM base_image

LABEL name="BZFS Big Brother (DEV)"
LABEL description="A web application to store and track information about players and their connections"
LABEL version="X.X.X"

RUN useradd -m -u 1001 BigBrotherUser
USER BigBrotherUser

ENTRYPOINT ["apache2-foreground"]
