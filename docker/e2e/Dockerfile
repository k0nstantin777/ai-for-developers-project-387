FROM mcr.microsoft.com/playwright:latest

WORKDIR /app
RUN npm init -y && npm install @playwright/test@1.46.1 playwright@1.46.1

COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
