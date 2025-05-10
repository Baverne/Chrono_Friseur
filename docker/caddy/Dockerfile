# Stage 1: Build assets
FROM debian AS vite

# Install Node.js and build tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Set working directory
WORKDIR /srv

# Copy Laravel application code
COPY . /srv

# Install Node.js dependencies and build assets
RUN npm install && npm run build

# Stage 2: Caddy production image
FROM caddy:2-alpine

# Copy custom Caddy configuration
# -----------------------------------------------------------
# Replace the default Caddy configuration with our custom one
# that is optimized for serving a Laravel application.
# -----------------------------------------------------------
COPY ./docker/caddy/Caddyfile /etc/caddy/Caddyfile

# Copy Laravel's public assets from the vite stage
# -----------------------------------------------------------
# We only need the 'public' directory from our Laravel app.
# -----------------------------------------------------------
COPY --from=vite /srv/public /srv/public

# Set the working directory to the srv folder
WORKDIR /srv

EXPOSE 80
CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
