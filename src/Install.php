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
            // 获取目标文件的父目录路径
            $parentDir = base_path(dirname($dest));
            // 如果父目录不存在，则递归创建目录
            if (!is_dir($parentDir)) {
                mkdir($parentDir, 0777, true);  // 0777 权限，允许读写和执行
            }
            // 获取目标文件完整路径
            $destFile = base_path($dest);
            // 如果目标文件已存在，则跳过该文件的复制
            if (file_exists($destFile)) {
                continue;
            }
            // 获取源文件的完整路径
            $sourceFile = __DIR__ . "/$source";
            // 复制目录或文件到目标路径（递归复制）
            copy_dir($sourceFile, $destFile, true);
            // 输出复制成功的目标路径
            echo "Create $dest\r\n";
        }
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