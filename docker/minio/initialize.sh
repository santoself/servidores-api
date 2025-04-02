#!/bin/sh
set -e

# Export variables para garantir que estejam disponíveis
export MINIO_ROOT_USER=${MINIO_ROOT_USER:-minioadmin}
export MINIO_ROOT_PASSWORD=${MINIO_ROOT_PASSWORD:-minioadmin}
export MINIO_BUCKET=${MINIO_BUCKET:-servidores}

echo "Aguardando MinIO ficar disponível..."

# Espera até que o MinIO esteja respondendo
until mc alias set minio http://minio:9000 ${MINIO_ROOT_USER} ${MINIO_ROOT_PASSWORD} 2>/dev/null; do
  echo 'MinIO não está disponível, aguardando...'
  sleep 1
done

echo "MinIO está disponível, verificando bucket..."

# Cria o bucket se não existir
if ! mc ls minio | grep -q ${MINIO_BUCKET}; then
  echo "Criando bucket ${MINIO_BUCKET}..."
  mc mb minio/${MINIO_BUCKET} --ignore-existing
  mc policy set public minio/${MINIO_BUCKET}
  echo "Bucket ${MINIO_BUCKET} criado e configurado como público."
else
  echo "Bucket ${MINIO_BUCKET} já existe."
fi

echo "Configuração do MinIO concluída com sucesso."