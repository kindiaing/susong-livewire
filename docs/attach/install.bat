@echo off
chcp 65001 >nul
REM ============================================================
REM 本地速送服务平台 — Windows 启动脚本
REM 使用方法：双击运行或在项目根目录执行 docs\attach\install.bat
REM ============================================================

echo =========================================
echo   本地速送服务平台 — 启动脚本 (Windows)
echo =========================================

REM 切换到项目根目录
cd /d "%~dp0..\.."

echo [1/6] 检查 .env 配置...
if not exist .env (
    echo   .env 不存在，从模板复制...
    copy docs\attach\.env.example .env
    echo   ⚠ 请修改 .env 中的数据库、Redis等配置后重新运行本脚本
    pause
    exit /b 1
)
echo   .env 已存在

echo [2/6] 检查 APP_KEY...
findstr /C:"APP_KEY=" .env >nul
if errorlevel 1 (
    php artisan key:generate
    echo   APP_KEY 已生成
) else (
    echo   APP_KEY 已配置
)

echo [3/6] 安装后端依赖...
call composer install --no-interaction
echo   后端依赖安装完成

echo [4/6] 执行数据库迁移...
php artisan migrate --force
echo   数据库迁移完成

echo [5/6] 导入初始化数据...
echo   请手动执行: mysql -u账号 -p库名 ^< docs\attach\init.sql
echo   或执行: php artisan db:seed --force
echo   （如已导入可跳过）

echo [6/6] 启动后端服务...
echo   访问地址: http://localhost:8000
echo   按 Ctrl+C 停止服务
echo =========================================

php artisan serve --host=0.0.0.0 --port=8000

pause
