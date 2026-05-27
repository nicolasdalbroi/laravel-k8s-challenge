<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🚀 Laravel Kubernetes Deployment (Local)

This project deploys a Laravel application to a local Kubernetes cluster using **Kind**, **Docker**, and **Kustomize**.

## 📦 Prerequisites

- Docker
- kubectl
- Kind

Verify:

docker --version
kubectl version --client
kind version

## ⚙️ 1. Create Kubernetes Cluster (Kind)

./scripts/setup-kind.sh

kubectl get nodes

## 📊 2. Install Metrics Server (Required for HPA)

./scripts/install-metrics.sh

kubectl top nodes

## 🐳 3. Build & Deploy Application

./scripts/deploy-local.sh

## 🔍 4. Verify Deployment

kubectl get pods -n laravel-prod
kubectl get svc -n laravel-prod
kubectl get hpa -n laravel-prod

## 🌐 5. Access Application (Port Forward)

kubectl port-forward svc/laravel-app 8080:9000 -n laravel-prod

## 🧪 6. Test Endpoints

curl http://localhost:8080/api/health
curl http://localhost:8080/api/ready
curl http://localhost:8080/api/info

## 📈 7. Test Auto-Scaling

curl "http://localhost:8080/api/load-test?duration=30&iterations=10000000"

kubectl get hpa -w -n laravel-prod

## 🛠 Troubleshooting

kubectl get apiservices | grep metrics
kubectl describe pod <pod-name> -n laravel-prod
kubectl logs -f deployment/laravel-app -n laravel-prod

## ⚡ Quick Start

./scripts/setup-kind.sh && \
./scripts/install-metrics.sh && \
./scripts/deploy-local.sh
