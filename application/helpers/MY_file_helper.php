<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('delete_file'))
{
    /**
     * Удаление файла     *
     * Удаляет файл по указаному путь
     *
     * @param string $path - путь + имя файла
     * @return	bool - true, если успешно удален файл, иначе - false
     */
    function delete_file($path)
    {
        if (!is_file($path) || !file_exists($path)) {
            return false;
        }

        return unlink($path);
    }
}