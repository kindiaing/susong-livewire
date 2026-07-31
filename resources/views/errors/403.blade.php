<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - 无权限访问</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center px-6">
        <h1 class="text-6xl font-bold text-gray-300">403</h1>
        <p class="mt-4 text-lg text-gray-600">您没有访问此页面的权限</p>
        <p class="mt-2 text-sm text-gray-400">请联系管理员分配相关权限</p>
        <a href="{{ route('dashboard') }}" class="mt-6 inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 transition">
            返回首页
        </a>
    </div>
</body>
</html>
