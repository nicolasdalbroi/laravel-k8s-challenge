#!/bin/bash
set -e

# Get the directory where the script lives
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Go to repo root (one level up from /scripts)
REPO_ROOT="$(dirname "$SCRIPT_DIR")"

IMAGE=laravel-app:local
CLUSTER_NAME=laravel-prod

echo "📁 Using repo root: $REPO_ROOT"

echo "🔨 Building Docker image..."
docker build -t $IMAGE "$REPO_ROOT"

echo "📦 Loading image into kind..."
kind load docker-image $IMAGE --name $CLUSTER_NAME

echo "🚀 Deploying with kustomize..."
kubectl apply -k "$REPO_ROOT/k8s/overlays/local"

echo "⏳ Waiting for deployment..."
kubectl rollout status deployment/laravel-app -n laravel-prod

echo "✅ Done"