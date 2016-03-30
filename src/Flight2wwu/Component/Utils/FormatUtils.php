<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2015/9/21
 * Time: 13:26
 */

namespace Flight2wwu\Component\Utils;

class FormatUtils
{

    /**
     * @param int $length
     * @return string
     */
    public static function randStr($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;
        for($i = 0; $i < $length; $i++){
            $str .= $strPol[rand(0,$max)];
        }
        return $str;
    }

    /**
     * Format path without last separator.
     *
     * @param string $path
     * @return string
     */
    public static function formatPath($path)
    {
        return (trim($path) && $path != DIRECTORY_SEPARATOR) ? rtrim(trim($path), DIRECTORY_SEPARATOR) : '';
    }

    /**
     * Format path with last separator.
     *
     * @param string $path
     * @return string
     */
    public static function formatWebPath($path)
    {
        return (trim($path) && $path != '/') ? '/' . (trim(trim($path), '/') . '/') : '/';
    }

    /**
     * Format extension with first dot.
     *
     * @param string $extension
     * @return string
     */
    public static function formatExtension($extension)
    {
        $extension = preg_replace('/[^\w\.]/', '', trim(trim($extension), '.'));
        return '.' . $extension;
    }

    /**
     * Skip array null values and skip keys.
     *
     * @param array $arr
     * @param array $skipKeys
     * @return array
     */
    public static function formatArray(array $arr, $skipKeys = [])
    {
        $out = [];
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                continue;
            }
            if ($skipKeys && is_array($skipKeys)) {
                if (in_array($k, $skipKeys)) {
                    continue;
                }
            }
            $out[$k] = $v;
        }
        return $out;
    }

    /**
     * Format array to string.
     *
     * @param array $arr
     * @return string
     */
    public static function arrayToString(array $arr)
    {
        $str = [];
        foreach ($arr as $k => $v) {
            array_push($str, "$k = $v");
        }
        return implode(',', $str);
    }

    /**
     * Format time with format.
     *
     * @param string $datetime
     * @param string $format
     * @return string
     */
    public static function formatTime($datetime = null, $format = 'Y-m-d H:i:s')
    {
        if ($datetime) {
            $dt = new \DateTime($datetime);
        } else {
            $dt = new \DateTime();
        }
        return $dt->format($format);
    }

    /**
     * @param $val
     * @return string
     */
    public static function getExcelDate($val)
    {
        if (!$val) {
            return null;
        }
        $jd = GregorianToJD(1, 1, 1970);
        $gregorian = JDToGregorian($jd + intval($val) - 25569);
        $darr = date_parse_from_format('m/d/Y', $gregorian);
        $d = $darr['year'] . '/' . $darr['month'] . '/' . $darr['day'];
        return $d;
    }

    /**
     * Format time and translate in array by fields
     *
     * @param array $data
     * @param array $transFields
     * @param array|string $dateFields
     * @param string $dateFormat
     * @return array
     */
    public static function formatTransArray(array &$data, $transFields = [], $dateFields = '/^\w+_at$/', $dateFormat = 'Y-m-d H:i:s')
    {
        $re = array_walk_recursive($data, function(&$item, $key) use ($transFields, $dateFields, $dateFormat) {
            if (is_null($item)) {
                return;
            }
            //format date
            if (is_array($dateFields)) {
                if (in_array($key, $dateFields)) {
                    $item = FormatUtils::formatTime($item, $dateFormat);
                }
            } else {
                if (preg_match($dateFields, $key)) {
                    $item = FormatUtils::formatTime($item, $dateFormat);
                }
            }
            //trans
            if ($transFields) {
                if (in_array($key, $transFields)) {
                    $item = T($item);
                }
            } else {
                $item = T($item);
            }
        });
        return $data;
    }

    /**
     * Format head array to format header
     *
     * @param array $head
     * @return array
     */
    public static function formatHead(array $head) {
        $out = [];
        foreach ($head as $h) {
            array_push($out, ['field'=>$h, 'title'=>T($h)]);
        }
        return $out;
    }

    /**
     * Same as pathinfo but support chinese for < php5.6.
     *
     * @param $filepath
     * @return array
     */
    public static function pathInfo($filepath)
    {
        $p = DIRECTORY_SEPARATOR;
        $f = rtrim($filepath, $p);
        $path_parts = array();
        $path_parts['dirname'] = (strrpos($f, $p) === false ? '.' : substr($f, 0, strrpos($f, $p)));
        if (strrchr($path_parts['dirname'], ':') == ':' || !$path_parts['dirname']) {
            $path_parts['dirname'] .= $p;
        }
        $path_parts['basename'] = ltrim(substr($f, strrpos($f, $p)), $p);
        if (strrchr($filepath, '.') !== false) {
            $path_parts['extension'] = substr(strrchr($f, '.'), 1);
            $path_parts['filename'] = ltrim(substr($path_parts['basename'], 0, strrpos($path_parts['basename'], '.')), $p);
        } else {
            $path_parts['filename'] = $path_parts['basename'];
        }
        return $path_parts;
    }
} 