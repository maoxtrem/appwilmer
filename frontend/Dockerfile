FROM nginx:alpine

# Instalar Node.js y npm en Alpine
RUN apk add --no-cache nodejs npm

# Elimina configuración por defecto
RUN rm /etc/nginx/conf.d/default.conf \
    && mkdir -p /tmp/nginx \
    && chown -R nginx:nginx /tmp/nginx

# Copia tu configuración personalizada
COPY ./frontend/nginx-frontend.conf /etc/nginx/conf.d
COPY ./frontend/dist /usr/share/nginx/html

CMD ["nginx", "-g", "daemon off;"]
