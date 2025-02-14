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
    protected static array $pathRelation = array(
        'web'    => 'web',
        'public' => 'public/web',
    );

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
            if (file_exists($destFile)) {
                continue;
            }
            $sourceFile = __DIR__ . "/$source";
            copy_dir($sourceFile, $destFile, true);
            echo "Create $dest\r\n";

            // === 调试输出路径和权限信息 ===
            echo "Source File Path: $sourceFile\r\n";
            echo "Is Writable: " . (is_writable($sourceFile) ? 'Yes' : 'No') . "\r\n";

            // === 复制成功后删除源文件或目录 ===
            if (is_file($sourceFile) && is_writable($sourceFile)) {
                echo "Deleting file: $sourceFile\r\n";
                @unlink($sourceFile);
            } elseif (is_dir($sourceFile)) {
                echo "Deleting directory: $sourceFile\r\n";
                self::recursiveRemoveDir($sourceFile);  // 使用递归删除目录
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
                echo "Deleting file: $path\r\n";
                @unlink($path);
            }
        }
        echo "Removing directory: $dir\r\n";
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