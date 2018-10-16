README
======

![license](https://img.shields.io/packagist/l/bafs/via.svg?style=flat-square)
![PHP 5.6+](https://img.shields.io/badge/PHP-5.4+-brightgreen.svg?style=flat-square)

What is Claudo?
-----------------

Claudo is a PHP semaphore system. Helper for async execute code.

Installation
------------

The best way to install is to use the composer by command:

    composer require newclass/claudo
    composer install

Use example
-------------

    use Claudo\Semaphore;
    use Claudo\SemaphoreBatch;
    
    $semaphoreBatch = new SemaphoreBatch();
    
    $semaphoreBatch->add(new Semaphore('test1', '.'));
    $semaphoreBatch->add(new Semaphore('test2'));
    
    $result = $semaphoreBatch->synchronize(function (Semaphore $semaphore) {
        $var = $semaphore->get('var', null);
        if (!$var) {
            $var = 0;
        }
    
        ++$var;
    
        $semaphore->set('var', $var);
    
        return sprintf('finish: %s, result: %d',$semaphore->getName(),$var);
    });
    
    echo $result;
