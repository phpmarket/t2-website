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
            if ($pos = strrpos($dest, '/')) {
                $parent_dir = base_path() . '/' . substr($dest, 0, $pos);
                if (!is_dir($parent_dir)) {
                    mkdir($parent_dir, 0777, true);
                }
            }
            copy_dir(__DIR__ . "/$source", base_path() . "/$dest");
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