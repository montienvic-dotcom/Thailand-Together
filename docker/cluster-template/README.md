# Cluster Template

## Quick Start

1. Copy this directory for a new cluster:
   ```bash
   cp -r cluster-template clusters/chiangmai
   cd clusters/chiangmai
   ```

2. Create `.env` from template:
   ```bash
   cp .env.example .env
   # Edit .env: set CLUSTER_CODE, APP_PORT, DB_PORT, etc.
   ```

3. Start the cluster:
   ```bash
   docker compose -f docker-compose.cluster.yml up -d
   ```

4. Run migrations:
   ```bash
   docker exec tt-app-chiangmai php artisan migrate
   docker exec tt-app-chiangmai php artisan db:seed
   ```

## Port Allocation Convention

| Cluster    | APP_PORT | DB_PORT |
|------------|----------|---------|
| global     | 8080     | 3306    |
| pattaya    | 8081     | 3307    |
| chiangmai  | 8082     | 3308    |
| danang     | 8083     | 3309    |

## Network

Each cluster has its own network (`tt-cluster-{code}`) and connects to the global network (`tt-network-global`) for cross-cluster communication.
