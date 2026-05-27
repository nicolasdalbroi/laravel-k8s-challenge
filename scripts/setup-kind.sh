#!/bin/bash
set -e

cat <<EOF | kind create cluster --name laravel-prod --config=-
kind: Cluster
apiVersion: kind.x-k8s.io/v1alpha4
nodes:
  - role: control-plane
    kubeadmConfigPatches:
      - |
        kind: InitConfiguration
        nodeRegistration:
          kubeletExtraArgs:
            node-labels: "ingress-ready=true"
  - role: worker
  - role: worker
EOF

kubectl cluster-info