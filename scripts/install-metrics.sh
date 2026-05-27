#!/bin/bash
set -e

kubectl apply -f https://github.com/kubernetes-sigs/metrics-server/releases/latest/download/components.yaml

# Fix for kind (VERY IMPORTANT)
kubectl patch deployment metrics-server -n kube-system \
  --type='json' \
  -p='[
    {"op":"add","path":"/spec/template/spec/containers/0/args/-","value":"--kubelet-insecure-tls"},
    {"op":"add","path":"/spec/template/spec/containers/0/args/-","value":"--kubelet-preferred-address-types=InternalIP"}
  ]'

kubectl rollout status deployment metrics-server -n kube-system