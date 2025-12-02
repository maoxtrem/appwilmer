FROM nginx:alpine

# Elimina configuración por defecto
RUN rm /etc/nginx/conf.d/default.conf \
    && mkdir -p /tmp/nginx \
    && chown -R nginx:nginx /tmp/nginx
# Copia tu configuración personalizada
COPY nginx-frontend.conf /etc/nginx/conf.d

# Copia la carpeta dist
COPY dist/ /usr/share/nginx/html

CMD ["nginx", "-g", "daemon off;"]
