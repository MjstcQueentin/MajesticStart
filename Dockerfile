FROM php:8.5-apache-trixie
WORKDIR /var/www/html

# Copy application files
COPY *.php .
COPY .env .
COPY application/ ./application/
COPY public/ ./public/

# Create projects directory
RUN mkdir writable
RUN mkdir writable/logs
RUN mkdir writable/rsscache
VOLUME /var/www/html/writable

# Enable writing permissions for the web server user
RUN chown -R www-data /var/www/html
