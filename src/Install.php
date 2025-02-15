<?php

class Install
{
    /**
     * 常量：IS_PLUGIN
     * 用于标识当前类是否为 T2Engine 插件
     * 值为 true，表示是 T2Engine 插件
     */
    const IS_PLUGIN = true;

    /**
     * @var array $pathRelation
     * 路径映射关系
     * 键：源文件路径（相对于当前目录）
     * 值：目标文件路径（相对于项目根目录）
     * 作用：用于在安装时，将源文件复制到目标位置
     */
    protected static array $pathRelation = [
        'config/web.php' => 'config/web.php', // 配置文件
        'web'            => 'web', // web目录
        'public'         => 'public/web', // 静态资源目录
    ];

    /**
     * Install
     *
     * @return void
     */
    public static function install(): void
    {
        static::installByRelation();
    }

    /**
     * Uninstall
     *
     * @return void
     */
    public static function uninstall(): void
    {
        self::uninstallByRelation();
    }

    /**
     * installByRelation
     *
     * @return void
     */
    public static function installByRelation(): void
    {
        foreach (static::$pathRelation as $source => $dest) {
            $parentDir = base_path(dirname($dest));
            if (!is_dir($parentDir)) {
                mkdir($parentDir, 0777, true);
            }
            $destFile = base_path($dest);
            $sourceFile = __DIR__ . "/$source";
            // 如果目标文件已存在，跳过复制，但仍尝试删除源文件
            if (!file_exists($destFile)) {
                // 复制目录或文件到目标路径（递归复制）
                copy_dir($sourceFile, $destFile, true);
                echo "Create $dest\r\n";
            }
            if (is_file($sourceFile) && is_writable($sourceFile)) {
                @unlink($sourceFile);
            } elseif (is_dir($sourceFile)) {
                self::recursiveRemoveDir($sourceFile);
            }
        }
    }

    /**
     * 递归删除目录及其内容
     *
     * @param string $dir 目录路径
     *
     * @return void
     */
    private static function recursiveRemoveDir(string $dir): void
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            if (is_dir($path)) {
                self::recursiveRemoveDir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    /**
     * uninstallByRelation
     *
     * @return void
     */
    public static function uninstallByRelation(): void
    {
        foreach (static::$pathRelation as $dest) {
            $path = base_path() . "/$dest";
            if (!is_dir($path) && !is_file($path)) {
                continue;
            }
            echo "Remove $dest\r\n";
            if (is_file($path) || is_link($path)) {
                unlink($path);
                continue;
            }
            remove_dir($path);
        }
    }
}