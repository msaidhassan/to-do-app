FROM node:20-alpine

# Create and switch to a non-root user first
RUN adduser -D appuser
WORKDIR /var/www/frontend
RUN chown -R appuser:appuser /var/www/frontend

# Switch to the non-root user before running npm install
USER appuser

# Copy package.json and package-lock.json first for better caching
COPY --chown=appuser:appuser package*.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application
# Note: You would typically do this in your actual Dockerfile,
# but in your case, you're mounting the volume instead
# COPY --chown=appuser:appuser . .

EXPOSE 3000

CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--port", "3000"]