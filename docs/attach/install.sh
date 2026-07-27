#!/bin/bash
# ============================================================
# 本地速送服务平台 — Linux 启动/部署脚本
# 使用方法：chmod +x docs/attach/install.sh && ./docs/attach/install.sh
# ============================================================

set -e

echo "========================================="
echo "  本地速送服务平台 — 启动脚本"
echo "========================================="

# 项目根目录（脚本所在目录的上两级）
PROJECT_ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$PROJECT_ROOT"

echo "[1/6] 检查 .env 配置..."
if [ ! -f .env ]; then
    echo "  .env 不存在，从模板复制..."
    cp docs/attach/.env.example .env
    echo "  ⚠ 请修改 .env 中的数据库、Redis等配置后重新运行本脚本"
    exit 1
fi
echo "  .env 已存在"

echo "[2/6] 检查 APP_KEY..."
if grep -q "APP_KEY=" .env && [ -n "$(grep "APP_KEY=" .env | cut -d'=' -f2)" ]; then
    echo "  APP_KEY 已配置"
else
    php artisan key:generate
    echo "  APP_KEY 已生成"
fi

echo "[3/6] 安装后端依赖..."
composer install --no-interaction
echo "  后端依赖安装完成"

echo "[4/6] 执行数据库迁移..."
php artisan migrate --force
echo "  数据库迁移完成"

echo "[5/6] 导入初始化数据..."
# 检查 init.sql 是否已导入（检查 roles 表是否有数据）
ROLE_COUNT=$(php artisan tinker --execute="echo \DB::table('roles')->count();" 2>/dev/null || echo "0")
if [ "$ROLE_COUNT" = "0" ]; then
    DB_NAME=$(grep DB_DATABASE .env | cut -d'=' -f2)
    DB_USER=$(grep DB_USERNAME .env | cut -d'=' -f2)
    mysql -u"$DB_USER" -p "$(grep DB_PASSWORD .env | cut -d'=' -f2)" "$DB_NAME" < docs/attach/init.sql 2>/dev/null || \
    php artisan db:seed --force
    echo "  初始化数据导入完成"
else
    echo "  初始化数据已存在，跳过"
fi

echo "[6/6] 启动后端服务..."
echo "  访问地址: http://localhost:$(grep APP_PORT .env | cut -d'=' -f2 || echo 8000)"
echo "  按 Ctrl+C 停止服务"
echo "========================================="

php artisan serve --host=0.0.0.0 --port=$(grep APP_PORT .env | cut -d'=' -f2 || echo 8000)
