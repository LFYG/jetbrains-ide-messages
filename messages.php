<?php
error_reporting(E_ALL);


define('DS', '/');

define('MESSAGES_DIR', __DIR__ . DS . 'messages');
define('MESSAGES_EN_DIR', __DIR__ . DS . 'messages_en');
define('MESSAGES_ZH_DIR', __DIR__ . DS . 'messages_zh');


class  Messages
{
    private $globs = [];

    private $messages = [];
    private $properties = [];
    private $properties_array_format = [];
    private $properties_index_format = [];

    /**
     * Messages constructor.
     * @param $basedir
     */
    public function __construct($basedir)
    {
        /**
         * [$files description]
         * @var [type]
         */
        $globs = glob($basedir . DS . '*.properties');
        $globs = array_map(function ($item) {
            return str_replace('\\', '/', $item);
        }, $globs);

        $this->globs = $globs;
    }

    /**
     * getContentAsLines
     * @return array
     */
    public function getContentAsLines()
    {
        if (!empty($this->messages)) {
            $this->messages;
        }

        foreach ($this->globs as $filename) {
            $this->messages[basename($filename)] = file($filename, FILE_IGNORE_NEW_LINES);
        }
        return $this->messages;
    }


    /**
     * getPropertiesAsLines
     * @return array
     */
    public function getPropertiesAsLines()
    {
        if (!empty($this->properties)) {
            return $this->properties;
        }

        $messages = $this->getContentAsLines();
        foreach ($messages as $basename => $lines) {
            $_propertie = $_tmp = [];
            array_push($lines, "");
            foreach ($lines as $k2 => $line) {
                array_push($_tmp, $line);
                if (!isset($lines[$k2 + 1])
                    || preg_match('/^[\w\.\$\-]+\s*=/', $lines[$k2 + 1])
                    || preg_match('/^#/', $lines[$k2 + 1])
                    || empty(trim($lines[$k2 + 1]))
                ) {
                    array_push($_propertie, implode("\n", $_tmp));
                    $_tmp = [];
                }
            }
            $this->properties[$basename] = $_propertie;
        }

        return $this->properties;
    }


    /**
     * getPropertiesAsArray
     * @return array
     */
    public function getPropertiesAsArray()
    {
        if (!empty($this->properties_array_format)) {
            return $this->properties_array_format;
        }

        $properties = $this->getPropertiesAsLines();
        foreach ($properties as $basename => &$items) {
            foreach ($items as $key => &$prop) {
                if (preg_match('/([\w\.\$\-]+)\s*=(.*)/s', $prop, $matches)) {
                    $prop = [$matches[1], $matches[2]];
                }
            }


//            if ($basename == 'StatisticsBundle.properties') {
//                print_r( $items );
//                exit;
//            }
        }

        $this->properties_index_format = $properties;
        return $this->properties_index_format;
    }

    /**
     * getPropertiesAsIndex
     * @return array
     */
    public function getPropertiesAsIndex()
    {
        if (!empty($this->properties_index_format)) {
            return $this->properties_index_format;
        }

        $properties_tmp = array();
        $properties = $this->getPropertiesAsArray();
        foreach ($properties as $basename => $items) {
            foreach ($items as $key => $prop) {
                if (is_array($prop)) {
                    $properties_tmp[$basename][$prop[0]] = $prop[1];
                }
            }
        }

        $this->properties_index_format = $properties_tmp;
        return $this->properties_index_format;
    }


    public function __get($name)
    {
        return isset($this->$name) ? $this->$name : null;
    }

}

/**
 * Messages
 */
$messages_en = new Messages(MESSAGES_EN_DIR);
$messages_zh = new Messages(MESSAGES_ZH_DIR);

$enPropertiesAsArray = $messages_en->getPropertiesAsArray();
$zhPropertiesAsIndex = $messages_zh->getPropertiesAsIndex();


/**
 * 双语格式
 */
foreach ($enPropertiesAsArray as $basename => &$properties) {
    $properties_en = $properties_zh = array();
    foreach ($properties as $key => &$item) {
        if (is_array($item)) {
            $_zh = isset($zhPropertiesAsIndex[$basename][$item[0]]) ?
                $zhPropertiesAsIndex[$basename][$item[0]] : $item[1];
            array_push($item, $_zh);
        }

        $item_en = $item;
        if (is_array($item_en)) {
            $item_en = $item_en[0] . '=' . $item_en[1];
        }
        array_push($properties_en, $item_en);

        $item_zh = $item;
        if (is_array($item_zh)) {
            $item_zh = $item_zh[0] . '=' . $item_zh[2];
        }
        array_push($properties_zh, $item_zh);
    }

    is_dir('messages_en2') or mkdir('messages_en2');
    is_dir('messages_zh2') or mkdir('messages_zh2');
    file_put_contents('messages_en2/' . $basename, implode("\n", $properties_en));
    file_put_contents('messages_zh2/' . $basename, implode("\n", $properties_zh));
}



//print_r($messages->globs);
//print_r($messages->getPropertiesAsLines());



$ss = $enPropertiesAsArray;

//print_r( $ss['ActionsBundle.properties'] );
//print_r( $ss['AnalysisScopeBundle.properties'] );
//print_r( $ss['ApplicationBundle.properties']);

$ss = array_map(function ($item) {
    return count($item);
}, $ss);
print_r($ss);



exit;


