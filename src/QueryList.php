<?php
/**
 * QueryList
 *
 * 一个基于phpQuery的通用列表采集类
 *
 * @author 			Jaeger
 * @email 			JaegerCode@gmail.com
 * @link            https://github.com/jae-jae/QueryList
 * @version         4.0.0
 *
 */

namespace QL;
use phpQuery;
use QL\Dom\Query;
use Tightenco\Collect\Support\Collection;
use Closure;
use QL\Services\MultiRequestService;


/**
 * Class QueryList
 * @package QL
 *
 * @method string getHtml($rel = true)
 * @method QueryList setHtml($html)
 * @method QueryList html($html)
 * @method Dom\Elements find($selector)
 * @method QueryList rules(array $rules)
 * @method QueryList range($range)
 * @method QueryList removeHead()
 * @method QueryList query(Closure $callback = null)
 * @method Collection getData(Closure $callback = null)
 * @method Array queryData(Closure $callback = null)
 * @method QueryList setData(Collection $data)
 * @method QueryList encoding(string $outputEncoding,string $inputEncoding = null)
 * @method QueryList use($plugins,...$opt)
 * @method QueryList pipe(Closure $callback = null)
 */
class QueryList
{
    protected $query;
    protected $kernel;
    protected static $instance = null;

    /**
     * QueryList constructor.
     */
    public function __construct()
    {
        $this->query = new Query($this);
        $this->kernel = (new Kernel($this))->bootstrap();
        Config::getInstance()->bootstrap($this);
    }

    public function __call($name, $arguments)
    {
        if(method_exists($this->query,$name)){
            $result = $this->query->$name(...$arguments);
        }else{
            $result = $this->kernel->getService($name)->call($this,...$arguments);
        }
       return $result;
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = new self();
        return $instance->$name(...$arguments);
    }

    public function __destruct()
    {
        $this->destruct();
    }

    /**
     * Get the QueryList single instance
     *
     * @return QueryList
     */
    public static function getInstance()
    {
        self::$instance || self::$instance = new self();
        return self::$instance;
    }

    /**
     * Get the Config instance
     * @return null|Config
     */
    public static function config()
    {
        return Config::getInstance();
    }

    /**
     * Destruction of resources
     */
    public function destruct()
    {
        unset($this->query);
        unset($this->kernel);
    }

    /**
     * Destroy all documents
     */
    public static function destructDocuments()
    {
        phpQuery::$documents = [];
    }

    /**
     * Bind a custom method to the QueryList object
     *
     * @param string $name Invoking the name
     * @param Closure $provide Called method
     * @return $this
     */
    public function bind(string $name,Closure $provide)
    {
        $this->kernel->bind($name,$provide);
        return $this;
    }

}